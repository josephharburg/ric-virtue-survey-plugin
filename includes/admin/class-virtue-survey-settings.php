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
      add_action('admin_menu', array($this, 'vs_add_admin_menus'));
      add_filter('plugin_action_links_'.VIRTUE_SURVEY_PLUGIN_NAME, array(__CLASS__, 'vs_plugin_settings_link'), 10, 1);
      add_action('admin_enqueue_scripts', array($this, 'vs_enqueue_admin_scripts'));
    }

    /**
     * Adds the menus to WordPress Backend
     *
     * @return void
     */

    public function vs_add_admin_menus(){
      $capability = 'create_users';

      add_menu_page( 'Virtue Survey Settings', 'Virtue Survey', $capability, 'virtue-survey-settings', array($this,'vs_settings_main'), 'dashicons-media-spreadsheet', 5);

      add_submenu_page( 'virtue-survey-settings', 'Download Backups', 'Download Backups', 'create_users', 'download-backups', array($this,'vs_version_downloads' ));

      add_submenu_page( 'virtue-survey-settings', 'Upload Backups', 'Upload Backups', 'create_users', 'upload-backups', array($this,'vs_version_uploads' ));

      add_submenu_page( 'virtue-survey-settings', 'Virtue Definitions and Resources', 'Question Settings', 'create_users', 'virtue-definitions', array($this, 'vs_definitions') );

    }

    /**
     * Pulls the template the Main Menu for virtue survey settings
     * @return void
     */

    public function vs_settings_main(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/admin/admin-page-templates/admin-main-panel.php';
    }

    /**
     * Pulls the template for the version download page
     * @return void
     */

    public function vs_version_downloads(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/admin/admin-page-templates/download-backups.php';
    }

    /**
     * Pulls the template for the definitions and links settings
     * @return void
     */

    public function vs_definitions(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/admin/admin-page-templates/virtue-definitions.php';
    }

    /**
     * Pulls the template for the definitions and links settings
     * @return void
     */

    public function vs_version_uploads(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/admin/admin-page-templates/upload-backups.php';
    }

    /**
     * Enqueues the scripts for admin interface
     * @return void
     */

    public function vs_enqueue_admin_scripts(){
      if(!is_admin()) return;
      $current_css_ver  = date("ymd-Gis", filemtime(   VIRTUE_SURVEY_PLUGIN_DIR_PATH. 'assets/css/admin-styles.css'));
      wp_enqueue_style( 'virtue-survey-admin-styles', VIRTUE_SURVEY_FILE_PATH .'assets/css/admin-styles.css', array());
    }

    /**
     * Add settings link to plugins page
     * @param  array $links
     * @return array
     */

    public function vs_plugin_settings_link($links){
      $settings_link = '<a href="admin.php?page=virtue-survey-settings">Settings</a>';
      array_push($links, $settings_link);
      return $links;
    }



}
