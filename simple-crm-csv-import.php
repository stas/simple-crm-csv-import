<?php
/*
Plugin Name: Simple CRM CSV Import Addon
Plugin URI: http://wordpress.org/extend/plugins/simple-crm-csv-import/
Description: CSV Import Addon lets you import users from a `.csv` file.
Author: Stas Sușcov
Version: 0.1
Author URI: http://stas.nerd.ro/
*/

define( 'SCRM_CSV_ROOT', dirname( __FILE__ ) );
define( 'SCRM_CSV_WEB_ROOT', WP_PLUGIN_URL . '/' . basename( SCRM_CSV_ROOT ) );

require_once SCRM_CSV_ROOT . '/includes/csv-import.class.php';

/**
 * i18n
 */
function scrm_csv_textdomain() {
    load_plugin_textdomain( 'scrm_csv', false, basename( SCRM_CSV_ROOT ) . '/languages' );
}
add_action( 'init', 'scrm_csv_textdomain' );

SCRM_CSV::init();

?>
