<?php

class Virtue_Survey_Gravity_Forms_Integration
{
  function __construct(){
    require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
    add_action( 'gform_after_submission_1', array($this, 'vs_save_return_code_and_form_id'), 10, 2 );
    add_action( 'gform_after_submission_3', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_action( 'gform_pre_submission_4', array($this, 'vs_update_matching_form_id'), 10, 1);
    add_filter( 'gform_pre_render_1', array($this,'vs_populate_return_code'),10, 1 );
    // add_action( 'gform_after_submission_2', array($this, 'vs_create_and_save_results'), 10, 2 );
    // add_filter( 'gform_pre_render_2', array($this,'vs_populate_return_code'),10, 1 );
    add_action( 'gform_field_validation_1_27', array($this,'vs_validate_code_saved'), 10, 4 );
    // add_action( 'gform_field_validation_2_27', array($this,'validate_return_code'), 10, 4 );
    add_filter( 'gform_field_value_return_code', array($this,'add_return_code_to_hidden_field') );
    // add_action( 'gform_after_save_form', 'vs_form_saved_alerts', 10, 1);
//     add_filter( 'gform_form_settings_fields', function ( $fields, $form ) {
//     $fields['form_options']['fields'][] = array( 'type' => 'number', 'name' => 'version' );
//     return $fields;
// }, 10, 2 );
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

  function vs_save_return_code_and_form_id($entry, $form){
    $return_code = rgar($entry, 19);
    $data        = array('entry-id'=> rgar($entry, 'id'),'form-id' =>  $form['id'], 'next-form-id' => vs_get_matching_form_id($form['id']));
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
      $matching_entry = GFAPI::get_entries( 1, $search_criteria);
      $entry_with_code = reset($matching_entry);
      // Get the form associated with the entry
      $previous_form_id = rgar($entry_with_code, 'form-id');
      // Use our matching function to get the matching form id
      $matching_form_id = vs_get_matching_form_id($previous_form_id);
    }

    $_POST['input_3'] = $matching_form_id;
    return;
  }
  /**
   * Adds code into field value based on url parameter
   *
   * @param  mixed $value                Value from the field
   * @return array
   */


  function add_return_code_to_hidden_field($value){
    return $_GET['return-code'];
  }

/**
 * Validates the code written into DERP THIS IS NOT HANDLED HERE ON FORM
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
  * Method to create a random return code for user.
  *
  * @param object $form
  *
  * @return boolean
  */

  function vs_populate_return_code($form){
    $current_page = GFFormDisplay::get_current_page( $form['id'] );

    if ( $current_page == 1 ) {
      $letters = 'abcdefghijklmnopqrstuvwxyz';
      $rand_one = $letters[rand(0, 26)];
      $rand_two = $letters[rand(0, 26)];
      $rand_three = $letters[rand(0, 26)];
      $return_code = str_shuffle(rand(1000,10000).$rand_one.$rand_two.$rand_three);
       foreach ( $form['fields'] as &$field ) {
         if ( $field->id == 19 && $field->type != 'page' && empty(rgpost( 'input_19' ))) {
             $field->defaultValue = $return_code;
         }

         if ($field->id == 25 && $field->type != 'page') {
           if(rgpost( 'input_19' )){
            $return_code = rgpost( 'input_19' );
           }
           $field->content = "<div>Welcome to the survey! Your first task is to write down this code!
<br> Your Code Is:$return_code</div>";
         }
       }
     }
   return $form;
  }

/**
 * Creates and Saves Survey Results
 *
 * @see #VS_RESULT_OBJ
 *
 * @param  array|object $entry          The entry object from GF.
 * @param  array|object $form           The form object from GF.
 * @return void
 */


  function vs_create_and_save_results($entry, $form){
    // Leave if no Virtue Survey Result class
    if(! class_exists('Virtue_Survey_Result')){
      exit;
    }


     $return_code = rgar($entry, 28);
     $virtue_result_object = new Virtue_Survey_Result($entry, $form, $return_code);
     /** @see #VS_STORAGE */
     // GFAPI::update_entry_field( $entry_id, 20, (string)$virtue_result_object->results['prudence'] );
     // GFAPI::update_entry_field( $entry_id, 21, (string)$virtue_result_object->results['justice'] );
     // GFAPI::update_entry_field( $entry_id, 22, (string)$virtue_result_object->results['temperance'] );
     $user_results_meta_key = "return-results-$return_code";
     set_transient($user_results_meta_key, $virtue_result_object, MONTH_IN_SECONDS*2 );
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
