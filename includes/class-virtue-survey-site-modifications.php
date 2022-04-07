<?php
/**
* This class handles all the modificaitons made to wordpress site
*
* @package ric-virtue-survey-plugins
* @version 1.0
*/
if ( ! defined( 'ABSPATH' ) ) 	exit; // Exit if accessed directly

class Virtue_Survey_Site_Modifications
{
  function __construct(){
    add_action('wp_enqueue_scripts', array($this,'vs_enqueue_scripts'));
    add_action('wp_login', array($this, 'vs_save_results_after_login_or_register'),10, 2);
    add_action('user_register', array($this, 'vs_save_results_after_login_or_register'),10, 2);
  }

  /**
   * Enqueues script to update the take another survey button url
   *
   * @param int|string $form_id
   * @return int
   */

  function vs_enqueue_scripts(){
    // Enqueue Front End stlyes
    $css_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH.'assets/css/frontend-style.min.css'));
    wp_enqueue_style( 'ric-styles', VIRTUE_SURVEY_FILE_PATH.'assets/css/frontend-style.min.css', array(), $css_version);

    // Enqueue Random URL Scripts
    if(is_front_page() || is_page('Survey Results')){
      $js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH. 'assets/js/get-random-survey-url.min.js'));
      wp_enqueue_script( 'return-random-url', VIRTUE_SURVEY_FILE_PATH.'assets/js/get-random-survey-url.min.js', array('jquery'), $js_version, true);
      wp_localize_script( 'return-random-url', 'randomSurvey',
      array(
      'nonce' => wp_create_nonce('wp_rest'),
      'ajaxURL' => get_site_url()."/wp-json/vs-api/v1/get-random-survey/",
      ) );
    }
  }

  /**
   * Save users results upon login or registration
   *
   * @param  string|int $user_login_or_id  Either a user login or id depending on hook
   * @param  object|array $userdata   Either a User object or array of user data depending on hook
   * @return void
   */

  function vs_save_results_after_login_or_register($user_login_or_id, $userdata){
    // Make sure we only save results if they are coming from the results page login form
    if(empty($_POST['return-code'])){return;}
    // Get the return code from form post object
    $return_code = $_POST['return-code'];
    $virtue_result_object = get_transient( "return-results-$return_code" );

    //If this transient does not exist exit
    if(empty($virtue_result_object)){return;}
    // We dont want the user to save multiple of the same results to their account so
    // we delete the transient to avoid repeat result objects being stored.
    delete_transient( "return-results-$return_code" );

    //The wp_login hook passes the user object while the user_register does not so we should use the user
    // user id passed by the user_register hook if user is not an object
    $user_id = (is_object($userdata)) ? $userdata->ID : $user_login_or_id;

    vs_save_results_to_usermeta($user_id, $virtue_result_object);
  }

}
