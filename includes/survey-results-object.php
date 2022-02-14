<?php
/*The Virtue Survey Result Object
*
* For every survey result, this is the object created.
* The object is capable of calculating and serving survey results as well as raw survey entries.
*
* @package RIC_Virtue_Survey_Plugin
* @access public
* @version 1.0
*/

if ( ! defined( 'ABSPATH' ) ) {

 exit; // Exit if accessed directly

}

class Virtue_Survey_Result
{
  public $entry = [];
  public $results = [];
  public $test_version = "1.0";

  function __construct($entry = array(), $test_version = "1.0")
  {
    $this->entry = $entry;
    $this->results = $this->calculate_results($entry);
    $this->test_version = get_option("ric-test-version");
  }

  public function calculate_results($entry){

    // $optional_question_ids = get_option('optional_questions');
    // $optional_questions= array();
    // foreach($optional_question_ids as $field_id){
    //   $optional_questions[] = rgar($entry, $field_id);
    // }

    //
    // $virtue_questions = array(get_option('prudence_question_ids'), get_option('justice_question_ids'), get_option('temperance_question_ids')...);
    // foreach($virtue_questions as $question_set){
    //   $current_virtue_name = $question_set['virtue_name'];
    //   foreach($question_set as $key => $field_id){
    //    if($key == 'virtue_name') continue;
    //    if(stipos($key, 'reverse') !== false){
    //    $current_virtue[] = rgar($entry, $field_id);
    // }
    //  }
    //  $current_virtue_calculation = ceil( array_sum($current_virtue) / count($current_virtue) );
    //  $calculated_survey_results[$current_virtue_name] = $current_virtue_calculation;
    // }
    $temperance = [rgar($entry,1), rgar($entry,8)];
    $temperance_average = ceil( array_sum($temperance) / count($temperance) );
    $prudence = [rgar($entry, 16),7 - rgar($entry, 4) ];
    $prudence_average = ceil( array_sum($prudence) / count($prudence) );
    $justice = [rgar($entry,15), 7 - rgar($entry,7)];
    $justice_average = ceil( array_sum($justice) / count($justice) );
    $array_of_ordered_values = ["temperance" => $temperance_average, "prudence" => $prudence_average, "justice" => $justice_average];
    arsort($array_of_ordered_values);
    return $array_of_ordered_values;
  }

  public function get_top_virtue(){
    return array_key_first($this->results);
  }

  public static function output_results_page($results = []){
    $html_to_return ="<div><ul>";
    $order = 1;
    foreach($results as $virtue => $value){
      $html_to_return .= "<li><span style='font-weight: bold'>$order</span>: $virtue </li>";
      $order++;
    }
    $html_to_return .="</ul></div>";
    return $html_to_return;
  }
}
