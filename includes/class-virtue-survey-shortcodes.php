<?php
/**
* This class handles all the survey form actions and shortcodes
*
* @package ric-virtue-survey-plugins
* @version 1.0
*/
if ( ! defined( 'ABSPATH' ) ) 	exit; // Exit if accessed directly

class Virtue_Survey_Shortcodes
{
  function __construct(){
    require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
    add_shortcode( 'survey_results', array($this, 'vs_output_survey_results'));
    // add_shortcode( 'student_survey_results_table', array($this, 'output_student_table' ));
    add_shortcode( 'random-survey-button', array($this, 'vs_ouput_random_survey_button') );
    add_shortcode( 'output_part_two', array($this, 'vs_ouput_random_survey_part_two') );
  }

  /**
   * Shortcode to output results on results page
   * @param  array $atts               shortcode attributes
   * @return string
   */

  public function vs_output_survey_results(){
    // if(is_user_logged_in()){
    //   $user_id =  get_current_user_id();
    //   if(!metadata_exists( 'user', $user_id, 'user-virtue-survey-result-1' )){
    //     return "<div>Please take a survey first to see results!</div>";
    //   }
    //     $survey_completions = get_user_meta( $user_id, "total-surveys-completed", true);
    //     $result_object = get_user_meta( $user_id, "user-virtue-survey-result-$survey_completions", true );
    //     return vs_output_results_table($result_object->get_ranked_virtues());
    // }
    // $results_meta_key = $_GET['uid']. "-". $_GET['quiz-results'];
    $return_code = $_GET['return-code'];
    $result_object = get_transient( "return-results-$return_code" );
    if(empty($result_object)){
      return "<div>Please take a survey first to see results!</div>";
    }
    return vs_output_results_table($result_object->get_ranked_virtues());
  }

  /**
   * Outputs a random survey button
   * @param  array $atts               shortcode attributes
   * @return string
   */

  function vs_ouput_random_survey_button($atts){
    session_start();
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );
    $shortcode_atts = shortcode_atts(array('retake' => 'false'), $atts);
    $button_style = "has-white-background-color has-text-color has-background survey-btn";
    // Set Session variable as array if not set
    if(!isset($_SESSION['surveys-taken'])){
      $_SESSION['surveys-taken'] = array();
    }

    // Add completed survey ID to Session Variable if on the results page
    // and restyle button accordingly
    if($shortcode_atts['retake'] == 'true'){
      $_SESSION['surveys-taken'][] = $_GET['formID'];
      $button_style = "has-text-color has-background retake-btn";
    }

    // Create array with 16 numbers representing survey numbers
    $available_surveys = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
    foreach($available_surveys as $k=> $v){
      if(in_array($v, $_SESSION['surveys-taken'])){
        unset($available_surveys[$k]);
      }
    }
    $site_url = get_site_url();
    $random_key = array_rand($available_surveys,1);
    $button_text = ($shortcode_atts['retake'] == 'false') ? "Take Survey" : "Take Another Survey";
    $button_to_return = "<div class='wp-block-button'> <a class='survey-rdm-button wp-block-button__link $button_style' href='$site_url/survey-version-{$available_surveys[$random_key]}'>$button_text</a></div>";
    return $button_to_return;
  }

  /**
   * Outputs a random survey button
   * @param  array $atts               shortcode attributes
   * @return string
   */

  function vs_ouput_random_survey_part_two($atts){
    // $atts = array_change_key_case( (array) $atts, CASE_LOWER );
    // $shortcode_atts = shortcode_atts(array('retake' => 'false'), $atts);
    if(!$_GET['form-id']){
      return "<div>Oops something broke. ¯\(°_o)/¯ <br/> Please click back and enter your code again.</div>";
    }

    return do_shortcode( '[gravityform id="'.$_GET['form-id'].'" title="false" description="false"]' );

  }

  //  function output_student_table(){
  //   //some user post meta on teachers account
  //   $CODE = 'ROUGETWO';
  //   $search_criteria['field_filters'][] = array( 'key' => '17', 'value' => $CODE );
  //   $entries = GFAPI::get_entries(12, $search_criteria);
  //   $array_of_user_values = [];
  //   foreach($entries as $entry){
  //     $student = $entry['18.3']. " " . $entry['18.6'];
  //     $result_obj = get_user_meta($entry['20'] ,"{$entry['20']}-{$entry['id']}", true);
  //     if(!empty($result_obj) && is_object($result_obj)){
  //       $array_of_user_values[$student] = $result_obj->get_top_virtue();
  //     }
  //   }
  //   $html_to_return = "<div><table><th style='font-weight: bold;'>Student Name</th><th style='font-weight: bold;'>Top Virtue</th>";
  //   foreach($array_of_user_values as $name => $virtue){
  //     $html_to_return .= "<tr><td>$name</td><td>$virtue</td></tr>";
  //   }
  //   $html_to_return .="</table></div>";
  //   return $html_to_return;
  // }
}
