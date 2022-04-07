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
    add_action('wp_login', array($this, 'vs_save_user_results_on_login'),10, 2);
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
   * Save users results up login
   *
   * @param  string $user_login
   * @param  object $user
   * @return void
   */

  function vs_save_user_results_on_login($user_login, $user){
    // Make sure we only save results if they are coming from the results page login form
    if(empty($_POST['return-code'])){return;}
    // Get the return code from form post object
    $return_code = $_POST['return-code'];
    $virtue_result_object = get_transient( "return-results-$return_code" );

    //If this transient does not exist exit
    if(empty($virtue_result_object)){return;}

    $user_id = $user->ID;
    // We dont want the user to save multiple of the same results to their account so
    // we delete the transient to avoid repeat result objects being stored.
    delete_transient( "return-results-$return_code" );

    // See if the user has any stored surveys
    $survey_completions = get_user_meta($user_id, "total-surveys-completed", true);
    if($survey_completions == '' || $survey_completions == false){
      add_user_meta($user_id, "user-virtue-survey-result-1",$virtue_result_object, true);
      add_user_meta($user_id, "total-surveys-completed", 1, true);
    } else{
      $survey_completions++;
      add_user_meta($user_id, "user-virtue-survey-result-$survey_completions", $virtue_result_object, true);
      update_user_meta($user_id, "total-surveys-completed", $survey_completions);
    }
  }

}
