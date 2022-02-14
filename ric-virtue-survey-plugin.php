<?php
   /*
   Plugin Name: Restored in Christ Virtue Survey
   Plugin URI:
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
         require_once SURVEY_PLUGIN_DIR_PATH . 'includes/survey-results-actions.php';
         require_once SURVEY_PLUGIN_DIR_PATH . 'includes/survey-results-object.php';
         require_once SURVEY_PLUGIN_DIR_PATH . 'admin/survey-administration-panel.php';

         //Survey Calculations and Results
         $survey_results_actions = new Survey_Results_Actions;
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
