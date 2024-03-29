<?php

class Virtue_Survey_Gravity_Forms_Integration
{
  function __construct(){
    require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
    add_action( 'gform_after_submission_11', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_action( 'gform_after_submission_12', array($this, 'vs_create_and_save_results'), 10, 2 );
    // add_action( 'gform_after_save_form', 'vs_form_saved_alerts', 10, 1);
//     add_filter( 'gform_form_settings_fields', function ( $fields, $form ) {
//     $fields['form_options']['fields'][] = array( 'type' => 'number', 'name' => 'version' );
//     return $fields;
// }, 10, 2 );
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

    if( !is_serialized( $virtue_result_object ) ) {
    $serialized_result = maybe_serialize($virtue_result_object);
    }

    /** @see #VS_STORAGE */
    // $_POST['input_135'] = $virtue_result_object->results['prudence'];

    // If user is logged in add it to their user meta.
    if(is_user_logged_in()){
      $user_id = get_current_user_id();
      // Get the number of completed surveys
      $survey_completions = get_user_meta( $user_id, "total-surveys-completed", true );

      /** @see #CALC_INC_DEC */
      if($survey_completions == ''){
        add_user_meta($user_id, "user-virtue-survey-result-1",$serialized_result, true);
        add_user_meta($user_id, "total-surveys-completed", 1, true);
      } else{
        $survey_completions++;
        add_user_meta($user_id, "user-virtue-survey-result-$survey_completions", $serialized_result, true);
        update_user_meta($user_id, "total-surveys-completed", $survey_completions, $survey_completions--);
        vs_calculate_and_save_increases($survey_completions);
        vs_calculate_and_save_decreases($survey_completions);
      }
    } else{
      $user_id = rgar($entry, 19);
      $user_results_meta_key = "$user_id-".rgar($entry,'id')."";
      set_transient($user_results_meta_key, $serialized_result, DAY_IN_SECONDS );
    }
  }

  /**
   * This alerts admins that the form has been changed and logs it
   *
   * @param  array $form
   * @return void
   */

  function vs_form_saved_alerts( $form ) {
      $log_file = VIRTUE_SURVEY_PLUGIN_DIR_PATH . '/assets/logs/gf_saved_forms.log';
      $f = fopen( $log_file, 'a' );
      $user = wp_get_current_user();
      fwrite( $f, date( 'c' ) . " - Form updated by {$user->user_login}. Form ID: {$form["id"]}. n" );
      fclose( $f );

      $old_version = get_option('current-vs-version');
      update_option('current-vs-version', $old_version + .1 );

      $to = array("jharburg@sistersofmary.org");
      $subject = "Alert: Someone has changed the virtue survey form";
      $headers = array('Content-Type: text/html; charset=UTF-8');
      $message = "Warning:<br><br>This is an alert to let you know that the virtue survey has been edited by {$user->user_login}.";
      wp_mail($to, $subject, $message, $headers);
  }
}
