<?php
/**
 * The filter builder screen.
 *
 * A filter preset is a set of panels, not a document, so the post editor around
 * it - title box, editor, publish box, screen options - is chrome nobody uses.
 * The editor is redirected here and this screen owns the whole page, the same
 * arrangement the floating menu builder uses.
 *
 * The panels themselves are untouched: this renders the existing settings
 * metabox, so the tabs, fields and styling are exactly what they were.
 */

defined('ABSPATH') || die();

class Super_Product_Filter_Builder {

    const PAGE_SLUG = 'swpf-filter-builder';
    const POST_TYPE = 'swpf-product-filter';
    const NONCE_ACTION = 'swpf_builder_save';
    const NONCE_NAME = 'swpf_builder_nonce';

    public function __construct() {
        /* After the Settings entry, so it lands under the same parent. */
        add_action('admin_menu', array($this, 'add_page'), 21);
        add_action('admin_head', array($this, 'hide_page'));

        add_action('load-post.php', array($this, 'redirect_edit'));
        add_action('load-post-new.php', array($this, 'redirect_new'));

        add_action('admin_post_swpf_builder_save', array($this, 'handle_save'));
        add_action('wp_ajax_swpf_builder_save', array($this, 'handle_ajax_save'));

        add_filter('parent_file', array($this, 'parent_file'));
        add_filter('submenu_file', array($this, 'submenu_file'));
        add_filter('admin_body_class', array($this, 'body_class'));

        /* Late, so the builder stylesheet is printed after the shared admin one
           and wins where the two describe the same element. */
        add_action('admin_enqueue_scripts', array($this, 'scripts'), 20);
    }

    /**
     * The builder URL, for a preset or for a new one.
     *
     * @param  int  $post_id
     * @return string
     */
    public static function url($post_id = 0) {
        $args = array('page' => self::PAGE_SLUG);

        if ($post_id) {
            $args['filter'] = intval($post_id);
        }

        return add_query_arg($args, admin_url('admin.php'));
    }

    /**
     * The preset list URL.
     *
     * @return string
     */
    public static function list_url() {
        return admin_url('edit.php?post_type=' . self::POST_TYPE);
    }

    /**
     * Whether the screen being rendered is the builder.
     *
     * @return bool
     */
    public static function is_builder() {
        return is_admin() && isset($_GET['page']) && self::PAGE_SLUG === $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    }

    public function add_page() {
        add_submenu_page(
            'edit.php?post_type=' . self::POST_TYPE,
            esc_html__('Edit Filter', 'super-product-filter'),
            esc_html__('Edit Filter', 'super-product-filter'),
            'manage_options',
            self::PAGE_SLUG,
            array($this, 'render')
        );
    }

    /**
     * Keeps the builder out of the menu.
     *
     * It has to be registered to be reachable, but it edits one preset, so it
     * has no place in a list of destinations.
     */
    public function hide_page() {
        remove_submenu_page('edit.php?post_type=' . self::POST_TYPE, self::PAGE_SLUG);
    }

    /**
     * Sends the post editor to the builder.
     */
    public function redirect_edit() {
        $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if (!$post_id || self::POST_TYPE !== get_post_type($post_id)) {
            return;
        }

        /* A user who cannot edit it should meet the editor's own message
           rather than a builder that will not save. */
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        wp_safe_redirect(self::url($post_id));
        exit;
    }

    /**
     * Sends Add New to the builder.
     */
    public function redirect_new() {
        $type = isset($_GET['post_type']) ? sanitize_key(wp_unslash($_GET['post_type'])) : 'post'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if (self::POST_TYPE !== $type || !current_user_can('manage_options')) {
            return;
        }

        wp_safe_redirect(self::url());
        exit;
    }

    /**
     * Highlights the preset list rather than the hidden builder entry.
     */
    public function parent_file($parent_file) {
        if (self::is_builder()) {
            return 'edit.php?post_type=' . self::POST_TYPE;
        }

        return $parent_file;
    }

    public function submenu_file($submenu_file) {
        if (self::is_builder()) {
            return 'edit.php?post_type=' . self::POST_TYPE;
        }

        return $submenu_file;
    }

    public function body_class($classes) {
        if (self::is_builder()) {
            $classes .= ' swpf-builder-screen';
        }

        return $classes;
    }

    /**
     * The builder needs the same assets the post editor gave the panels.
     */
    public function scripts() {
        if (!self::is_builder()) {
            return;
        }

        /* The frame is meant to load after the shared admin stylesheet, but that
           one is enqueued behind a screen check. Naming it as a dependency it
           does not always have would raise a notice, so it is only declared
           when it is really there. */
        $deps = wp_style_is('super-product-filter', 'registered') ? array('super-product-filter') : array();

        wp_enqueue_style(
            'swpf-builder',
            SWPF_URL . 'admin/css/builder.css',
            $deps,
            SWPF_VERSION
        );

        wp_enqueue_script(
            'swpf-builder',
            SWPF_URL . 'admin/js/builder.js',
            array('jquery'),
            SWPF_VERSION,
            true
        );

        wp_localize_script('swpf-builder', 'swpfBuilder', array(
            'ajaxurl' => esc_url_raw(admin_url('admin-ajax.php')),
            'saved' => esc_html__('Saved', 'super-product-filter'),
            'unsaved' => esc_html__('Unsaved changes', 'super-product-filter'),
            'saving' => esc_html__('Saving', 'super-product-filter'),
            'unsavedNew' => esc_html__('Not saved yet', 'super-product-filter'),
            'failed' => esc_html__('Could not save. Please try again.', 'super-product-filter'),
            'confirmLeave' => esc_html__('This filter has unsaved changes.', 'super-product-filter'),
            'trashLabel' => esc_html__('Move to Trash', 'super-product-filter'),
        ));
    }

    /**
     * The preset being edited, creating an unsaved draft for a new one.
     *
     * @return WP_Post|null
     */
    protected function current_post() {
        $post_id = isset($_GET['filter']) ? absint($_GET['filter']) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ($post_id) {
            $post = get_post($post_id);

            return ($post && self::POST_TYPE === $post->post_type) ? $post : null;
        }

        /* Nothing is written until Save, so a new preset is a post object that
           exists only for this request. The panels read defaults from it. */
        return new WP_Post((object) array(
            'ID' => 0,
            'post_title' => '',
            'post_type' => self::POST_TYPE,
            'post_status' => 'draft',
        ));
    }

    /**
     * Renders the builder.
     */
    public function render() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to edit filters.', 'super-product-filter'));
        }

        $post = $this->current_post();

        if (!$post) {
            wp_die(esc_html__('That filter no longer exists.', 'super-product-filter'));
        }

        /* The panels read the preset from the global, exactly as they did
           inside the post editor. */
        $GLOBALS['post'] = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        setup_postdata($post);

        $is_new = !$post->ID;
        $saved = isset($_GET['saved']) ? absint($_GET['saved']) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        ?>
        <div class="wrap swpf-builder-wrap">
            <h1 class="screen-reader-text"><?php
                echo $is_new
                    ? esc_html__('Add New Filter', 'super-product-filter')
                    : esc_html(sprintf(
                        /* translators: %s: the filter being edited. */
                        __('Edit Filter: %s', 'super-product-filter'),
                        $post->post_title
                    ));
            ?></h1>
            <hr class="wp-header-end"/>

            <?php if ($saved) { ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Filter saved.', 'super-product-filter'); ?></p>
                </div>
            <?php } ?>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="swpf-builder-form" class="swpf-builder">
                <input type="hidden" name="action" value="swpf_builder_save"/>
                <input type="hidden" name="filter" value="<?php echo esc_attr($post->ID); ?>"/>
                <?php wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME); ?>

                <div class="swpf-builder-topbar">
                    <a class="swpf-topbar-back" href="<?php echo esc_url(self::list_url()); ?>">
                        <span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span>
                        <?php esc_html_e('Filters', 'super-product-filter'); ?>
                    </a>

                    <?php
                    /* Marks the bar as this plugin's rather than another set of
                       fields in a page of admin screens that all look alike. */
                    ?>
                    <span class="swpf-topbar-mark" aria-hidden="true">
                        <span class="dashicons dashicons-filter"></span>
                    </span>

                    <label class="swpf-topbar-name">
                        <span class="screen-reader-text"><?php esc_html_e('Filter name', 'super-product-filter'); ?></span>
                        <input type="text" name="filter_title"
                            value="<?php echo esc_attr($post->post_title); ?>"
                            placeholder="<?php esc_attr_e('Filter name', 'super-product-filter'); ?>" required/>
                    </label>

                    <?php
                    /* Only meaningful with the background save watching the
                       form, so it says what it knows on arrival and hands over
                       to the script from there. */
                    ?>
                    <span class="swpf-topbar-state" aria-live="polite"
                        data-state="<?php echo $is_new ? 'new' : 'saved'; ?>"><?php
                        echo $is_new
                            ? esc_html__('Not saved yet', 'super-product-filter')
                            : esc_html__('Saved', 'super-product-filter');
                    ?></span>

                    <div class="swpf-topbar-actions">
                        <?php
                        $trash = $post->ID ? get_delete_post_link($post->ID) : '';

                        if ($trash) {
                            /* post.php sends the browser back where it came from,
                               which here is a builder for a preset that no longer
                               exists. The list is the only place worth landing. */
                            $trash = add_query_arg('_wp_http_referer', rawurlencode(self::list_url()), $trash);
                            ?>
                            <a class="swpf-topbar-trash" href="<?php echo esc_url($trash); ?>">
                                <?php esc_html_e('Move to Trash', 'super-product-filter'); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="swpf-builder-body">
                    <?php
                    /* The panels' own switcher, sitting on top of the panels it
                       moves between so the open tab joins the settings below
                       it. Separate from the name above, which is its own bar. */
                    ?>
                    <div class="swpf-builder-sections">
                        <?php Super_Product_Filter_Metabox::render_settings_nav(); ?>
                    </div>
                    <?php
                    /* The settings panels, unchanged - same tabs, same fields,
                       same styling as the metabox they came from. Saving is the
                       panels' own sticky footer, so the builder adds none of
                       its own. */
                    Super_Product_Filter_Metabox::render_settings_panels();
                    ?>
                </div>

            </form>
        </div>
        <?php
        wp_reset_postdata();
    }

    /**
     * Writes the preset and its settings.
     *
     * The post editor used to own this: it created the post and fired save_post,
     * which the metabox listened for. The builder posts to its own endpoint, so
     * neither happens and both steps are done here. Shared by the two callers
     * below so the background save and the plain form post behind it cannot
     * drift apart.
     *
     * @return int|WP_Error  The saved preset, or why it could not be saved.
     */
    protected function save_filter() {
        if (!current_user_can('manage_options')) {
            return new WP_Error('swpf_forbidden', esc_html__('You are not allowed to edit filters.', 'super-product-filter'));
        }

        $post_id = isset($_POST['filter']) ? absint($_POST['filter']) : 0;
        $title = isset($_POST['filter_title']) ? sanitize_text_field(wp_unslash($_POST['filter_title'])) : '';

        if ('' === trim($title)) {
            $title = esc_html__('Untitled Filter', 'super-product-filter');
        }

        if ($post_id) {
            if (self::POST_TYPE !== get_post_type($post_id) || !current_user_can('edit_post', $post_id)) {
                return new WP_Error('swpf_forbidden', esc_html__('You are not allowed to edit this filter.', 'super-product-filter'));
            }

            $result = wp_update_post(array(
                'ID' => $post_id,
                'post_title' => $title,
            ), true);
        } else {
            $result = wp_insert_post(array(
                'post_title' => $title,
                'post_type' => self::POST_TYPE,
                'post_status' => 'publish',
            ), true);
        }

        if (is_wp_error($result)) {
            return $result;
        }

        $post_id = intval($result);

        /* Same routine the metabox ran on save_post, and the panels still print
           the nonce it checks for. */
        $metabox = new Super_Product_Filter_Metabox();
        $metabox->save_metabox_settings($post_id);

        return $post_id;
    }

    /**
     * Saves in the background and reports back.
     *
     * What the browser needs afterwards is everything a preset only has once it
     * has been written: the id to keep editing, the address to sit at, and a
     * trash link. A new preset was created from a page that had none of them.
     */
    public function handle_ajax_save() {
        check_ajax_referer(self::NONCE_ACTION, self::NONCE_NAME);

        $post_id = $this->save_filter();

        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => $post_id->get_error_message()));
        }

        $trash = get_delete_post_link($post_id);

        if ($trash) {
            /* post.php sends the browser back where it came from, which here is
               a builder for a preset that no longer exists. */
            $trash = add_query_arg('_wp_http_referer', rawurlencode(self::list_url()), $trash);
        }

        wp_send_json_success(array(
            'id' => $post_id,
            'url' => self::url($post_id),
            'trash' => $trash ? $trash : '',
            'message' => esc_html__('Filter saved.', 'super-product-filter'),
        ));
    }

    /**
     * Saves and returns to the builder.
     *
     * The form posts here on its own when the background save is unavailable,
     * so the builder still works with no scripting at all.
     */
    public function handle_save() {
        check_admin_referer(self::NONCE_ACTION, self::NONCE_NAME);

        $post_id = $this->save_filter();

        if (is_wp_error($post_id)) {
            wp_die(esc_html($post_id->get_error_message()));
        }

        wp_safe_redirect(add_query_arg('saved', 1, self::url($post_id)));
        exit;
    }
}

new Super_Product_Filter_Builder();
