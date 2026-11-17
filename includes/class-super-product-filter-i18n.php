<?php

/**
 * Define the internationalization functionality.
 */
class Super_Product_Filter_i18n {

    public function load_plugin_textdomain() {

        load_plugin_textdomain(
            'super-product-filter', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}
