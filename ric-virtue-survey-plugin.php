<?php
   /*
   Plugin Name: Restored in Christ Virtue Survey
   description: Adds the neccessary elements to create the Restored in Christ Virtue Survey.
   Version: 1.0
   Author: Joseph Harburg
   Author URI:
   License: GPL2
   */

   if ( ! defined( 'ABSPATH' ) ) {

   	exit; // Exit if accessed directly

   }

   if(! class_exists('RIC_Virtue_Survey_Plugin') ){
     class RIC_Virtue_Survey_Plugin{
       public static $instance = null;
       function __construct(){
         if ( ! defined( 'SURVEY_PLUGIN_DIR_PATH' ) ) define( 'SURVEY_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
         require_once SURVEY_PLUGIN_DIR_PATH . 'includes/class-virtue-survey-actions-and-shortcodes.php';
         require_once SURVEY_PLUGIN_DIR_PATH . 'includes/class-virtue-survey-result-object.php';
         require_once SURVEY_PLUGIN_DIR_PATH . 'admin/class-virtue-survey-administration-panel.php';

         $survey_actions_and_shortcodes = new Virtue_Survey_Actions_And_Shortcodes;
         $admin_interface = new Survey_Administation_Panel;
       }

       public static function instance() {
         if ( is_null( self::$instance ) ) {
           self::$instance = new self();
         }
         return self::$instance;
       }
     }
   }

   RIC_Virtue_Survey_Plugin::instance();
