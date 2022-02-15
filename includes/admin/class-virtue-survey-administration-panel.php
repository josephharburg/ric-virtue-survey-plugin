<?php
/**
* This class handles all the survey form actions and shortcodes
*
* @package ric-virtue-survey-plugin
* @version 1.0
*/

class Virtue_Survey_Administration_Panel
{
  function __construct(){
    // Add admin menu page for menu
        add_action('admin_menu', array($this, 'add_virtue_survey_admin_menus'));


  }
    /**
     * [add_virtue_survey_admin_menus description]
     */
    public function add_virtue_survey_admin_menus(){
      //Add Main Admin Panel
        add_menu_page( 'Virtue Survey Settings', 'Virtue Survey', 'add_users', 'virtue-survey-settings', array($this,'virtue_survey_settings_panel'), 'dashicons-media-spreadsheet', 5);

      // Add Survey Field ID Mapping SubMenu page
        add_submenu_page( 'virtue-survey-settings', 'Field Id Mapping', 'Map Field IDS', 'add_users', 'field-id-mapping', array($this, 'virtue_survey_field_id_mapping') );

      // Add Survey Version Download SubMenu page
        add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' );
        
      // Add Virtue Definitions and Resources SubMenu Page
        add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' );

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
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/previous-version-download-page.php';
    }

    /**
     * Pulls the template for the definitions and links settings
     * @return void
     */
    public function virtue_survey_definitions_and_links(){
        require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'admin-page-templates/virtue-definitions-and-links.php';
    }


}
