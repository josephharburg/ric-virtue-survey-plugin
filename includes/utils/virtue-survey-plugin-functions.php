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

 function vs_output_results_table($results = []){
    $html_to_return ="<div><ul>";
    $rank = 1;
    foreach($results as $virtue){
      $html_to_return .= "<li><span style='font-weight: bold'>$rank. $virtue</span> <br/>".get_option('vs-'. $virtue .'-definition')."  </li>";
      $rank++;
    }
    $html_to_return .="</ul></div>";
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
