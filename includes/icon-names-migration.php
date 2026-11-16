<?php
/**
 * One-time rename of saved Elegant Icons and Essential Icons classes.
 *
 * Elegant Icons are now written with the egi- prefix (arrow_up is egi-arrow-up) and
 * Essential Icons with the esi- prefix (essentialicon-add is esi-add), the same in
 * every HashThemes theme and plugin. Icons picked before the change are saved under
 * the old names, so they are renamed once, wherever Super Product Filter keeps them.
 *
 * Only exact icon names change (each old name draws the same glyph as the new one),
 * and only in data that belongs to Super Product Filter.
 *
 * @package Super_Product_Filter
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('spf_old_icon_map')) {

    // Old Elegant Icons name => egi- name.
    function spf_old_icon_map() {
        static $map = null;

        if (null === $map) {
            $map = array();
            // 360 Elegant Icons names as saved before the egi- prefix, dot-joined.
            foreach (explode('.', 'arrow-up-down.arrow_back.arrow_carrot-2down.arrow_carrot-2down_alt2.arrow_carrot-2dwnn_alt.arrow_carrot-2left.arrow_carrot-2left_alt.arrow_carrot-2left_alt2.arrow_carrot-2right.arrow_carrot-2right_alt.arrow_carrot-2right_alt2.arrow_carrot-2up.arrow_carrot-2up_alt.arrow_carrot-2up_alt2.arrow_carrot-down.arrow_carrot-down_alt.arrow_carrot-down_alt2.arrow_carrot-left.arrow_carrot-left_alt.arrow_carrot-left_alt2.arrow_carrot-right.arrow_carrot-right_alt.arrow_carrot-right_alt2.arrow_carrot-up.arrow_carrot-up_alt2.arrow_carrot_up_alt.arrow_condense.arrow_condense_alt.arrow_down.arrow_down_alt.arrow_expand.arrow_expand_alt.arrow_expand_alt2.arrow_expand_alt3.arrow_left.arrow_left-down.arrow_left-down_alt.arrow_left-right.arrow_left-right_alt.arrow_left-up.arrow_left-up_alt.arrow_left_alt.arrow_move.arrow_right.arrow_right-down.arrow_right-down_alt.arrow_right-up.arrow_right-up_alt.arrow_right_alt.arrow_triangle-down.arrow_triangle-down_alt.arrow_triangle-down_alt2.arrow_triangle-left.arrow_triangle-left_alt.arrow_triangle-left_alt2.arrow_triangle-right.arrow_triangle-right_alt.arrow_triangle-right_alt2.arrow_triangle-up.arrow_triangle-up_alt.arrow_triangle-up_alt2.arrow_up.arrow_up-down_alt.arrow_up_alt.icon_adjust-horiz.icon_adjust-vert.icon_archive.icon_archive_alt.icon_bag.icon_bag_alt.icon_balance.icon_blocked.icon_book.icon_book_alt.icon_box-checked.icon_box-empty.icon_box-selected.icon_briefcase.icon_briefcase_alt.icon_building.icon_building_alt.icon_calculator_alt.icon_calendar.icon_calulator.icon_camera.icon_camera_alt.icon_cart.icon_cart_alt.icon_chat.icon_chat_alt.icon_check.icon_check_alt.icon_check_alt2.icon_circle-empty.icon_circle-slelected.icon_clipboard.icon_clock.icon_clock_alt.icon_close.icon_close_alt.icon_close_alt2.icon_cloud.icon_cloud-download.icon_cloud-download_alt.icon_cloud-upload.icon_cloud-upload_alt.icon_cloud_alt.icon_cog.icon_cogs.icon_comment.icon_comment_alt.icon_compass.icon_compass_alt.icon_cone.icon_cone_alt.icon_contacts.icon_contacts_alt.icon_creditcard.icon_currency.icon_currency_alt.icon_cursor.icon_cursor_alt.icon_datareport.icon_datareport_alt.icon_desktop.icon_dislike.icon_dislike_alt.icon_document.icon_document_alt.icon_documents.icon_documents_alt.icon_download.icon_drawer.icon_drawer_alt.icon_drive.icon_drive_alt.icon_easel.icon_easel_alt.icon_error-circle.icon_error-circle_alt.icon_error-oct.icon_error-oct_alt.icon_error-triangle.icon_error-triangle_alt.icon_film.icon_floppy.icon_floppy_alt.icon_flowchart.icon_flowchart_alt.icon_folder.icon_folder-add.icon_folder-add_alt.icon_folder-alt.icon_folder-open.icon_folder-open_alt.icon_folder_download.icon_folder_upload.icon_genius.icon_gift.icon_gift_alt.icon_globe.icon_globe-2.icon_globe_alt.icon_grid-2x2.icon_grid-3x3.icon_group.icon_headphones.icon_heart.icon_heart_alt.icon_hourglass.icon_house.icon_house_alt.icon_id.icon_id-2.icon_id-2_alt.icon_id_alt.icon_image.icon_images.icon_info.icon_info_alt.icon_key.icon_key_alt.icon_laptop.icon_lifesaver.icon_lightbulb.icon_lightbulb_alt.icon_like.icon_like_alt.icon_link.icon_link_alt.icon_loading.icon_lock.icon_lock-open.icon_lock-open_alt.icon_lock_alt.icon_mail.icon_mail_alt.icon_map.icon_map_alt.icon_menu.icon_menu-circle_alt.icon_menu-circle_alt2.icon_menu-square_alt.icon_menu-square_alt2.icon_mic.icon_mic_alt.icon_minus-06.icon_minus-box.icon_minus_alt.icon_minus_alt2.icon_mobile.icon_mug.icon_mug_alt.icon_music.icon_ol.icon_paperclip.icon_pause.icon_pause_alt.icon_pause_alt2.icon_pencil.icon_pencil-edit.icon_pencil-edit_alt.icon_pencil_alt.icon_pens.icon_pens_alt.icon_percent.icon_percent_alt.icon_phone.icon_piechart.icon_pin.icon_pin_alt.icon_plus.icon_plus-box.icon_plus_alt.icon_plus_alt2.icon_printer.icon_printer-alt.icon_profile.icon_pushpin.icon_pushpin_alt.icon_puzzle.icon_puzzle_alt.icon_question.icon_question_alt.icon_question_alt2.icon_quotations.icon_quotations_alt.icon_quotations_alt2.icon_refresh.icon_ribbon.icon_ribbon_alt.icon_rook.icon_search.icon_search-2.icon_search_alt.icon_shield.icon_shield_alt.icon_star.icon_star-half.icon_star-half_alt.icon_star_alt.icon_stop.icon_stop_alt.icon_stop_alt2.icon_table.icon_tablet.icon_tag.icon_tag_alt.icon_tags.icon_tags_alt.icon_target.icon_tool.icon_toolbox.icon_toolbox_alt.icon_tools.icon_trash.icon_trash_alt.icon_ul.icon_upload.icon_vol-mute.icon_vol-mute_alt.icon_volume-high.icon_volume-high_alt.icon_volume-low.icon_volume-low_alt.icon_wallet.icon_wallet_alt.icon_zoom-in.icon_zoom-in_alt.icon_zoom-out.icon_zoom-out_alt.social_blogger.social_blogger_circle.social_blogger_square.social_delicious.social_delicious_circle.social_delicious_square.social_deviantart.social_deviantart_circle.social_deviantart_square.social_dribbble.social_dribbble_circle.social_dribbble_square.social_facebook.social_facebook_circle.social_facebook_square.social_flickr.social_flickr_circle.social_flickr_square.social_googledrive.social_googledrive_alt2.social_googledrive_square.social_googleplus.social_googleplus_circle.social_googleplus_square.social_instagram.social_instagram_circle.social_instagram_square.social_linkedin.social_linkedin_circle.social_linkedin_square.social_myspace.social_myspace_circle.social_myspace_square.social_picassa.social_picassa_circle.social_picassa_square.social_pinterest.social_pinterest_circle.social_pinterest_square.social_rss.social_rss_circle.social_rss_square.social_share.social_share_circle.social_share_square.social_skype.social_skype_circle.social_skype_square.social_spotify.social_spotify_circle.social_spotify_square.social_stumbleupon_circle.social_stumbleupon_square.social_tumbleupon.social_tumblr.social_tumblr_circle.social_tumblr_square.social_twitter.social_twitter_circle.social_twitter_square.social_vimeo.social_vimeo_circle.social_vimeo_square.social_wordpress.social_wordpress_circle.social_wordpress_square.social_youtube.social_youtube_circle.social_youtube_square') as $name) {
                $map[$name] = 'egi-' . str_replace('_', '-', $name);
            }
            // Spellings the free themes used for the WordPress logo.
            $map['social_word-press'] = 'egi-social-wordpress';
            $map['social_word-press_circle'] = 'egi-social-wordpress-circle';
            $map['social_word-press_square'] = 'egi-social-wordpress-square';
        }

        return $map;
    }

}

if (!function_exists('spf_esi_names')) {

    // Essential Icons names (without the esi- prefix), read from the stylesheet the theme ships.
    function spf_esi_names() {
        static $names = null;

        if (null === $names) {
            $names = array();
            $css = @file_get_contents(dirname(__DIR__) . '/public/css/essentialicon.css'); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
            if ($css && preg_match_all('/\.esi-([a-z0-9-]+):before/', $css, $m)) {
                $names = array_flip($m[1]);
            }
        }

        return $names;
    }

}

if (!function_exists('spf_rename_old_icons')) {

    /*
     *  Old Elegant / Essential names -> egi- / esi- names, in a string, an array or an object.
     *
     *  Essential names (essential-icon-add) cannot be anything but icons, so they are
     *  renamed wherever they are. Elegant names (arrow_up, social_share, icon_id) can also
     *  be ordinary setting values or identifiers, so they are renamed only where an icon
     *  is stored: under a key that contains "icon", or as the value of a JSON key that does.
     */
    function spf_rename_old_icons($value, $icon_context = false) {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = spf_rename_old_icons($item, $icon_context || (is_string($key) && false !== stripos($key, 'icon')));
            }
            return $value;
        }

        if (is_object($value)) {
            foreach (get_object_vars($value) as $key => $item) {
                $value->$key = spf_rename_old_icons($item, $icon_context || false !== stripos($key, 'icon'));
            }
            return $value;
        }

        if (!is_string($value) || '' === $value) {
            return $value;
        }

        if (false !== strpos($value, 'essential')) {
            $names = spf_esi_names();
            $value = preg_replace_callback('/(?<![\w-])(?:essential-icon|essentialicon)-([a-z0-9-]+)(?![\w-])/', function ($match) use ($names) {
                return isset($names[$match[1]]) ? 'esi-' . $match[1] : $match[0];
            }, $value);
        }

        if (!preg_match('/(arrow|icon|social)[_-]/', $value)) {
            return $value;
        }

        $map = spf_old_icon_map();

        if ($icon_context) {
            // The whole value is an icon class list: rename each old name in it.
            return preg_replace_callback('/(?<![\w-])([a-z0-9][a-z0-9_-]*)(?![\w-])(?!"\s*:)/', function ($match) use ($map) {
                return isset($map[$match[1]]) ? $map[$match[1]] : $match[0];
            }, $value);
        }

        // JSON stored as text (repeaters): rename the value of a key that names an icon.
        return preg_replace_callback('/("[a-z0-9_-]*icon[a-z0-9_-]*"\s*:\s*")([^"]*)(")/i', function ($match) use ($map) {
            $classes = preg_replace_callback('/(?<![\w-])([a-z0-9][a-z0-9_-]*)(?![\w-])/', function ($m) use ($map) {
                return isset($map[$m[1]]) ? $map[$m[1]] : $m[0];
            }, $match[2]);
            return $match[1] . $classes . $match[3];
        }, $value);
    }

}

if (!function_exists('spf_migrate_icon_names')) {

    /**
     * Rename the saved names.
     *
     * @param bool $force Run again even when it has run before (after a demo import).
     */
    function spf_migrate_icon_names($force = false) {
        if (!$force && get_option('spf_icon_names_migrated')) {
            return;
        }

        global $wpdb;

        // Plugin settings.
        foreach (array('swpf_general_settings', 'swpf_metafield_settings') as $option) {
            $old = get_option($option, null);
            if (null !== $old) {
                $new = spf_rename_old_icons($old);
                if ($new !== $old) {
                    update_option($option, $new);
                }
            }
        }

        // Filters and product layouts, written by meta id so a revision never writes into its parent post.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time migration.
        $rows = $wpdb->get_results("SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('swpf_settings', 'swpf_layout') AND meta_value <> ''");
        foreach ((array) $rows as $row) {
            $old = maybe_unserialize($row->meta_value);
            $new = spf_rename_old_icons($old);
            if ($new !== $old) {
                update_metadata_by_mid('post', $row->meta_id, $new);
            }
        }

        update_option('spf_icon_names_migrated', 1, false);
    }

}
add_action('init', 'spf_migrate_icon_names', 20);

