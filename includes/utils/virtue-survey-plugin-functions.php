<?php
/**
 * This file contains the plugins global functions.
 *
 * @package ric-virtue-survey-plugin
 */

 /**
  * Enqueues script to update the take another survey button url
  *
  * @param int|string $form_id
  * @return int
  */

 function vs_enqueue_random_url(){
   if(is_front_page() || is_page('Survey Results')){
     $js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH. 'assets/js/get-random-survey-url.js'));
     wp_enqueue_script( 'return-random-url', VIRTUE_SURVEY_FILE_PATH.'assets/js/get-random-survey-url.js', array('jquery'), $js_version, true);
     wp_localize_script( 'return-random-url', 'randomSurvey',
     array(
     'nonce' => wp_create_nonce('wp_rest'),
     'ajaxURL' => get_site_url()."/wp-json/vs-api/v1/get-random-survey/",
     ) );
   }
 }
 add_action('wp_enqueue_scripts', 'vs_enqueue_random_url');

 /**
  * Returns an the corresponding form id
  *
  * @param int|string $form_id
  * @return int
  */

   function vs_get_matching_form_id($form_id){
      $mapped_form_id_matches = array(
       1 => 3,
       3 => 1
     );
     return $mapped_form_id_matches[(int)$form_id];
   }

  /**
   * Returns an array of the virtue names
   *
   * @return array
   */

  function vs_get_virtue_list(){
    return array(
        'judgment',
        'fairness',
        'courage',
        'affability',
        'courtesy',
        'gratitude',
        'generosity',
        'kindness',
        'loyalty',
        'obedience',
        'reverence',
        'respect',
        'responsibility',
        'sincerity',
        'trustworthiness',
        'circumspection',
        'docility',
        'foresight',
        'industriousness',
        'magnanimity',
        'magnificence',
        'patience',
        'perseverance',
        'honesty',
        'humility',
        'meekness',
        'moderation',
        'modesty',
        'orderliness',
        'self-control',
        'eutrapelia',
        'clemency',
        'studiousness'
    );
  }

  /**
   * Returns the complimentary survey form
   *
   * @return object
   */

  function vs_get_matching_form($form_id){
    $matching_form_id = vs_get_matching_form_id($form_id);
    $matching_form = array(
      'id' => $matching_form_id,
      'form'=> GFAPI::get_form($matching_form_id)
  );
    return $matching_form;
  }

  /**
   * Returns an html table of survey results
   *
   * @param  array  $results required
   * @return string
   */

 function vs_create_results_html($results){
    $html_to_return ="<div><h1 style='font-weight: 500;'>Virtue Survey Results</h1><h2>Below are your virtue survey results! <br/>They are ranked strongest to weakest.</h2><ol>";
    foreach($results as $virtue){
      $virtue_style = ucfirst($virtue);
      $virtue_icon =  wp_get_attachment_image_src( get_option("$virtue-icon-id", '') );
      $virtue_icon_html = (!empty($virtue_icon))? "<img id='currentVirtueImg' src='$virtue_icon[0]'>": '';
      $html_to_return .= "<li class='virtue-result $virtue-style'><span class='virtue-result-icon'>$virtue_icon_html</span><span style='font-weight: bold;position: relative;top: -1rem;text-transform: uppercase;'>$virtue_style</span> <br/><p>".get_option('vs-'. $virtue .'-definition')."</p></li>";
    }
    $html_to_return .="</ol></div>";

    // if(is_user_logged_in()){
    //   $user_id = get_current_user_id();
    //   //pull positive results
    //   if(metadata_exists( 'user', $user_id  , 'survey-virtue-increases' )){
    //     $positive_results = get_user_meta( $user_id, 'survey-virtue-increases', true );
    //     $html_to_return .= '<div><h3>Your answers indicate the you have grown in the following virtues since the last survey you took: </h3><ul style="list-style: none;font-weight:400">';
    //     foreach($positive_results as $virtue_name => $value){
    //       $virtue_style = ucfirst($virtue_name);
    //       $virtue_icon =  wp_get_attachment_image_src( get_option("$virtue_name-icon-id", '') );
    //       $virtue_icon_html = (!empty($virtue_icon))? "<img id='currentVirtueImg' src='$virtue_icon[0]'>": '';
    //       $rounded_score = round($value, 0, PHP_ROUND_HALF_UP);
    //       $html_to_return .= "<li class='$virtue_name-style'><span class='virtue-result-icon'>$virtue_icon_html</span> $virtue_style +$rounded_score score.</li>";
    //     }
    //     $html_to_return .= '</ul></div>';
    //   };
    //
    //   //pull negative results
    //   if(metadata_exists( 'user', $user_id , 'survey-virtue-decreases' )){
    //     $negative_results = get_user_meta( $user_id, 'survey-virtue-decreases', true );
    //     $html_to_return .= '<div><h3>Your answers indicate the you have decreased in the following virtues since the last three surveys you took: </h3><ul>';
    //     foreach($negative_results as $virtue_name => $value){
    //       $uppercase_virtue = ucfirst($virtue_name);
    //       $rounded_score = round($value, 0, PHP_ROUND_HALF_UP);
    //       $html_to_return .= "<li>$uppercase_virtue -$rounded_score points.</li>";
    //     }
    //     $html_to_return .= '</ul></div>';
    //   };
    // }

    return $html_to_return;
  }

  /**
   * Maps the field ids to the virtues dynamically
   *
   * @see #MAPPING_FIELDS
   * @param  object|array $form
   * @return array       a multidimensional array
   */

  function vs_map_field_ids_to_array($form,$mapped_fields_ids = array()){
      $virtue_list = vs_get_virtue_list();
      foreach ( $form['fields'] as $field ) {
        if(!empty($field->adminLabel)){
          $admin_label = $field->adminLabel;
          $field_id = $field->id;
          foreach($virtue_list as $virtue){
            $virtue_first_five = substr($virtue, 0, 5);
            if(stripos($admin_label, $virtue_first_five) !== false){
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
        // FIX THIS =>
        // $score_average_increase = $score_average_increase/7;
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

  /**
   * Create a results array with form and entry ids
   *
   * @see  #PUBLIC_CALC_LOOP
   * @param  int $entry_id
   * @param  int $form_id
   *
   * @return array
   */

  function vs_create_results_array(int $entry_id,int $form_id, $return_code){
    $entry_one = GFAPI::get_entry( $entry_id );
    $matching_form_id = vs_get_matching_form_id($form_id);
    $search_criteria['field_filters'][] = array( 'key' => '19', 'value' => $return_code );
    $matching_entry = GFAPI::get_entries( $matching_form_id, $search_criteria);
    $entry_two = reset($matching_entry);
    $both_entries = array($form_id => $entry_one, $matching_form_id => $entry_two);
    foreach($both_entries as $form_id => $entry){
      $current_form = GFAPI::get_form( $form_id );
      /** @see #MAPPING_FIELDS */
      $virtue_questions = vs_map_field_ids_to_array($current_form);
      foreach($virtue_questions as $current_virtue_name => $field_id_set){
        // Make SURE THE CURRENT VIRTUE ARRAY IS EMPTY DERPPPP!!! I cant believe I forgot to do this. (* ￣︿￣)
        $current_virtue = [];
        foreach($field_id_set as $field_key => $field_id){
        // If the key(admin_label) of the array has reverse in it make sure to do reverese calculation
         $current_virtue[] = (stripos($field_key, 'neg') !== false) ? 7 - rgar($entry, $field_id) : rgar($entry, $field_id);
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

  // /**
  //  * Decodes the return code
  //  *
  //  * @see  #DECODE_RETURN_CODE
  //  * @param  string $return_code
  //  *
  //  * @return array
  //  */
  //
  //   function vs_decode_return_code($return_code){
  //     $entry_id_index = strpos($return_code, "EID");
  //     $form_id_index =
  //   }
