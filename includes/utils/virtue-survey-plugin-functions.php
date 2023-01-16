<?php
/**
 * This file contains the plugins global functions.
 *
 * @package ric-virtue-survey-plugin
 */

 /**
  * Returns an the corresponding form id
  *
  * @param int|string $form_id
  * @return int
  */

   function vs_get_correlated_form_id($form_id){
     //20 and 21 are the adult version
      $mapped_form_id_matches = array(
      //Version one youth
       1 => 2,
       2 => 1,
       //Version Two Youth
       22 => 23,
       23 => 22,
       //Version one adult
       20 => 21,
       21 => 20,
       //Version two adult
       24 => 25,
       25 => 24
     );
     return $mapped_form_id_matches[(int)$form_id];
   }

   /**
    * Returns the parent virtue for styling
    *
    * @param string $virtue
    * @return array
    */

   function vs_get_parent_virtue($virtue){
     $fortitude = array('courage','industriousness','magnanimity','magnificence','patience','perseverance');
     $prudence = array('circumspection','docility','foresight', 'judgment');
     $temperance = array('honesty','humility','meekness','moderation','modesty','orderliness','self control','clemency',
     'studiousness','eutrapelia',);
     $justice = array('fairness','affability','courtesy','gratitude','generosity','kindness','loyalty','obedience','reverence','respect','responsibility','sincerity','trustworthiness',);
     switch ($virtue) {
       case in_array($virtue, $fortitude):
         $parent_virtue = 'fortitude';
         break;
       case in_array($virtue, $justice):
         $parent_virtue = 'justice';
         break;
       case in_array($virtue, $temperance):
         $parent_virtue = 'temperance';
         break;
       case in_array($virtue, $prudence):
         $parent_virtue = 'prudence';
         break;

       default:
         $parent_virtue = 'prudence';
         break;
     }
     return $parent_virtue;
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
        'self control',
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

  function vs_get_correlated_form($form_id){
    $matching_form_id = vs_get_correlated_form_id($form_id);
    $matching_form = array(
      'id' => $matching_form_id,
      'form'=> GFAPI::get_form($matching_form_id)
  );
    return $matching_form;
  }

  /**
   * Saves the result to users meta
   *
   * @param  int|string $user_id
   * @param object $result
   */


  function vs_save_results_to_usermeta($user_id, $result){
    // See if the user has any stored surveys
    $survey_completions = get_user_meta($user_id, "total-surveys-completed", true);
    if($survey_completions == '' || $survey_completions == false){
      add_user_meta($user_id, "user-virtue-survey-result-1",$result, true);
      add_user_meta($user_id, "total-surveys-completed", 1, true);
    } else{
      $survey_completions++;
      add_user_meta($user_id, "user-virtue-survey-result-$survey_completions", $result, true);
      update_user_meta($user_id, "total-surveys-completed", $survey_completions);
    }
  }

  /**
   * Returns an html table of survey results
   *
   * @param  array  $results required
   * @return string
   */

 function vs_create_results_html($results){
    $return_code = ($_GET['return-code'])?$_GET['return-code'] : $_POST['returnCode'];
    $virtue_tree_link_and_video = '<div style="text-align:center; text-decoration:underline;"><div class="no-print-video"style="padding:56.25% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/732594369?h=7b3ccf326a&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;" title="VSS - Ep 14 - Rough Cut v2"></iframe></div><script src="https://player.vimeo.com/api/player.js"></script><a class="no-print-video" href="https://openlightmedia.com/interactive-virtue-tree/" target="_blank">You can read more about these virtues and their definitions by clicking here.</a></div>';
    $html_to_return ="<div class='virtue-results-wrapper'><div id='results-page-header' ><h1 style='color: var(--primary-color);'>Virtue Survey Results</h1></div>$virtue_tree_link_and_video<div id='results-page-subheader' style='text-align:center;letter-spacing: 0.1rem;color: var(--primary-black-color);margin-bottom: 2rem;line-height:26pt;font-weight:400;'>Virtues are ranked in order by strongest to needing improvement.<br>The results below are for the return code: $return_code</div><h2 style='text-align: center;color: var(--primary-black-color);''>Your Top Three Virtues Are:</h2><ol>";
    foreach($results as $index => $virtue){
      $rank = $index + 1;
      $parent_virtue = vs_get_parent_virtue($virtue);

      //Emphasize the top three items
      $top_three = '';
      if(in_array($rank, array(1,2,3))){
        $top_three =" $parent_virtue-top-three-style";
      }

      //Rank html
      $ranked_number = "<span class='result-rank' style='border: 2px solid var(--$parent_virtue);color: var(--$parent_virtue); display:inline-block'>$rank</span>";

      //Get the virtue icon
      $virtue_icon =  wp_get_attachment_image_src( get_option("$virtue-icon-id", '95'), 'full' );

      // Description html
      // $html_to_return .= get_option('vs-'. $virtue .'-definition', 'definition here');

      $html_to_return .= "<li class='virtue-result $parent_virtue-style$top_three'><div class='result-card-top'>$ranked_number <img id='currentVirtueImg' src='$virtue_icon[0]'></div></li>";

      //Emphasize the top three items
      if($rank === 3){$html_to_return.= "</ol><hr><ol>";}

      //Make it printer friendly
      if($rank % 9 == 0 && $rank !== 1){
        $html_to_return .= "</ol><div style='page-break-before:always;'></div><ol>";
      }
    }

    $html_to_return .="</ol></div><div style='margin-top: 2rem;text-align:center;'><a href='/' class='button' style='margin-right:2rem;font-weight: 400;'><i class='ph-lg ph-house-light' style='vertical-align: -6px;'></i> Return Home</a><a class='button' onclick='window.print();return false;' class='button'style='background-color: var(--primary-black-color);font-weight: 400;'><i class='ph-lg ph-printer-light' style='vertical-align: -6px;'></i> Print Your Results</a></div>";

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
            //NOte added 5/19/22 for self control
            $virtue_first_five = str_replace(' ', '',$virtue);
            $virtue_first_five = substr($virtue_first_five, 0, 5);
            if(mb_stripos($admin_label, $virtue_first_five) !== false){
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
    $matching_form_id = vs_get_correlated_form_id($form_id);
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
         $current_virtue[] = (mb_stripos($field_key, 'neg') !== false) ? 8 - rgar($entry, $field_id) : rgar($entry, $field_id);
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
   * Collect and radomize the survey questions (fields)
   *
   * @see  #RANDOMIZATION_FUNCTION
   * @param  array|object $form
   *
   * @return array
   */

  function vs_randomize_field_order($form){
    $reordered_fields = array();
    //Gather all the fields that are questions
    foreach ( $form['fields'] as $index => &$field ) {
      if(mb_stripos($field->cssClass, 'fieldset-wrapper-grid')){
        $random_index_keys[] = $index;
      }
    }

    //Shuffle the random indexes
    $random_indexes = $random_index_keys;
    shuffle($random_indexes);

    foreach ( $form['fields'] as $index => &$field ) {
      //Set the page number if its a page
      if($field->type == 'page' ){
        $current_page_number = $field->pageNumber;
        $reordered_fields[$index] = $field;
        continue;
      }

      //Replace the current field with another one using the index
      if(in_array($index, $random_index_keys)){
         $random_index = array_pop($random_indexes);
         $form['fields'][$random_index]->pageNumber = $current_page_number;
         $reordered_fields[$index] = $form['fields'][$random_index];
      }else{
         $reordered_fields[$index] = $field;
      }
    }

    return $reordered_fields;
  }
