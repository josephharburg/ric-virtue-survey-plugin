<?php

/** The Virtue Survey Result Object
*
* For every survey result, this is the object created.
* The object is capable of calculating and serving survey results as well as raw survey entries.
* @package ric-virtue-survey-plugin
* @access public
* @version 1.1
*/

if ( ! defined( 'ABSPATH' ) ) {

 exit; // Exit if accessed directly

}

class Virtue_Survey_Result {
  public $entry_id = 0;
  public $form_id = 11;
  public $results = [];
  public $ranked_virtues = [];
  public $survey_version = "";
  private $mapped_ids = "";

  function __construct($entry_id = 1, $form_id = 11)
  {
    require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
    $this->entry_id = $entry_id;
    $this->form_id = $entry_id;
    $this->results = self::calculate_survey_results($entry_id, $form_id);
    $this->survey_version = get_option("vs-current-version");
    $this->ranked_virtues = array_keys($this->results);
  }

  /**
   * Calculates the survey results
   *
   * @param  int $entry_id
   * @return array
   */


  public static function calculate_survey_results($entry_id, $form_id){
    $entry = GFAPI::get_entry( $entry_id );
    $form = GFAPI::get_form( $form_id );
    $optional_question_ids = get_option('optional_questions');
    $optional_questions= array();
    foreach($optional_question_ids as $field_id){
      $optional_questions[] = (stripos($key, 'reverse') !== false) ? 7 - rgar($entry, $field_id) : rgar($entry, $field_id);
    }

    // Get all the values set in admin interface
    $virtue_questions = map_field_ids_to_array($form);

    foreach($virtue_questions as $current_virtue_name => $question_set){
      foreach($question_set as $key => $field_id){
      // If the key of the array has reverse in it make sure to do reverese calculation
       $current_virtue[] = (stripos($key, 'reverse') !== false) ? 7 - rgar($entry, $field_id) : rgar($entry, $field_id);
     }

    // Do the calculation after collecting all values
     $current_virtue_calculation = ceil( array_sum($current_virtue) / count($current_virtue) );
     $calculated_survey_results[$current_virtue_name] = $current_virtue_calculation;
    }
    // Sort it by highest value
     arsort($calculated_survey_results);
    return $calculated_survey_results;
  }

  public function get_top_virtue(){
    return array_key_first($this->results);
  }

  public function get_weakest_virtue(){
    return array_key_last($this->results);
  }

  public function get_top_six_virtues(){
    return array_keys(array_slice($this->results, 0, 6, true));
  }

  public function get_ranked_virtues(){
    return $this->ranked_virtues;
  }


}
