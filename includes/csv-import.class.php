<?php
/**
 * Class adds CSV import for users
 */
class SCRM_CSV {
    /**
     * Transient key to store import results
     */
    public static $results_key = 'scrm_csv_import';
    
    /**
     * Static constructor. Loads all the hooks
     */
    function init() {
        add_action( 'scrm_options_screen_updated', array( __CLASS__, 'screen_update' ) );
        add_action( 'scrm_options_screen', array( __CLASS__, 'screen' ), 11 );
    }
    
    /**
     * Options screen
     */
    function screen() {
        $vars = array();
        $vars['results'] = get_transient( self::$results_key );
        // Cleanup old messages
        delete_transient( self::$results_key );
        
        self::template_render( 'import_screen', $vars );
    }
    
    /**
     * Update options
     */
    function screen_update() {
        if( isset( $_POST['scrm_csv_nonce'] ) && wp_verify_nonce( $_POST['scrm_csv_nonce'], 'scrm_csv' ) )
            if( isset( $_FILES['scrm_csv_import_filename'] ) && !empty( $_FILES['scrm_csv_import_filename']['name'] ) )
                self::import( $_FILES['scrm_csv_import_filename'] );
    }
    
    /**
     * Imports users with their xprofile fields data
     */
    function import( $import_file ) {
        $lines = 0;
        $imported = 0;
        $file_data = file_get_contents( $import_file['tmp_name'] );
        $file_data = explode( "\n", $file_data );
        
        // Parse the csv
        foreach( $file_data as $line ) {
            if( $lines != 0 ) {
                $line = str_replace( '"', '', $line );
                $line_fields = explode( ",", $line );
                // Names and email are mandatory
                if( count( $line_fields ) > 2 ) {
                    $userdata = array();
                    $userdata['first_name'] = $line_fields[0];
                    $userdata['last_name'] = $line_fields[1];
                    $userdata['display_name'] = implode( " ", $userdata );
                    $userdata['user_email'] = $line_fields[2];
                    $userdata['user_login'] = sanitize_key( $userdata['display_name'] );
                    // Generate a random password with time() as salt
                    $userdata['user_pass'] = md5( $userdata['display_name'] . time() );
                    // All except first 3 fields will go right into user description
                    $userdata['description'] = implode( " ", array_slice( array_filter( $line_fields ), 3 ) );
                    // Try to register the new user
                    $uid = wp_insert_user( $userdata );
                    // On success
                    if( is_numeric( $uid ) && $uid > 0 )
                        $imported++;
                }
            }
            $lines++;
        }
        // Store some results
        $results[0] = sprintf( __( 'Imported %s users by parsing %s lines. ','scrm_csv' ), $imported, $lines );
        $results[1] = __( 'Go check the users &rarr;','scrm_csv' );
        set_transient( self::$results_key, $results );
        
        // Cleanup
        unlink( $import_file['tmp_name'] );
    }
    
    /**
     * template_render( $name, $vars = null, $echo = true )
     *
     * Helper to load and render templates easily
     * @param String $name, the name of the template
     * @param Mixed $vars, some variables you want to pass to the template
     * @param Boolean $echo, to echo the results or return as data
     * @return String $data, the resulted data if $echo is `false`
     */
    function template_render( $name, $vars = null, $echo = true ) {
        ob_start();
        if( !empty( $vars ) )
            extract( $vars );
        
        if( !isset( $path ) )
            $path = dirname( __FILE__ ) . '/templates/';
        
        include $path . $name . '.php';
        
        $data = ob_get_clean();
        
        if( $echo )
            echo $data;
        else
            return $data;
    }
}
?>