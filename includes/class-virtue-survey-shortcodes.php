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
    add_shortcode( 'survey_results', array($this, 'output_survey_results'));
    // add_shortcode( 'student_survey_results_table', array($this, 'output_student_table' ));
  }

  public function output_survey_results(){
    if(is_user_logged_in()){
      $user_id =  get_current_user_id();
      if(!metadata_exists( 'user', $user_id, 'user-virtue-survey-result-1' )){
        return "<div>Please take a survey first to see results!</div>";
      }
        $survey_completions = get_user_meta( $user_id, "total-surveys-completed", true);
        $result_object = get_user_meta( $user_id, "user-virtue-survey-result-$survey_completions", true );
        return vs_output_results_table($result_object->get_ranked_virtues());
    }
    $results_meta_key = $_GET['uid']. "-". $_GET['quiz-results'];
    $result_object = get_transient( $results_meta_key );
    if(empty($result_object)){
      return "<div>Please take a survey first to see results!</div>";
    }
    return vs_output_results_table($result_object->get_ranked_virtues());
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
