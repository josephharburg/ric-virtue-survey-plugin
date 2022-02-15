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

    public function enqueue_admin_scripts(){
      if(!is_admin()) return;
      $current_css_ver  = date("ymd-Gis", filemtime(   VIRTUE_SURVEY_FILE_PATH. 'assets/admin-styles-min.css'));
      wp_enqueue_style( 'virtue-survey-admin-styles', VIRTUE_SURVEY_FILE_PATH. 'assets/admin-styles-min.css', array(), $current_css_ver );

      //Add Javascript Files here as well


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

    /**
     * Adds the menus to WordPress Backend
     *
     * @return void
     */

    public function add_virtue_survey_admin_menus(){
      $capability = 'add_users';

      //Add Main Admin Panel
        add_menu_page( 'Virtue Survey Settings', 'Virtue Survey', $capability, 'virtue-survey-settings', array($this,'virtue_survey_settings_panel'), 'dashicons-media-spreadsheet', 5);

      // Add Survey Field ID Mapping SubMenu page
        add_submenu_page( 'virtue-survey-settings', 'Field Id Mapping', 'Map Field IDS', $capability, 'field-id-mapping', array($this, 'virtue_survey_field_id_mapping') );

      // Add Survey Version Download SubMenu page
        add_submenu_page( 'virtue-survey-settings', 'Donwnload Previous Survey Versions', 'Download Previous Verisions', $capability, 'previous-version-download', array($this,'virtue_survey_version_downloads' ));

      // Add Virtue Definitions and Resources SubMenu Page
        add_submenu_page( 'virtue-survey-settings', 'Virtue Definitions and Resources', 'Result Settings', $capability, 'virtue-definitions-and-links', array($this, 'virtue_survey_definitions_and_links') );

    }

    /**
     * Pulls the template the Main Menu for virtue survey settings
     * @return void
     */
    public function virtue_survey_settings_panel(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/admin-main-panel.php';
    }

    /**
     * Pulls the template
     * @return void
     */
    public function virtue_survey_field_id_mapping(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/field-id-mapping.php';
    }

    /**
     * Pulls the template for the version download page
     * @return void
     */
    public function virtue_survey_version_downloads(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/previous-version-download.php';
    }

    /**
     * Pulls the template for the definitions and links settings
     * @return void
     */
    public function virtue_survey_definitions_and_links(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/virtue-definitions-and-links.php';
    }


}
