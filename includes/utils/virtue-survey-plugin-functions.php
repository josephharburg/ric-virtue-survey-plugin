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
   * @param  array  $results
   * @return string
   */

 function vs_output_results_table($results = [], $number_of_surveys = 1){
    $html_to_return ="<div><ul>";
    $rank = 1;
    foreach($results as $virtue){
      $html_to_return .= "<li><span style='font-weight: bold'>$rank. $virtue</span> <br/>".get_option('vs-'. $virtue .'-definition')."  </li>";
      $rank++;
    }
    $html_to_return .="</ul></div>";
    if($number_of_surveys > 1){
      $iteration_count = ($number_of_surveys < 3)? 2: 3;
      for($i = $number_of_surveys; $i > $number_of_surveys - $iteration_count; $i--){
        $current_result_object = get_user_meta( get_current_user_id(), "user-virtue-survey-result-$i", true );
        $results_array[] = $current_result_object->results;
      }

  // Positive results array
   $positive_increases = vs_calculate_positive_results($results_array);

  //negative results calculation
  foreach($results_array as $first_array_key => $result){
    if($first_array_key = 2){break;}
    foreach($result as $key => $virtue_result){
      if(!empty($results_array[$first_array_key + 1][$key])){

      }
    }
  }

    }
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
   * @see #CALC_INC_DEC_FN
   * @param  array $results_array
   * @return array
   */

  function vs_calculate_positive_results($results_array){
    // Get two most recent results by getting first two items in array
    $two_most_recent_results = array_slice($results_array, 2);
    $increased_virtues = [];
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
    $data = maybe_serialize($increased_virtues);
    }

    update_user_meta( get_current_user_id(), 'survey-positive-increases', $data);
    return $increased_virtues;
  }
