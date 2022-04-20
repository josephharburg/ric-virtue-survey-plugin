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
  public $matched_items;
  public $field_id_maps;
  public $results;
  public $ranked_virtues;
  public $return_code;

  function __construct($entry = null, $form = null, $return_code='') {
    // Make sure both entry and form is passed
    if($entry == null ||  $form == null){
      $error = new WP_Error( 'Form Submission Error', __( "Oops, something went wrong please click the back link below and click submit again.") );
      wp_die($error, $error->title, array('response' => 200, 'back_link' => true));
    }

    $this->return_code = $return_code;
    $this->matched_items = $this->vs_match_forms_and_entries($form, $entry, $return_code);
    $this->field_id_maps = $this->vs_save_form_field_id_maps($this->matched_items);
    $this->results = $this->vs_calculate_survey_results($this->matched_items,$this->field_id_maps);
    $this->ranked_virtues = array_keys($this->results);
  }

  /**
   * Match both forms with both entries.
   *
   * @param  array $form_one
   * @param array $entry_one
   *
   * @return array
   */

  public function vs_match_forms_and_entries($form_one, $entry_one, $return_code){
    $transient_data = get_transient( "$return_code-data" );
    if($transient_data){
      $matching_form = GFAPI::get_form($transient_data['form-id']);
      $entry_two = GFAPI::get_entry($transient_data['entry-id']);
    } else{
      $matching_form_data = vs_get_correlated_form($form_one['id']);
      $search_criteria['field_filters'][] = array( 'key' => '19', 'value' => $return_code );
      $matching_entry = GFAPI::get_entries( $matching_form_data['id'], $search_criteria);
      $entry_two = reset($matching_entry);
      $matching_form = $matching_form_data['form'];
    }

    $matched_items = array(
      array('form'=> $form_one,'entry'=> $entry_one),
      array('form'=> $matching_form,'entry'=> $entry_two)
    );
    return $matched_items;
  }

  /**
   * Calculates the survey results
   *
   * @param  int $entry_id
   * @return array
   */

  public function vs_calculate_survey_results($matched_items, $field_id_maps){
    foreach($matched_items as $item){
      $current_form = $item['form'];
      $entry = $item['entry'];
      /** @see #MAPPING_FIELDS */
      $virtue_questions = $field_id_maps[$current_form['id']];
      foreach($virtue_questions as $current_virtue_name => $field_id_set){
        // Make SURE THE CURRENT VIRTUE ARRAY IS EMPTY DERPPPP!!! I cant believe I forgot to do this. (* ￣︿￣)
        $current_virtue = [];
        foreach($field_id_set as $admin_label => $field_id){
        // If the key(admin_label) of the array has reverse in it make sure to do reverese calculation
         $current_virtue[] = (stripos($admin_label, 'neg') !== false) ? 7 - rgar($entry, $field_id) : rgar($entry, $field_id);
       }
       // Do the calculation after collecting all values
       $current_virtue_calculation =  array_sum($current_virtue) / count($current_virtue);
       $calculated_survey_results[$current_virtue_name] = $current_virtue_calculation;
      }
    }
    // Sort it by highest value
    arsort($calculated_survey_results);
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

  public function vs_save_form_field_id_maps($matched_items){
    $field_maps = [];
    foreach($matched_items as $item){
      $field_maps[$item['form']['id']] = vs_map_field_ids_to_array($item['form']);
    }
    return $field_maps;
  }


}
