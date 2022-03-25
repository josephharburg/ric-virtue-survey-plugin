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
  public $return_code;


  function __construct($entry_id = 1, $form_id = 11, $return_code='') {
    require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
    $this->entry_id = $entry_id;
    $this->form_id = $form_id;
    $this->return_code = $return_code;
    // $this->survey_version = get_option("vs-current-version");
    // $this->id_map = $this->save_id_map($form_id);
    // $this->results = self::vs_calculate_survey_results($entry_id, $form_id);
    // $this->ranked_virtues = array_keys($this->results);
  }

  /**
   * Calculates the survey results
   *
   * @param  int $entry_id
   * @return array
   */

  public static function vs_calculate_survey_results($entry_one, $form_one,  $return_code){
    var_dump($entry_one);
    var_dump($form_one);

    $matching_form = vs_get_matching_form($form_one['id']);
    $search_criteria['field_filters'][] = array( 'key' => '19', 'value' => $return_code );
    $matching_entry = GFAPI::get_entries( $matching_form['id'], $search_criteria);
    $entry_two = reset($matching_entry);
    var_dump($entry_two);
    var_dump($matching_form['form']);
    die();
    // $hashed_obj = new SplObjectStorage();
    // $hashed_obj[$form_one] = $entry_one;
    // $hashed_obj[$matching_form['form']] = $entry_two;
    // $hashed_obj->rewind();
    // while($hashed_obj->valid()) {
    //     $current_form  = $hashed_obj->key();
    //     $current_entry = $hashed_obj[$current_form];
    //     /** @see #MAPPING_FIELDS */
    //     $virtue_questions = vs_map_field_ids_to_array($current_form);
    //     foreach($virtue_questions as $current_virtue_name => $field_id_set){
    //       $current_virtue = [];
    //       foreach($field_id_set as $admin_label => $field_id){
    //       // If the key of the array has reverse in it make sure to do reverese calculation
    //        $current_virtue[] = (stripos($admin_label, 'neg') !== false) ? 7 - rgar($current_entry, $field_id) : rgar($current_entry, $field_id);
    //      }
    //      // Do the calculation after collecting all values
    //      $current_virtue_calculation =  array_sum($current_virtue) / count($current_virtue);
    //      $calculated_survey_results[$current_virtue_name] = $current_virtue_calculation;
    //    }
    //     $hashed_obj->next();
    // }
    //
    // // Sort it by highest value
    // arsort($calculated_survey_results);
    // return $calculated_survey_results;
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
