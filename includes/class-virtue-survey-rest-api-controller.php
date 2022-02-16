<?php
/**
 * This REST API handles updates and settings for plugin.
 */
class Virtue_Survey_REST_API
{
  private $namespace = 'vs_api/v1';

  function __construct()
  {
    $this->namespace = 'vs_api/v1';
    add_action('rest_api_init', array($this, 'vs_register_plugin_routes'));
  }

/**
 * Registers the REST endpoints for plugin
 * @return void
 */

public function vs_register_plugin_routes(){
  // Add path to update Field Id Mapping by Virtue
  register_rest_route($this->namespace, '/map-field-ids/',
    array(
      'methods' => 'POST',
      'callback' => array($this,'vs_map_field_ids'),
      'permission_callback' => array($this, 'vs_plugin_permission_callback'),
    ));
  // Add path to upload survey versions
  //
  // Add path to retrive previous survey versions
  register_rest_route($this->namespace, '/retrieve-stored-survey/',
    array(
      'methods' => 'POST',
      'callback' => array($this,'vs_retrieve_stored_survey'),
      'permission_callback' => array($this, 'vs_plugin_permission_callback'),
    ));


  // Add path to Update Virtue Definition and Links (or have separate paths)?

  // Add path to add transient results to user meta
}

/**
* Handles the permission callback
*
* See the wordpress rest api documentation for more information.
* @return mixed
*/

function vs_plugin_permission_callback(){
  if(!wp_verify_nonce( $_POST['nonce'], 'wp_rest' )){
    wp_send_json_error( "Nonce was not verified", 403 );
    return false;
  }
  return true;
}

// Add callback to handle updating field ids for each virtue
function vs_map_field_ids(){
  // Everytime we update the field ids it should be represented
  // as a new version of the survey incremented by .1
  $new_version_number = get_option('current_vs_version') + 0.1;
  update_option('current_vs_version', $new_version_number);
}

///////////////////////////////////////////////////////////////////////////////
// Add callback to upload previous versions of survey.                       //
// I am thinking that there should be a separate uploads folder in           //
// the wp-uploads directory specifically for this.                           //
// The naming of these files needs to be consistant so that retrieving them  //
// later will be possible.                                                   //
///////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////
// <form action="none" onSubmit="return uploadSurvey()" method="post" enctype="multipart/form-data"> //
//   Select survey to upload:                                                  //
//   <input type="file" name="surveyToUpload" id="surveyToUpload">                //
//   <input type="submit" value="Upload Survey" name="submit">                 //
// </form>                                                                    //
////////////////////////////////////////////////////////////////////////////////
function vs_upload_survey(){
  $version_number = get_option('current_vs_version');

  $uploads_folder = wp_upload_dir();
  $upload_dir = $uploads_folder['basedir'] . '/virtue-survey';
  if (is_dir($upload_dir)) {
    $target_file = $upload_dir . basename($_FILES["surveyToUpload"]["name"]."_$version_number");

    // Check to see that file doesnt already exist.
    if (file_exists($target_file)) {
      echo "This survey already exists";
      return wp_send_json_error("This version of survey already exists!");
    }

    // Check to see file is larger than 1mb
    if ($_FILES["surveyToUpload"]["size"] > 1000000) {
      echo "Sorry, the filesize is too large.";
      return wp_send_json_error( "Sorry, the filesize is too large." );
    }

    //Try to move file into uploads directory or send error if applicable.
    if (move_uploaded_file($_FILES["surveyToUpload"]["tmp_name"], $target_file)) {
    return wp_send_json_success( "The file ". htmlspecialchars( basename( $_FILES["surveyToUpload"]["name"])). " has been uploaded to the virtue survey directory!" ) ;
    }
  }
  // If the file upload does not make it through the validation send error
  // by default.
    return wp_send_json_error("Whoops! There was an error uploading your file.");
}

// Add callback to handle downloading previous versions
function vs_retrieve_stored_survey(){
  $version_number = $_POST['vs_survey_version'];

}


// Add callback to update virtue definitions and links.

// Add callback for saving transient result after logging in or registerin

}
