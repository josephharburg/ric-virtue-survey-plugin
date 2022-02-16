<?php
/**
* This class handles all the survey form actions and shortcodes
*
* @package ric-virtue-survey-plugins
* @version 1.0
*/

class Virtue_Survey_Actions_And_Shortcodes
{
  function __construct(){
    add_action( 'gform_after_submission_11', array($this, 'calculate_data_results'), 10, 2 );
    add_action( 'gform_after_submission_12', array($this, 'calculate_data_results'), 10, 2 );
    add_shortcode( 'survey_results', array($this, 'output_survey_results'));
    add_shortcode( 'student_survey_results_table', array($this, 'output_student_table' ));
  }

  /*We cant assume the user is logged in because this works on non logged in users. What we
  * when we prerender the user_id_field it either uses the current users ID or generate a random one
  * in the form.
  */

  function calculate_data_results($entry, $form){
    if(class_exists('Virtue_Survey_Result')){
      $virtue_result_object = new Virtue_Survey_Result($entry);
    }
    // If user is logged in add it to their account.
    if(is_user_logged_in()){
      $user_id = get_current_user_id();
      // Get the number of completed surveys
      $survey_completions = get_user_meta( $user_id, "total_surveys_completed", true );
      // We collect survey result objects individually as separate meta keys and values.
      // Later, we will iterate through all the meta value results to calculate the
      // increase or decrease of virute values.
      if($survey_completions == '' || $survey_completions == false){
        add_user_meta($user_id, "user_virtue_survey_result_1",$virtue_result_object, true);
        add_user_meta($user_id, "total_surveys_completed", 1, true);
      } else{
        $survey_completions++;
        add_user_meta($user_id, "user_virtue_survey_result_$survey_completions", $virtue_result_object, true);
        update_user_meta($user_id, "total_surveys_completed", $survey_completions, $survey_completions--);
      }
    } else{
      $user_id = rgar($entry, 19);
      $user_results_meta_key = "$user_id-".rgar($entry,'id')."";
      set_transient($user_results_meta_key, $virtue_result_object, DAY_IN_SECONDS );
    }
  }

  public function output_survey_results(){
    if(is_user_logged_in()){
        $survey_completions = get_user_meta( $user_id, "total_surveys_completed", true);
        $result_object = get_user_meta( get_current_user_id(), "user_virtue_survey_result_$survey_completions", true );
        return $result_object::output_results_page($result_object->results);
    }
    $results_meta_key = $_GET['uid']. "-". $_GET['quiz-results'];
    $result_object = get_transient( $results_meta_key );
    return $result_object::output_results_page($result_object->results);
  }

   function output_student_table(){
    //some user post meta on teachers account
    $CODE = 'ROUGETWO';
    $search_criteria['field_filters'][] = array( 'key' => '17', 'value' => $CODE );
    $entries = GFAPI::get_entries(12, $search_criteria);
    $array_of_user_values = [];
    foreach($entries as $entry){
      $student = $entry['18.3']. " " . $entry['18.6'];
      $result_obj = get_user_meta($entry['20'] ,"{$entry['20']}-{$entry['id']}", true);
      if(!empty($result_obj) && is_object($result_obj)){
        $array_of_user_values[$student] = $result_obj->get_top_virtue();
      }
    }
    $html_to_return = "<div><table><th style='font-weight: bold;'>Student Name</th><th style='font-weight: bold;'>Top Virtue</th>";
    foreach($array_of_user_values as $name => $virtue){
      $html_to_return .= "<tr><td>$name</td><td>$virtue</td></tr>";
    }
    $html_to_return .="</table></div>";
    return $html_to_return;
  }
}
