<?php
/**
* This class handles all the gravity form customizations
*
* @package ric-virtue-survey-plugins
* @version 2.0
*/

class Virtue_Survey_Gravity_Forms_Integration
{
  function __construct(){
    /*())____________)_> GLOBAL FORM CHANGES  <_(____________(()   */
    add_filter( 'gform_validation_message', array($this,'vs_validation_error_message'), 10, 2 );
    add_filter( 'gform_field_value_return_code', array($this,'vs_add_return_code_to_hidden_field') );
    add_filter( 'gform_enable_legacy_markup', '__return_true' );

    /*())____________)_> YOUTH VERSION ONE  <_(____________(()   */
    add_filter( 'gform_pre_render_1', array($this,'vs_populate_return_code'),10, 1 );
    add_action( 'gform_after_submission_1', array($this, 'vs_save_return_code_data'), 10, 2 );
    add_action( 'gform_after_submission_2', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_action( 'gform_enqueue_scripts_1', array($this,'vs_enqueue_answer_key_script'), 10, 2 );
    add_action( 'gform_enqueue_scripts_2', array($this,'vs_enqueue_answer_key_script'), 10, 2 );
    add_action( 'gform_post_paging_1', array($this, 'vs_redirect_non_consenting_user'), 10, 3 );
    add_filter( 'gform_previous_button_1', array($this,'vs_previous_button_markup'), 10, 2 );
    add_filter( 'gform_previous_button_2', array($this,'vs_previous_button_markup'), 10, 2 );

    /*())____________)_> YOUTH VERSION TWO  <_(____________(()   */
    add_filter( 'gform_pre_render_22', array($this,'vs_populate_return_code'),10, 1 );
    add_action( 'gform_after_submission_22', array($this, 'vs_save_return_code_data'), 10, 2 );
    add_action( 'gform_after_submission_23', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_action( 'gform_enqueue_scripts_22', array($this,'vs_enqueue_answer_key_script'), 10, 2 );
    add_action( 'gform_enqueue_scripts_23', array($this,'vs_enqueue_answer_key_script'), 10, 2 );
    add_action( 'gform_post_paging_22', array($this, 'vs_redirect_non_consenting_user'), 10, 3 );
    add_filter( 'gform_previous_button_22', array($this,'vs_previous_button_markup'), 10, 2 );
    add_filter( 'gform_previous_button_23', array($this,'vs_previous_button_markup'), 10, 2 );


    ////__________________"o##o> Question Randomization <o##o"__________________//
    /////...................|______ Part One ______/....................//
    add_action( 'gform_pre_render_1', array($this,'vs_randomize_questions'),10, 2);
    add_filter( 'gform_pre_validation_1', array($this,'vs_pre_form_fields_validation'), 10, 1);
    add_action( 'gform_field_validation_1', array($this,'vs_post_form_fields_validation'), 10, 4 );
    add_action( 'gform_pre_render_22', array($this,'vs_randomize_questions'),10, 2);
    add_filter( 'gform_pre_validation_22', array($this,'vs_pre_form_fields_validation'), 10, 1);
    add_action( 'gform_field_validation_22', array($this,'vs_post_form_fields_validation'), 10, 4 );
    //This is required to turn off the randomization.
    // add_action( 'gform_field_validation_1_25', array($this,'vs_validate_code_saved'), 10, 4 );
    /////...................|______ Part Two ______/....................//
    add_action( 'gform_pre_render_2', array($this,'vs_randomize_questions'),10, 2 );
    add_filter( 'gform_pre_validation_2', array($this,'vs_pre_form_fields_validation'), 10, 1);
    add_action( 'gform_field_validation_2', array($this,'vs_post_form_fields_validation'), 10, 4 );
    add_action( 'gform_pre_render_23', array($this,'vs_randomize_questions'),10, 2 );
    add_filter( 'gform_pre_validation_23', array($this,'vs_pre_form_fields_validation'), 10, 1);
    add_action( 'gform_field_validation_23', array($this,'vs_post_form_fields_validation'), 15, 4 );

    /*())____________)_> ADULT VERSION ONE  <_(____________(()   */
    add_filter( 'gform_pre_render_20', array($this,'vs_populate_return_code'),10, 1 );
    add_action( 'gform_after_submission_20', array($this, 'vs_save_return_code_data'), 10, 2 );
    add_action( 'gform_field_validation_20_25', array($this,'vs_validate_code_saved'), 10, 4 );
    add_action( 'gform_after_submission_21', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_action( 'gform_enqueue_scripts_20', array($this,'vs_enqueue_answer_key_script'), 10, 2 );
    add_action( 'gform_enqueue_scripts_21', array($this,'vs_enqueue_answer_key_script'), 10, 2 );
    add_action( 'gform_post_paging_20', array($this, 'vs_redirect_non_consenting_user'), 10, 3 );

    /*())____________)_> ADULT VERSION TWO  <_(____________(()   */
    add_filter( 'gform_pre_render_24', array($this,'vs_populate_return_code'),10, 1 );
    add_action( 'gform_after_submission_24', array($this, 'vs_save_return_code_data'), 10, 2 );
    add_action( 'gform_field_validation_24_25', array($this,'vs_validate_code_saved'), 10, 4 );
    add_action( 'gform_after_submission_25', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_action( 'gform_enqueue_scripts_24', array($this,'vs_enqueue_answer_key_script'), 10, 2 );
    add_action( 'gform_enqueue_scripts_25', array($this,'vs_enqueue_answer_key_script'), 10, 2 );
    add_action( 'gform_post_paging_24', array($this, 'vs_redirect_non_consenting_user'), 10, 3 );

    ////__________________"o##o> Question Randomization <o##o"__________________//
    //////...................|______ Part One ______/....................//
    add_action( 'gform_pre_render_20', array($this,'vs_randomize_questions'),10, 2 );
    add_filter( 'gform_pre_validation_20', array($this,'vs_pre_form_fields_validation'), 10, 1);
    add_action( 'gform_pre_render_24', array($this,'vs_randomize_questions'),10, 2 );
    add_filter( 'gform_pre_validation_24', array($this,'vs_pre_form_fields_validation'), 10, 1);
    //////...................|______ Part Two ______/....................//
    add_action( 'gform_pre_render_21', array($this,'vs_randomize_questions'),10, 2 );
    add_filter( 'gform_pre_validation_21', array($this,'vs_pre_form_fields_validation'), 10, 1);
    add_action( 'gform_pre_render_25', array($this,'vs_randomize_questions'),10, 2 );
    add_filter( 'gform_pre_validation_25', array($this,'vs_pre_form_fields_validation'), 10, 1);

    /*())____________)_> GO TO PART TWO <_(____________(()   */
    add_action( 'gform_field_validation_18', array($this,'vs_validate_return_code_exists'), 10, 4 );
    add_action( 'gform_pre_submission_18', array($this, 'vs_update_matching_form_id'), 10, 1);
  }

  /**
   * Creates and Saves Return Code, Form ID, and next Form ID
   *
   * @see #RETURN_CODE_TRANSIENT
   *
   * @param  array|object $entry          The entry object from GF.
   * @param  array|object $form           The form object from GF.
   * @return void
   */

  function vs_save_return_code_data($entry, $form){
    $return_code = rgar($entry, 19);
    $data        = array('entry-id'=> rgar($entry, 'id'),'form-id' =>  $form['id'], 'next-form-id' => vs_get_correlated_form_id($form['id']));
    set_transient( "$return_code-data", $data, WEEK_IN_SECONDS*2 );
  }

  /**
   * Adds code into field value based on url parameter
   *
   * @param  mixed $form
   * @return array
   */

  function vs_update_matching_form_id($form){
    // Get the return code value from the post data
    $return_code = $_POST['input_1'];
    $matching_survey_data = get_transient( "$return_code-data");
    if($matching_survey_data){
      // Get the matching form id from transient data
      $matching_form_id = $matching_survey_data['next-form-id'];
    } else{
      // Use that return code to search entries with that return code
      // as the return code is unique to the entry it will return the
      // one we need
      $search_criteria['field_filters'][] = array( 'key' => '19', 'value' => $return_code );
      $matching_entry = GFAPI::get_entries( 0, $search_criteria);
      $entry_with_code = reset($matching_entry);
      // Get the form associated with the entry
      $previous_form_id = rgar($entry_with_code, 'form-id');
      // Use our matching function to get the matching form id
      $matching_form_id = vs_get_correlated_form_id($previous_form_id);
    }

    $_POST['input_3'] = $matching_form_id;
    return;
  }
  /**
   * Adds code into field value based on url parameter
   *
   * @param  mixed $value                Value from the field
   * @return string
   */


  function vs_add_return_code_to_hidden_field($value){
    return $_GET['return-code'];
  }

  /**
   * Validates that the return code exists
   *
   * @param  array $result               Current validation result object
   * @param  mixed $value                Value from the field
   * @param  array $form                 The form object
   * @param  mixed $field                The field object
   * @return array
   */

  function vs_validate_return_code_exists( $result, $value, $form, $field ) {
    $return_code_already_used = get_transient( "return-results-$value" );

    if($return_code_already_used){
      $result['is_valid'] = false;
      $result['message']  = 'That return code has already been used to take the second part!';
      return $result;
    }

    //Search for entries with that return code, if there arent any return false
    $search_criteria['field_filters'][] = array( 'key' => '19', 'value' => $value );
    $is_entry = GFAPI::get_entries( 0, $search_criteria);

    if ( $result['is_valid'] && !$is_entry ) {
        $result['is_valid'] = false;
        $result['message']  = 'That code doesnt appear to exist, please try again!';
    }

    return $result;
  }

  /**
   * Validates the code was written down by user
   *
   * @param  array $result               Current validation result object
   * @param  mixed $value                Value from the field
   * @param  array $form                 The form object
   * @param  mixed $field                The field object
   * @return array
   */

  function vs_validate_code_saved( $result, $value, $form, $field ) {
      $return_code = rgpost( 'input_19' );
      if ( $result['is_valid'] && $value !== $return_code ) {
          $result['is_valid'] = false;
          $result['message']  = 'That code doesnt match the code we gave you please try again!';
      }

      return $result;
  }

  /**
  * Method to create and/or display a random return code for user.
  *
  * @param array $form
  * @return array
  */

  function vs_populate_return_code($form){
    $current_page = GFFormDisplay::get_current_page( $form['id'] );

    if ( $current_page == 1 || $current_page == 2) {
      $letters = 'abcdefghijklmnopqrstuvwxyz';
      $rand_one = $letters[rand(0, 26)];
      $rand_two = $letters[rand(0, 26)];
      $rand_three = $letters[rand(0, 26)];
      $return_code = str_shuffle(rand(1000,10000).$rand_one.$rand_two.$rand_three);
       foreach ( $form['fields'] as &$field ) {
         if ( $field->id == 19 && $field->type != 'page' && empty(rgpost( 'input_19' ))) {
             $field->defaultValue = $return_code;
         }

         if ($field->id == 24 && $field->type != 'page') {
           if(rgpost( 'input_19' )){
            $return_code = rgpost( 'input_19' );
           }
           $field->content = "<div style='text-align:center;font-size:35px;'><h3>Welcome to the survey!</h3><span style='font-size: 35px;'>Your first task is to write down your return code.</span><br/> Your Return Code Is<br/><span class='put-return-code-here'> $return_code</span></div>";
         }
       }
     }
   return $form;
  }

/**
 * Creates and Saves Survey Results
 *
 * @see #VS_RESULT_OBJ
 * @param  array|object $entry          The entry object from GF.
 * @param  array|object $form           The form object from GF.
 */

  function vs_create_and_save_results($entry, $form){
    // Leave if no Virtue Survey Result class
    if(! class_exists('Virtue_Survey_Result')){
      exit;
    }
     $return_code = rgar($entry, 19);
     $virtue_result_object = new Virtue_Survey_Result($entry, $form, $return_code);
     /** @see #VS_STORAGE */
     // GFAPI::update_entry_field( $entry_id, 20, (string)$virtue_result_object->results['prudence'] );
     // GFAPI::update_entry_field( $entry_id, 21, (string)$virtue_result_object->results['justice'] );
     // GFAPI::update_entry_field( $entry_id, 22, (string)$virtue_result_object->results['temperance'] );

     // if(is_user_logged_in()){
     //   vs_save_results_to_usermeta(get_current_user_id(), $virtue_result_object);
     //   return;
     // }
     $user_results_meta_key = "return-results-$return_code";
     set_transient($user_results_meta_key, $virtue_result_object, MONTH_IN_SECONDS*2 );
  }

  /**
   * Modifys the error messages for skipped questions
   *
   * @param  string $message       The entry object from GF.
   * @param  array|object $form           The form object from GF.
   */

    function vs_validation_error_message($message, $form){
      return "<div class='validation_error'>" . esc_html__( "Oops! There's a problem! Please answer all the questions. ", 'gravityforms' ) . ' ' . esc_html__( 'Any you missed are highlighted below.', 'gravityforms' ) . '</div>';

    }

  /**
   * Modifys the error messages for skipped questions
   *
   *
   * @param  array|object $form           The form object from GF.
   * @param  string|int $source_page_number
   * @param  string|int $current_page_number
   */


    function vs_redirect_non_consenting_user($form, $source_page_number, $current_page_number){
      $disagreement = rgpost( 'input_30' );
      if($current_page_number == 2 && !empty($disagreement) && $disagreement == "No"){
        $endpoint_url = ($form['id'] == 1) ? "/youth" : '/adult';
        $url = get_site_url() . $endpoint_url;
        $script_to_redirect = "<script id='redirect-non-consent' type='text/javascript'>
        alert( 'Because you did not consent to the terms of agreement you are being redirected out of the survey.' );
        window.location.replace('$url');</script>";
        echo $script_to_redirect;
      }
      return;
    }


  /**
   * Enqueues the header answer key js
   *
   * @param  array|object $form      The entry object form GF.
   * @param  bool $is_ajax
   */

    function vs_enqueue_answer_key_script($form, $is_ajax){
      $pages_to_omit = array();
      if(in_array($form['id'], array(1,20,22,24))){$pages_to_omit = array(1,2,3);}
      if(in_array($current_page, $pages_to_omit)){return;}
      $current_page = GFFormDisplay::get_current_page( $form['id'] );
      $js_answer_key_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH.'assets/js/answer-key-header.js'));
      wp_enqueue_script( 'answer-key-header', VIRTUE_SURVEY_FILE_PATH.'assets/js/answer-key-header.js', array('jquery'), $js_answer_key_version,true);
    }

  /**
   * Hides Previous button for Youth Survey
   *
   * @param  string $previous_button
   * @param  array|object $form
   */

   function vs_previous_button_markup( $previous_button, $form ) {
     $page_number_to_hide = ($form['id'] == 1 || $form['id'] == 22)? 3 : 0;
     $current_page = GFFormDisplay::get_current_page( $form['id'] );
     if($current_page > $page_number_to_hide){
       $previous_button = '<div style="display:none;">' . $previous_button . '</div>';
     }

     return $previous_button;
   }

   /**
    * Randomize questions
    *
    * @param  array|object $form      The entry object form GF.
    * @param  bool $is_ajax
    */

   function vs_randomize_questions($form, $is_ajax){
       session_start();
       $form_id = $form['id'];
       $reordered_fields = array();
       $current_page = GFFormDisplay::get_current_page( $form['id'] );

       //If on page one create a unique session id and variable to retrieve that session id
       if($current_page == 1){
         $_SESSION["time-of-attempt-$form_id"] = date("H:i:s");
         $time_taken = $_SESSION["time-of-attempt-$form_id"];
         $_SESSION["survey-session-id-$form_id-$time_taken"] = rand();
       }

       //First three pages need to be ignored on part one
       if(in_array($form['id'], array(1,20,22,24))){
         if(in_array($current_page, array(1,2,3))){return $form;}
       }

       // ADDED THIS TO HANDLE PART TWO ERRORS 9-19-2022
       if(in_array($form_id, array(2,23))){
         $return_code = (!empty(rgpost( 'input_19' )))? rgpost( 'input_19' ) : $_GET['return-code'];
         $part_two_welcome = "<div style='text-align:center;font-size:35px;'><h3>Welcome to part two of the survey!</h3><span style='font-size: 25px;'>This is the second part for the return code: $return_code.</span><br/><span style='font-size: 25px;'>Click Next to begin!</span></div>";
         $field_id_to_change = ($form_id == 2)? 266: 281;
         foreach ( $form['fields'] as &$field ) {
           if($field->id ==  $field_id_to_change){
             $field->content = $part_two_welcome;
           }
         }
       }

       //Set form as already randomized so we get consistant form
       $time_taken_variable = $_SESSION["time-of-attempt-$form_id"];
       $survey_session = $_SESSION["survey-session-id-$form_id-$time_taken_variable"];
       if (isset($_SESSION["raw-form-fields-$survey_session-$form_id"])) {
         $form['fields'] = $_SESSION["raw-form-fields-$survey_session-$form_id"];
         return $form;
       }

       $reordered_fields = vs_randomize_field_order($form);
       $form['fields'] = $reordered_fields;
       $_SESSION["raw-form-fields-$survey_session-$form_id"] = $reordered_fields;

       return $form;
   }

   /**
    * Filters the gravity form fields for the survey
    *
    * The GForm Display uses this filter to allow for the form to be changed before
    * further validation and manipulation. Thus we change the fields to have the randomized order
    * that we created upon the first questions page rendering.
    *
    * @param  array|object $form
    *
    */

     function vs_pre_form_fields_validation($form){
       $form_id = $form['id'];
       //First three pages need to be ignored on part one
       if(in_array($form['id'], array(1,20,22,24))){
         $source_page = GFFormDisplay::get_source_page( $form['id'] );
         if(in_array($source_page, array(1,2))){return $form;}
       }

       session_start();
       $time_taken_variable = $_SESSION["time-of-attempt-$form_id"];
       $survey_session = $_SESSION["survey-session-id-$form_id-$time_taken_variable"];

       if(isset($_SESSION["raw-form-fields-$survey_session-$form_id"])){
         $form['fields'] = $_SESSION["raw-form-fields-$survey_session-$form_id"];
       }

       return $form;
     }

     /**
      * Validates the code was written down by user and makes radios are required
      *
      * @param  array $result               Current validation result object
      * @param  mixed $value                Value from the field
      * @param  array $form                 The form object
      * @param  mixed $field                The field object
      * @return array
      */

     function vs_post_form_fields_validation( $result, $value, $form, $field ) {
       $source_page = GFFormDisplay::get_source_page( $form['id'] );

       $result['is_valid'] = true;
       if(($form['id'] == 1 || $form['id'] == 22) && $source_page == 3){
           $return_code = rgpost( 'input_19' );
           if ( $result['is_valid'] && $value !== $return_code ) {
               $result['is_valid'] = false;
               $result['message']  = 'That code doesnt match the code we gave you please try again!';
           }
       }

       //make sure the radios are filled in!
       if (empty($value) && $field->type == 'radio' && in_array($form['id'], array(1,2,22,23))){
         $result['is_valid'] = false;
         $result['message']  = 'Please answer the question above.';
       }

       return $result;
     }



  /**
   * This alerts admins that the form has been changed and logs it
   *
   * @param  array $form
   * @return void
   */
  //
  // function vs_form_saved_alerts( $form ) {
  //     $log_file = VIRTUE_SURVEY_PLUGIN_DIR_PATH . '/assets/logs/gf_saved_forms.log';
  //     $f = fopen( $log_file, 'a' );
  //     $user = wp_get_current_user();
  //     fwrite( $f, date( 'c' ) . " - Form updated by {$user->user_login}. Form ID: {$form["id"]}. n" );
  //     fclose( $f );
  //
  //     $old_version = get_option('current-vs-version');
  //     update_option('current-vs-version', $old_version + .1 );
  //
  //     $to = array("jharburg@sistersofmary.org");
  //     $subject = "Alert: Someone has changed the virtue survey form";
  //     $headers = array('Content-Type: text/html; charset=UTF-8');
  //     $message = "Warning:<br><br>This is an alert to let you know that the virtue survey has been edited by {$user->user_login}.";
  //     wp_mail($to, $subject, $message, $headers);
  // }
}
