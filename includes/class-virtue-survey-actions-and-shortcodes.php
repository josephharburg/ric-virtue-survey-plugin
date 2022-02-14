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
    add_action( 'gform_after_submission_12', array($this,'calculate_data_results'), 10, 2 );
    add_shortcode( 'output_transient',array($this, 'output_survey_results'));
    add_shortcode( 'student_survey_results_table', array($this, 'output_student_table' ));
  }

  /*We cant assume the user is logged in because this works on non logged in users. What we
  * when we prerender the user_id_field it either uses the current users ID or generate a random one
  * in the form.
  */
  function calculate_data_results($entry, $form){
    $user_id = rgar($entry, 19);
    if($form['id'] == 12){
      $user_id = get_current_user_id();
    }
    $user_results_meta_key = "$user_id-".rgar($entry,'id')."";
    if(class_exists('Virtue_Survey_Result')){
      $virtue_result_object = new Virtue_Survey_Result($entry);
    }
    if(is_user_logged_in()){
      add_user_meta($user_id ,$user_results_meta_key, $virtue_result_object, true );
    } else{
      set_transient($user_results_meta_key, $virtue_result_object, DAY_IN_SECONDS );
    }
  }

  public function output_survey_results(){
    $results_transient_key = $_GET['uid']. "-". $_GET['quiz-results'];
    $results_array = get_transient( $results_transient_key );
    if(is_user_logged_in()){
        $results = get_user_meta( get_current_user_id(), $results_transient_key, true );
    }
    return $results::output_results_page($results->results);
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
