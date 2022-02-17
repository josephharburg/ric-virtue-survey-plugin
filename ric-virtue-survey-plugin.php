<?php
 /**
 * Plugin Name: Restored in Christ Virtue Survey
 * Description: Adds the neccessary elements to create the Restored in Christ Virtue Survey.
 * Version: 1.0
 * Author: Joseph Harburg
 * License: GPL2
 */

   if ( ! defined( 'ABSPATH' ) ) 	exit; // Exit if accessed directly
   if(! class_exists( 'RIC_Virtue_Survey_Plugin' ) ){
     class RIC_Virtue_Survey_Plugin{

       public static $instance = null;

       function __construct(){
         if ( ! defined( 'VIRTUE_SURVEY_PLUGIN_DIR_PATH' ) ) define( 'VIRTUE_SURVEY_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
         if ( ! defined( 'VIRTUE_SURVEY_PLUGIN_NAME' ) ) define( 'VIRTUE_SURVEY_PLUGIN_NAME', plugin_basename(__FILE__) );
         if ( ! defined( 'VIRTUE_SURVEY_FILE_PATH' ) ) define( 'VIRTUE_SURVEY_FILE_PATH', plugin_dir_url(__FILE__) );

         require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/class-virtue-survey-actions-and-shortcodes.php';
         require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/class-virtue-survey-result-object.php';
         require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/admin/class-virtue-survey-settings.php';
         require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/class-virtue-survey-api.php';
         $survey_actions_and_shortcodes = new Virtue_Survey_Actions_And_Shortcodes;
         $admin_interface = new  Virtue_Survey_Settings;
         $plugin_rest_api = new Virtue_Survey_API;
       }

       /**
        * Create an instance of plugin object
        * @return OBJECT
        */
       public static function instance() {
         if ( is_null( self::$instance ) ) {
           self::$instance = new self();
         }
         return self::$instance;
       }


     /**
      * Activation function
      * @return void
      */

      function virtue_survey_plugin_activate() {

        //Require Gravity Forms to be installed or deactivate plugin
        if(!is_plugin_active('gravityforms/gravityforms.php')){
          deactivate_plugins( VIRTUE_SURVEY_PLUGIN_NAME );
          // Link to gravity forms page
          $plugin_link = 'https://www.gravityforms.com/';
          $message = '<p style="text-align: center;">' .
                     esc_html(sprintf(
                        'Please download and activate %s before activating the Virtue Survey Plugin.',
                       '<a href="' . $plugin_link. '" target="_blank">Gravity Forms</a>'
                     )) .
                     '</p>';
          wp_die( $message );
        }

      // Here we are creating the upload directories for backup storage
      $uploads = wp_upload_dir();
      $upload_dir = $uploads['basedir'];
      $upload_dir = $upload_dir . '/virtue-survey';
      if (! is_dir($upload_dir)) {
         mkdir( $upload_dir, 0700 );
      }

      // Create survey upload directory
      $survey_upload_dir = $upload_dir. "/surveys";
      if(! is_dir($survey_upload_dir)){
        mkdir( $survey_upload_dir, 0700 );
      }
      // Create entry upload directory
      $entry_upload_dir = $upload_dir. "/entries";
      if(! is_dir($entry_upload_dir)){
           mkdir( $entry_upload_dir, 0700 );
      }
      // Set virtue survey version
      if(get_option('current-vs-version') == false){
        add_option( 'current-vs-version', (float)1.0);
      }
    }
  }
}

RIC_Virtue_Survey_Plugin::instance();
register_activation_hook( __FILE__, array('RIC_Virtue_Survey_Plugin' ,'virtue_survey_plugin_activate') );
