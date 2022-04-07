<?php
/**
* This class handles all the survey form actions and shortcodes
*
* @package ric-virtue-survey-plugins
* @version 1.0
*/
if ( ! defined( 'ABSPATH' ) ) 	exit; // Exit if accessed directly

class Virtue_Survey_Shortcodes
{
  function __construct(){
    add_shortcode( 'survey_results', array($this, 'vs_output_survey_results'));
    add_shortcode( 'random-survey-button', array($this, 'vs_ouput_random_survey_button') );
    add_shortcode( 'output_part_two', array($this, 'vs_ouput_survey_based_on_param') );
    add_shortcode( 'return_survey', array($this, 'vs_ouput_survey_based_on_param') );
  }

  /**
   * Shortcode to output results on results page
   *
   * @param  array $atts               shortcode attributes
   * @return string
   */

  public function vs_output_survey_results(){
    if(is_admin()){return;}
    if(is_user_logged_in()){
      $user_id =  get_current_user_id();
      if(!metadata_exists( 'user', $user_id, 'user-virtue-survey-result-1' )){
        return "<div>Please take a survey first to see results!</div>";
      }
        $survey_completions = get_user_meta( $user_id, "total-surveys-completed", true);
        $result_object = get_user_meta( $user_id, "user-virtue-survey-result-$survey_completions", true );
        return vs_create_results_html($result_object->get_ranked_virtues());
    }

    $return_code = $_GET['return-code'];
    $result_object = get_transient( "return-results-$return_code" );
    if(empty($result_object)){
      ob_start();
      ?>
      <div class="formError" id="surveyFormError"></div>
      <div style="display: block;text-align: center;padding: 5rem;border-radius: 8px;box-shadow: rgb(129 195 215 / 20%) 0px 7px 29px 0px;">
        <img src="http://development-playground.local/wp-content/uploads/2022/04/cardinal-virtues-accent-01.png" style="width: 20rem;margin-bottom: 2rem;">
         <h2 style="color: #393D3F;font-variant: all-small-caps;margin-bottom: 1rem;">Get Your Results</h2>
         <form id="getSurveyResultForm" onsubmit="return false" method="post" style="">
          <input type="text" name="returnCode" id="returnCode" required="" placeholder="Enter Your Return Code" style="border-radius: 8px;border-color: #393D3F;">
          <input id="get-result-button" class="vs-space" type="submit" value="Get Result">
        </form>
      </div>
    <?php
    ob_end_flush();
    $results_js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH. 'assets/js/get-survey-result.min.js'));
    wp_enqueue_script( 'get-survey-results', VIRTUE_SURVEY_FILE_PATH.'assets/js/get-survey-result.min.js', array('jquery'), $results_js_version, true );
    wp_localize_script( 'get-survey-results', 'surveyResults', array(
      'nonce' => wp_create_nonce('wp_rest'),
      'requestURL' => get_site_url()."/wp-json/vs-api/v1/get-survey-result/",
    ));
    return '';
    }
    ob_start();
    echo vs_create_results_html($result_object->get_ranked_virtues());
    if(!is_user_logged_in()){
      echo '<div class="alignfull" style="padding: 0 10%;"><h2 style="">Do you want to save these results in your user account?</h2><small>Login and Your Results will be saved automatically.</small></div>';
      wp_login_form(array('true') );
      wp_register_script( 'return-code-input', '', array("jquery"), '', true );
      wp_enqueue_script( 'return-code-input'  );
      wp_add_inline_script( 'return-code-input', "<script type='text/javascript'>jQuery(document).ready(function($){
        $('#loginform').prepend('<input type=hidden name=return-code value=$return_code>');
      });</script>");
      ob_end_flush();
    }
    return '';
  }

  /**
   * Outputs a random survey button
   *
   * @param  array $atts               shortcode attributes
   * @return string
   */

  function vs_ouput_random_survey_button($atts){
    session_start();
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );
    $shortcode_atts = shortcode_atts(array('retake' => 'false'), $atts);
    $button_style = "has-white-background-color has-text-color has-background survey-btn";
    // Set Session variable as array if not set
    if(!isset($_SESSION['surveys-taken'])){
      $_SESSION['surveys-taken'] = array();
    }

    // Add completed survey ID to Session Variable if on the results page
    // and restyle button accordingly
    if($shortcode_atts['retake'] == 'true'){
      $_SESSION['surveys-taken'][] = $_GET['formID'];
      $button_style = "has-text-color has-background retake-btn";
    }

    // Create array with 16 numbers representing survey numbers
    $available_surveys = array(1,5);
    foreach($available_surveys as $k=> $v){
      if(in_array($v, $_SESSION['surveys-taken'])){
        unset($available_surveys[$k]);
      }
    }
    $site_url = get_site_url();
    $random_key = array_rand($available_surveys,1);
    $button_text = ($shortcode_atts['retake'] == 'false') ? "Take Survey" : "Take Another Survey";
    $button_to_return = "<div class='wp-block-button'> <a class='survey-rdm-button wp-block-button__width-100 wp-block-button__link $button_style' href='$site_url/survey/?form-id={$available_surveys[$random_key]}'>$button_text</a></div>";
    return $button_to_return;
  }
  // /**
  //  * Outputs a random survey button
  //  *
  //  * @param  array $atts               shortcode attributes
  //  * @return string
  //  */
  //
  // function vs_ouput_random_survey_button($atts){
  //   session_start();
  //   $atts = array_change_key_case( (array) $atts, CASE_LOWER );
  //   $shortcode_atts = shortcode_atts(array('retake' => 'false'), $atts);
  //   $button_style = "has-white-background-color has-text-color has-background survey-btn";
  //   // Set Session variable as array if not set
  //   if(!isset($_SESSION['surveys-taken'])){
  //     $_SESSION['surveys-taken'] = array();
  //   }
  //
  //   // Add completed survey ID to Session Variable if on the results page
  //   // and restyle button accordingly
  //   if($shortcode_atts['retake'] == 'true'){
  //     $_SESSION['surveys-taken'][] = $_GET['formID'];
  //     $button_style = "has-text-color has-background retake-btn";
  //   }
  //
  //   // Create array with 16 numbers representing survey numbers
  //   $available_surveys = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
  //   foreach($available_surveys as $k=> $v){
  //     if(in_array($v, $_SESSION['surveys-taken'])){
  //       unset($available_surveys[$k]);
  //     }
  //   }
  //   $site_url = get_site_url();
  //   $random_key = array_rand($available_surveys,1);
  //   $button_text = ($shortcode_atts['retake'] == 'false') ? "Take Survey" : "Take Another Survey";
  //   $button_to_return = "<div class='wp-block-button'> <a class='survey-rdm-button wp-block-button__link $button_style' href='$site_url/survey-version-{$available_surveys[$random_key]}'>$button_text</a></div>";
  //   return $button_to_return;
  // }

  /**
   * Outputs a random survey button
   *
   * @see #OUTPUT_SURVEY_TWO_PAGE
   * @param  array $atts               shortcode attributes
   * @return string
   */

  function vs_ouput_survey_based_on_param($atts){
    if(!$_GET['form-id']){
      return "<div>Oops something broke. ¯\(°_o)/¯ <br/> Please click back and enter your code again.</div>";
    }

    return do_shortcode( '[gravityform id="'.$_GET['form-id'].'" title="false" description="false"]' );

  }

}
