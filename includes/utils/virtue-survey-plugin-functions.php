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
   * @return string
   */

 function vs_output_results_table($results){

    $html_to_return ="<div><ol>";
    foreach($results as $virtue){
        $virtue_style = ucfirst($virtue);
      $html_to_return .= "<li><span style='font-weight: bold'>$virtue_style</span> <br/>".get_option('vs-'. $virtue .'-definition')."  </li>";
    }
    $html_to_return .="</ol></div>";

    if(is_user_logged_in()){
      $user_id = get_current_user_id();
      //pull positive results
      if(metadata_exists( 'user', $user_id  , 'survey-virtue-increases' )){
        $positive_results = get_user_meta( $user_id, 'survey-virtue-increases', true );
        $html_to_return .= '<div><h3>Your answers indicate the you have grown in the following virtues since the last survey you took: </h3><ul>';
        foreach($positive_results as $virtue_name => $value){
          $virtue_style = ucfirst($virtue_name);
          $rounded_score = round($value, 0, PHP_ROUND_HALF_UP);
          $html_to_return .= "<li>$virtue_style +$rounded_score score.</li>";
          // $rounded_score = round($array['Raw Score Increase'], 0, PHP_ROUND_HALF_UP);
          // $rounded_percentage = ceil($array['Percent Increase']);
          // $html_to_return .= "<li>$virtue_style +$rounded_score score. This is a $rounded_percentage% increase.</li>";
        }
        $html_to_return .= '</ul></div>';
      };

      //pull negative results
      if(metadata_exists( 'user', $user_id , 'survey-virtue-decreases' )){
        $negative_results = get_user_meta( $user_id, 'survey-virtue-decreases', true );
        $html_to_return .= '<div><h3>Your answers indicate the you have decreased in the following virtues since the last three surveys you took: </h3><ul>';
        foreach($negative_results as $virtue_name => $value){
          $uppercase_virtue = ucfirst($virtue_name);
          $rounded_score = round($value, 0, PHP_ROUND_HALF_UP);
          $html_to_return .= "<li>$uppercase_virtue -$rounded_score points.</li>";
        }
        $html_to_return .= '</ul></div>';
      };
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
    foreach($two_most_recent_results[0] as $virtue_name => $score_average){
      $previous_score_average = $two_most_recent_results[1][$virtue_name];
      $score_average_increase = $score_average - $previous_score_average;
      if($score_average_increase > 0.5){
        // Calculate percentage increase
        /** @see #NOTE_2 */
        // $perecent_increase = ($score_increase / $score) * 100;
        // // If greater than 50% store in array.
        // if($perecent_increase > 3) {
          // $increased_virtues[$virtue_name] = array('Percent Increase' => $perecent_increase, "Raw Score Increase" => $score_increase);
          $increased_virtues[$virtue_name] = $score_average_increase;
        // }
      }
    }
    // If there are increases update usermeta otherwise delete as
    // its no longer applicable
    if(!empty($increased_virtues)){
      update_user_meta( get_current_user_id(), 'survey-virtue-increases', $increased_virtues);
      return;
    }

    delete_user_meta( get_current_user_id(), 'survey-virtue-increases');
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

    // Iterate three times
    for($i = $survey_completions - 2; $i < $survey_completions + 1; $i++){
      $current_object = get_user_meta( get_current_user_id(), "user-virtue-survey-result-$i", true );
      $three_most_recent_results[] = $current_object->results;
    }
    $first_result = $three_most_recent_results[0];
    foreach($first_result as $virtue_name => $score){
      $second_score = $three_most_recent_results[1][$virtue_name];
      $third_score = $three_most_recent_results[2][$virtue_name];
      if($score > $second_score && $second_score > $third_score){
        /** @see #NOTE_2 */
        // $percentage_decrease = (($score - $third_score)/$score) * 100;
        $score_decrease = $score - $third_score;
        if($score_decrease >= .5){
          $decreased_virtues[$virtue_name] = $score_decrease;
          // $decreased_virtues[$virtue_name] = array('Percent Decrease'=> $percentage_decrease, 'Score Decrease' => $score_decrease);
        }
      }
    }
    // If there are decreases update usermeta otherwise delete as
    // its no longer applicable
    if(!empty($decreased_virtues)){
      update_user_meta( get_current_user_id(), 'survey-virtue-decreases', $decreased_virtues);
      return;
    }
    delete_user_meta( get_current_user_id(), 'survey-virtue-decreases');
  }
