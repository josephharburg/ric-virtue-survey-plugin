<?php

class Virtue_Survey_Gravity_Forms_Integration
{
  function __construct(){
    require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
    add_action( 'gform_after_submission_1', array($this, 'vs_create_and_save_results'), 10, 2 );
    add_filter( 'gform_pre_render_1', array($this,'vs_populate_return_code'),10, 1 );
    // add_action( 'gform_after_submission_2', array($this, 'vs_create_and_save_results'), 10, 2 );
    // add_filter( 'gform_pre_render_2', array($this,'vs_populate_return_code'),10, 1 );
    add_action( 'gform_field_validation_1_27', array($this,'validate_return_code'), 10, 4 );
    // add_filter( 'gform_field_value_return_code', array($this,'my_custom_population_function') );
    // add_action( 'gform_after_save_form', 'vs_form_saved_alerts', 10, 1);
//     add_filter( 'gform_form_settings_fields', function ( $fields, $form ) {
//     $fields['form_options']['fields'][] = array( 'type' => 'number', 'name' => 'version' );
//     return $fields;
// }, 10, 2 );
  }

  // function my_custom_population_function( $value ) {
  //   $letters = 'abcdefghijklmnopqrstuvwxyz';
  //   $rand_one = $letters[rand(0, 26)];
  //   $rand_two = $letters[rand(0, 26)];
  //   $rand_three = $letters[rand(0, 26)];
  //   $return_code = str_shuffle(rand(1000,10000).$rand_one.$rand_two.$rand_three);
  //   return  $return_code;
  // }

function validate_return_code ( $result, $value, $form, $field ) {
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
    /** @see #VS_STORAGE */
    // GFAPI::update_entry_field( $entry_id, 20, (string)$virtue_result_object->results['prudence'] );
    // GFAPI::update_entry_field( $entry_id, 21, (string)$virtue_result_object->results['justice'] );
    // GFAPI::update_entry_field( $entry_id, 22, (string)$virtue_result_object->results['temperance'] );

     $return_code = rgar($entry, 19);
     // $entry_id = rgar( $entry, 'id' );
     // $form_id = $form['id'];
     // $virtue_result_object = new Virtue_Survey_Result($entry_id, $form_id, $return_code);
     $virtue_result_object = new Virtue_Survey_Result($entry, $form, $return_code);
     $user_results_meta_key = "return-results-$return_code";
     set_transient($user_results_meta_key, $virtue_result_object, DAY_IN_SECONDS );
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
