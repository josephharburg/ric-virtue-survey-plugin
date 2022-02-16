<?php
/**
* This class handles all the survey form actions and shortcodes
*
* @package ric-virtue-survey-plugin
* @version 1.0
*/

class Virtue_Survey_Settings
{
    public function __construct(){
      add_action('admin_menu', array($this, 'add_virtue_survey_admin_menus'));
      add_filter('plugin_action_links_'.VIRTUE_SURVEY_PLUGIN_NAME, array($this, 'admin_settings_link'), 10, 1);
      wp_enqueue_scripts('plugin-admin-style', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Adds the menus to WordPress Backend
     *
     * @return void
     */

    public function add_virtue_survey_admin_menus(){
      $capability = 'manage_options';

      add_menu_page( 'Virtue Survey Settings', 'Virtue Survey', $capability, 'virtue-survey-settings', array($this,'vs_settings_panel'), 'dashicons-media-spreadsheet', 5);

      add_submenu_page('virtue-survey-settings', 'Map Field Ids', 'Question Field Id Mapping', $capability, 'field-id-mapping', array($this, 'vs_field_id_mapping') );

      add_submenu_page( 'virtue-survey-settings', 'Donwnload Previous Survey Versions', 'Download Previous Verisions', $capability, 'current-version-upload', array($this,'vs_version_uploads' ));

      add_submenu_page( 'virtue-survey-settings', 'Upload Current Survey Version', 'Upload Current Survey Version', $capability, 'previous-version-download', array($this,'vs_version_downloads' ));

      add_submenu_page( 'virtue-survey-settings', 'Virtue Definitions and Resources', 'Result Settings', $capability, 'virtue-definitions-and-links', array($this, 'vs_definitions_and_links') );

    }

    /**
     * Pulls the template the Main Menu for virtue survey settings
     * @return void
     */

    public function vs_settings_panel(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/admin-main-panel.php';
    }

    /**
     * Pulls the template for field id mapping
     * @return void
     */

    public function vs_field_id_mapping(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/field-id-mapping.php';
    }

    /**
     * Pulls the template for the version download page
     * @return void
     */

    public function vs_version_downloads(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/previous-version-download.php';
    }

    /**
     * Pulls the template for the definitions and links settings
     * @return void
     */

    public function vs_definitions_and_links(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/virtue-definitions-and-links.php';
    }

    /**
     * Pulls the template for the definitions and links settings
     * @return void
     */

    public function vs_version_uploads(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/upload-current-survey-version.php';
    }

    /**
     * Enqueues the scripts for admin interface
     * @return void
     */

    public function enqueue_admin_scripts(){
      if(!is_admin()) return;
      $current_css_ver  = date("ymd-Gis", filemtime(   VIRTUE_SURVEY_FILE_PATH. 'assets/css/admin-styles-min.css'));
      wp_enqueue_style( 'virtue-survey-admin-styles', VIRTUE_SURVEY_FILE_PATH. 'assets/css/admin-styles-min.css', array(), $current_css_ver );

      //Add Javascript Files here as well
      // wp_enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false );

    }

    /**
     * Add settings link to plugins page
     * @param  array $links
     * @return array
     */

    public function admin_settings_link($links){
      $settings_link = '<a href="admin.php?page=virtue-survey-settings">Settings</a>';
      array_push($links, $settings_link);
      return $links;
    }



}
