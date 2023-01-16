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
    add_shortcode( 'output_survey', array($this, 'vs_ouput_survey_based_on_param') );
    add_shortcode( 'example_survey_results', array($this, 'vs_output_example_survey_results'));
  }

  /**
   * Shortcode to output results on results page
   *
   * @see #RESULTS_SHORTCODE
   * @param  array $atts               shortcode attributes
   * @return string
   */

  public function vs_output_survey_results(){
    if(is_admin()){return 'Hello';}
    // if(is_user_logged_in()){
    //   $user_id = get_current_user_id();
    //   if(metadata_exists( 'user', $user_id, 'user-virtue-survey-result-1' )){
    //     $survey_completions = get_user_meta( $user_id, "total-surveys-completed", true);
    //     $result_object = get_user_meta( $user_id, "user-virtue-survey-result-$survey_completions", true );
    //     return vs_create_results_html($result_object->get_ranked_virtues());
    //   }
    // }

    // Get return code from URL Parameter and retrieve result object
    $return_code = $_GET['return-code'];
    $result_object = get_transient( "return-results-$return_code" );
    
    // Make sure we have a valid return code param and results arent expired.
    // This will also run if it is just a plain old request to the results page
    // and user doesnt have any saved surveys.
    if(empty($result_object) || empty($return_code)){
      // Display form to retrieve results from return code
      ob_start();
      echo '<div class="formError" id="surveyFormError" style="margin-top: 15vh;"></div>
      <div style="display: block;text-align: center;padding: 5rem;border-radius: 8px;box-shadow: rgb(129 195 215 / 20%) 0px 7px 29px 0px;">
        <img src="https://restoredinchrist.openlightmedia.com/wp-content/uploads/2022/05/cardinal-virtues-accent-01.png" style="width: 20rem;margin-bottom: 2rem;">
         <h2 style="color: #393D3F;font-variant: all-small-caps;margin-bottom: 1rem;">Get Your Results</h2>
         <form id="getSurveyResultForm" onsubmit="return false" method="post" style="">
          <input type="text" name="returnCode" id="returnCode" required="" placeholder="Enter Your Return Code" style="border-radius: 8px;border-color: #393D3F;">
          <input id="get-result-button" class="vs-space" type="submit" value="Get Result">
        </form>
      </div>';
      ob_end_flush();
      $results_js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH. 'assets/js/get-survey-result.min.js'));
      wp_enqueue_script( 'get-survey-results', VIRTUE_SURVEY_FILE_PATH.'assets/js/get-survey-result.min.js', array('jquery'), $results_js_version, true );
      wp_localize_script( 'get-survey-results', 'surveyResults', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'requestURL' => get_site_url()."/wp-json/vs-api/v1/get-survey-result/",
      ));
    }else{
      ob_start();
      echo vs_create_results_html($result_object->get_ranked_virtues());
//       if(!is_user_logged_in()){
//           echo '<div class="alignfull" style="padding: 0 10%;"><h2 style="">Do you want to save these results in your user account?</h2><small>Login and Your Results will be saved automatically.</small></div>';
//           wp_login_form(array('true') );
//           echo '<div><form id="register" name="registerform" method="post" action="http://development-playground.local/wp-login.php?action=register" style="display: grid;grid-template-columns: repeat(2,1fr);">
//     <div><label style="display: block;">Email</label>
//     <input type="email" placeholder="example@email.com" name="user_login" style="display: block;" required="">
//     <label style="display: block;">First Name</label>
//     <input type="text" name="first_name">
//     <label style="display: block;">Last Name</label>
//     <input type="text" name="last_name">
//         </div>
//     <div><label style="display: block;">Password</label>
//    <input type="password" name="user_pass" style="" required="">
//      <label style="display: block;">Confirm Password</label>
// <input type="password" name="user_pass_conf" style="" required=""></div><input type="submit" value="submit"/></form></div>';
//           wp_register_script( 'return-code-input', '', array("jquery"), '', true );
//           wp_enqueue_script( 'return-code-input'  );
//           wp_add_inline_script( 'return-code-input', "<script type='text/javascript'>jQuery(document).ready(function($){
//             $('#loginform,#register').prepend('<input type=hidden name=return-code value=$return_code>');
//           });</script>");
//       }
      ob_end_flush();
    }
    return '';
  }

  /**
   * Shortcode to output results on results page
   *
   */

  public function vs_output_example_survey_results(){
    if(is_admin()){return 'Hello';}

    // Get return code from URL Parameter and retrieve result object
    $return_code = 'cz179m3';
    $result_object = array_keys(vs_create_results_array(209, 21, $return_code ));
    // var_dump($result_object);
      ob_start();
      echo vs_create_results_html($result_object);
      ob_end_flush();
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
    $available_surveys = array(1);
    foreach($available_surveys as $k=> $v){
      if(in_array($v, $_SESSION['surveys-taken'])){
        unset($available_surveys[$k]);
      }
    }
    $site_url = get_site_url();
    $random_key = array_rand($available_surveys,1);
    $button_text = ($shortcode_atts['retake'] == 'false') ? "Take Survey" : "Take Another Survey";
    $button_to_return = "<div class='wp-block-button'> <a class='survey-rdm-button wp-block-button__width-100 wp-block-button__link $button_style' href='$site_url/take-part-one/?form-id={$available_surveys[$random_key]}'>$button_text</a></div>";
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
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );
    $shortcode_atts = shortcode_atts(array('part-one' => 'false'), $atts);
    if(!$_GET['form-id'] && !$shortcode_atts['part-one']){
      return "<div style='text-align:center;'>Oops something went wrong.<br/> Please click back and enter your code again.</div>";
    } elseif(!$_GET['form-id'] && $shortcode_atts['part-one']){
        return "<div style='text-align:center;'>Oops something went wrong.<br/> Please click back and click start again please!.</div>";
    }

    return do_shortcode( '[gravityform id="'.$_GET['form-id'].'" title="false" description="false"]' );

  }

}
