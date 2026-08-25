<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * Two kinds of data are involved here, and they are treated differently.
 *
 * Bookkeeping the plugin keeps for itself means nothing once the plugin is
 * gone and is always removed.
 *
 * The filters and settings are the site owner's own work. Deleting a plugin is
 * easy to do by accident, and it is a normal step when moving a site between
 * servers, so that content is only removed when the owner has asked for it
 * under Settings.
 */
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Remove this plugin's data from the site that is currently active.
 *
 * @return void
 */
function swpf_uninstall_current_site() {
    // State that only ever described a running copy of the plugin.
    delete_option('swpf_seo_defaults_set');

    $swpf_general_settings = get_option('swpf_general_settings');
    $swpf_delete_everything = is_array($swpf_general_settings)
        && isset($swpf_general_settings['delete_data_on_uninstall'])
        && 'on' === $swpf_general_settings['delete_data_on_uninstall'];

    if (!$swpf_delete_everything) {
        /*
         * The filters and settings stay put, so reinstalling picks up exactly
         * where the site left off.
         */
        return;
    }

    $swpf_owner_options = array(
        'swpf_general_settings',
        'swpf_popup_settings',
        'swpf_freeshipping_settings',
    );

    foreach ($swpf_owner_options as $swpf_option) {
        delete_option($swpf_option);
    }

    /*
     * The post type is not registered during uninstall, so ask for the posts by
     * name rather than going through any of the plugin's own helpers.
     * wp_delete_post with force takes the post meta with it.
     */
    $swpf_ids = get_posts(array(
        'post_type' => 'swpf-product-filter',
        'post_status' => 'any',
        'numberposts' => -1,
        'fields' => 'ids',
    ));

    foreach ($swpf_ids as $swpf_id) {
        wp_delete_post($swpf_id, true);
    }
}

/*
 * On a network each site keeps its own filters and settings, so every one of
 * them has to be visited. Sites are walked in batches so a large network does
 * not have to hold every blog id in memory at once.
 */
if (is_multisite()) {
    $swpf_offset = 0;
    $swpf_batch = 100;

    do {
        $swpf_site_ids = get_sites(array(
            'fields' => 'ids',
            'number' => $swpf_batch,
            'offset' => $swpf_offset,
            'spam' => 0,
            'deleted' => 0,
            'archived' => 0,
        ));

        foreach ($swpf_site_ids as $swpf_site_id) {
            switch_to_blog($swpf_site_id);
            swpf_uninstall_current_site();
            restore_current_blog();
        }

        $swpf_offset += $swpf_batch;
    } while (count($swpf_site_ids) === $swpf_batch);
} else {
    swpf_uninstall_current_site();
}
