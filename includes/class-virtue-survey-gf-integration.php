<?php

class Virtue_Survey_Gravity_Forms_Integration
{
  function __construct(){
    require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
    add_action( 'gform_after_submission_1', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_filter( 'gform_pre_render_1', array($this,'vs_populate_user_id'),10, 1 );
    add_action( 'gform_after_submission_2', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_filter( 'gform_pre_render_2', array($this,'vs_populate_user_id'),10, 1 );
    add_action( 'gform_field_validation_1_27', array($this,'validate_return_code'), 10, 4 );
    // add_action( 'gform_after_save_form', 'vs_form_saved_alerts', 10, 1);
//     add_filter( 'gform_form_settings_fields', function ( $fields, $form ) {
//     $fields['form_options']['fields'][] = array( 'type' => 'number', 'name' => 'version' );
//     return $fields;
// }, 10, 2 );
  }

function validate_return_code ( $result, $value, $form, $field ) {
    $return_code = rgpost( 'input_19' );
    if ( $result['is_valid'] && $value == $master ) {
        $result['is_valid'] = false;
        $result['message']  = 'That doesnt match the code we gave you please try again!';
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

  function vs_populate_user_id($form){
    $current_page = GFFormDisplay::get_current_page( $form['id'] );

    if ( $current_page == 1 ) {
      $letters = 'abcdefghijklmnopqrstuvwxyz123456789';
      $rand_one = $letters[rand(0, 35)];
      $rand_two = $letters[rand(0, 35)];
      $rand_three = $letters[rand(0, 35)];
      $return_code = str_shuffle(rand(1000,10000).$rand_one.$rand_two.$rand_three);
       foreach ( $form['fields'] as &$field ) {
        //gather form data
         if ( $field->id == 19 && $field->type != 'page' ) {
           if(is_user_logged_in()){
             $field->defaultValue = get_current_user_id();
           } else{
             $field->defaultValue = $return_code;
           }
         }elseif ($field->id == 25 && $field->type != 'page') {
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

    $entry_id = rgar( $entry, 'id' );
    $form_id = $form['id'];
    $virtue_result_object = new Virtue_Survey_Result($entry_id, $form_id);

    /** @see #VS_STORAGE */
    GFAPI::update_entry_field( $entry_id, 20, (string)$virtue_result_object->results['prudence'] );
    GFAPI::update_entry_field( $entry_id, 21, (string)$virtue_result_object->results['justice'] );
    GFAPI::update_entry_field( $entry_id, 22, (string)$virtue_result_object->results['temperance'] );

    // If user is logged in add it to their user meta.
    if(is_user_logged_in()){
      $user_id = get_current_user_id();
      // Get the number of completed surveys
      $survey_completions = get_user_meta( $user_id, "total-surveys-completed", true );

      /** @see #CALC_INC_DEC */
      if($survey_completions == '' || $survey_completions == false){
        add_user_meta($user_id, "user-virtue-survey-result-1",$virtue_result_object, true);
        add_user_meta($user_id, "total-surveys-completed", 1, true);
      } else{
        $survey_completions++;
        add_user_meta($user_id, "user-virtue-survey-result-$survey_completions", $virtue_result_object, true);
        update_user_meta($user_id, "total-surveys-completed", $survey_completions);
        if($survey_completions > 1){
            vs_calculate_and_save_increases($survey_completions);
            if($survey_completions > 2){
              vs_calculate_and_save_decreases($survey_completions);
            }
        }

      }
    } else{
      $user_id = rgar($entry, 19);
      $user_results_meta_key = "$user_id-".rgar($entry,'id')."";
      set_transient($user_results_meta_key, $virtue_result_object, DAY_IN_SECONDS );
    }
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
