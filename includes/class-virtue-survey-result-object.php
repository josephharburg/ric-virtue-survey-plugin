<?php

/** The Virtue Survey Result Object
*
* For every survey result, this is the object created.
* The object is capable of calculating and serving survey results as well as raw survey entries.
* @package ric-virtue-survey-plugin
* @access public
* @version 1.0
*/

if ( ! defined( 'ABSPATH' ) ) {

 exit; // Exit if accessed directly

}

class Virtue_Survey_Result {
  public $entry = [];
  public $results = [];
  public $test_version = "1.0";

  function __construct($entry = array(), $test_version = "1.0")
  {
    $this->entry = $entry;
    $this->results = $this->calculate_survey_results($entry);
    $this->test_version = get_option("ric-test-version");
  }

  public function calculate_survey_results($entry){
    $optional_question_ids = get_option('optional_questions');
    $optional_questions= array();
    foreach($optional_question_ids as $field_id){
      $optional_questions[] = rgar($entry, $field_id);
    }

    /*
      EXAMPLE option array
      array( 'virtue_name => 'prudence', 'admin_label' => 'field id', 'admin_label_reverese' => 'field id', ... );
    */

    // Get all the values set in admin interface for the current version of the survey
    $virtue_questions = array(get_option('prudence_question_ids'));

    foreach($virtue_questions as $question_set){
      $current_virtue_name = $question_set['virtue_name'];
      foreach($question_set as $key => $field_id){

    // Skip virtue_name value
       if($key === 'virtue_name') continue;

    // If the key of the array has reverse in it make sure to do reverese calculation
       $current_virtue[] = (stipos($key, 'reverse') !== false) ? 7 - rgar($entry, $field_id) : rgar($entry, $field_id);
     }

    // Do the calculation after collecting all values
     $current_virtue_calculation = ceil( array_sum($current_virtue) / count($current_virtue) );
     $calculated_survey_results[$current_virtue_name] = $current_virtue_calculation;
    }
     arsort($calculated_survey_results);
    return $calculated_survey_results;
  }

  public function get_top_virtue(){
    return array_key_first($this->results);
  }

  public function get_weakest_virtue(){
    return array_key_last($this->results);
  }

  public static function output_results_page($results = []){
    $html_to_return ="<div><ul>";
    $order = 1;
    foreach($results as $virtue => $value){
      $html_to_return .= "<li><span style='font-weight: bold'>$order</span>: $virtue. Definition:".get_option($virtue .'-definition').". See More: ".get_option($virtue .'-see-more-link')."  </li>";
      $order++;
    }
    $html_to_return .="</ul></div>";
    return $html_to_return;
  }
}
