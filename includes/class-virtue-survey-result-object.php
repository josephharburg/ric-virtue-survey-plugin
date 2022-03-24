<?php

/** The Virtue Survey Result Object
*
* For every survey result, this is the object created.
* The object is capable of calculating and serving survey results.
*
* @package ric-virtue-survey-plugin
* @version 1.1
*/

if ( ! defined( 'ABSPATH' ) ) {

 exit; // Exit if accessed directly

}

class Virtue_Survey_Result {
  public $entry_id;
  public $form_id;
  public $survey_version;
  public $id_map = [];
  public $results = [];
  public $ranked_virtues = [];


  function __construct($entry_id = 1, $form_id = 11) {
    require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
    $this->entry_id = $entry_id;
    $this->form_id = $form_id;
    $this->survey_version = get_option("vs-current-version");
    $this->id_map = $this->save_id_map($form_id);
    $this->results = self::vs_calculate_survey_results($entry_id, $form_id);
    $this->ranked_virtues = array_keys($this->results);
  }

  /**
   * Calculates the survey results
   *
   * @param  int $entry_id
   * @return array
   */

  public static function vs_calculate_survey_results($entry_id, $form_id){
    $entry = GFAPI::get_entry( $entry_id );
    $form = GFAPI::get_form( $form_id );
    $optional_question_ids = get_option('optional_questions');
    $optional_questions= array();
    foreach($optional_question_ids as $field_id){
      $optional_questions[] = (stripos($key, 'reverse') !== false) ? 7 - rgar($entry, $field_id) : rgar($entry, $field_id);
    }

    // Get all the values set in admin interface
    $virtue_questions = vs_map_field_ids_to_array($form);

    foreach($virtue_questions as $current_virtue_name => $question_set){
      foreach($question_set as $key => $field_id){
      // If the key of the array has reverse in it make sure to do reverese calculation
       $current_virtue[] = (stripos($key, 'reverse') !== false) ? 7 - rgar($entry, $field_id) : rgar($entry, $field_id);
     }

    // Do the calculation after collecting all values
     $current_virtue_calculation =  array_sum($current_virtue) / count($current_virtue);
     $calculated_survey_results[$current_virtue_name] = $current_virtue_calculation;
    }
    // Sort it by highest value
     arsort($calculated_survey_results);
     // Positive results array
      // vs_calculate_and_save_positive_results($results_array);
    return $calculated_survey_results;
  }

  /**
   * Get the top virtue
   *
   * @return string
   */

  public function get_top_virtue(){
    return array_key_first($this->results);
  }

  /**
   * Get the weakest virtue
   *
   * @return string
   */

  public function get_weakest_virtue(){
    return array_key_last($this->results);
  }

  /**
   * Get the top 6 virtues
   *
   * @return array
   */

  public function get_top_six_virtues(){
    return array_keys(array_slice($this->results, 0, 6, true));
  }

  /**
   * Get the all virtues ranked by score
   *
   * @return array
   */

  public function get_ranked_virtues(){
    return $this->ranked_virtues;
  }

  /**
   * Save the filed id to virtue map
   *
   * @see #FIELD_MAPPING
   * @return string
   */

  public function save_id_map($form_id){
    $form = GFAPI::get_form( $form_id );
    return vs_map_field_ids_to_array($form);
  }


}
