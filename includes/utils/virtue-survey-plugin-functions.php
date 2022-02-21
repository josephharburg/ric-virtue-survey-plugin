<?php
/**
 * This file contains the plugins global functions.
 *
 * @package ric-virtue-survey-plugin
 */

/**
 * Returns an array of the virtue names
 *
 * @return array
 */

  function vs_get_virtue_list(){
    return array(
        'prudence',
        'justice',
        'fortitude',
        'temperance',
        'affability',
        'courtesy',
        'gratitude',
        'kindness',
        'loyalty',
        'obedience',
        'patriotism',
        'prayerfulness',
        'religion',
        'respect',
        'responsibility',
        'sincerity',
        'trustworhiness',
        'circumspection',
        'docility',
        'foresight',
        'industriousness',
        'magnanimity',
        'magnificence',
        'patience',
        'perseverance',
        'honesty',
        'humiliy',
        'meekness',
        'moderation',
        'modesty',
        'orderliness',
        'self-control'
    );
  }

  /**
   * This returns an html table of survey results
   *
   * @param  array  $results required
   * @param array $virtue_increases optional
   * @param array $virtue_decreases optional
   * @return string
   */

 function vs_output_results_table($results = [], $positive_increases){
    $html_to_return ="<div><ul>";
    $rank = 1;
    foreach($results as $virtue){
      $html_to_return .= "<li><span style='font-weight: bold'>$rank. $virtue</span> <br/>".get_option('vs-'. $virtue .'-definition')."  </li>";
      $rank++;
    }
    $html_to_return .="</ul></div>";

    //pull positive results

    //pull negative results

    return $html_to_return;
  }

  /**
   * Maps the field ids to the virtues dynamically
   *
   * @param  object|array $form
   * @return array       a multidimensional array
   */

  function vs_map_field_ids_to_array($form){
      $virtue_list = vs_get_virtue_list();
      $mapped_fields_ids = array();
      foreach ( $form['fields'] as $field ) {
        if(!empty($field->adminLabel)){
          $admin_label = $field->adminLabel;
          $field_id = $field->id;
          foreach($virtue_list as $virtue){
            if(stripos($admin_label, $virtue) !== false){
            $mapped_fields_ids[$virtue][$admin_label] = $field_id;
            }
          }
        }
      }
      return $mapped_fields_ids;
  }

  /**
   * Calculate and save users increased virtues.
   *
   * @see #CALC_INC_FN
   * @param  int $survey_completions
   */

  function vs_calculate_and_save_increases($survey_completions){
    if($survey_completions == 1){ return; }
    $increased_virtues = [];

    // Iterate twice from highest to lowest to get two most recent results
    for($i = $survey_completions; $i > $survey_completions - 2; $i--){
      $current_object = get_user_meta( get_current_user_id(), "user-virtue-survey-result-$i", true );
      $two_most_recent_results[] = $current_object->results;
    }

    // Iterate through the first array of virtue score pairs.
    foreach($two_most_recent_results[0] as $virtue_name => $score){
      $previous_score = $two_most_recent_results[1][$virtue_name];
      if($previous_score > $score){
        // Calculate percentage increase
        $perecent_increase = (($previous_score - $score) / $score) * 100;

        // If greater than 3% store in array.
        if($perecent_increase > 3) {
          $increased_virtues[$virtue_name] = array('Percent Increase' => $perecent_increase, "Raw Score Increase" => $previous_score - $score);
        }
      }
    }

    if( !is_serialized( $increased_virtues ) ) {
    $serialized_increases = maybe_serialize($increased_virtues);
    }

    update_user_meta( get_current_user_id(), 'survey-virtue-increases', $serialized_increases);
  }

  /**
   * Calculate and save users decreased virtues.
   *
   * @see #CALC_DEC_FN
   * @param  int $survey_completions
   */


  function vs_calculate_and_save_decreases($survey_completions){
    if($survey_completions < 3){ return; }
    $decreased_virtues = [];

    // Iterate twice from highest to lowest to get two most recent results
    for($i = $survey_completions; $i > $survey_completions - 3; $i--){
      $current_object = get_user_meta( get_current_user_id(), "user-virtue-survey-result-$i", true );
      $three_most_recent_results[] = $current_object->results;
    }



  }
