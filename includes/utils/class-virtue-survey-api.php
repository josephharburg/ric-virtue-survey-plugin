<?php
/**
 * This REST API handles updates and settings for plugin.
 */
class Virtue_Survey_API
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
  register_rest_route($this->namespace, '/map-field-ids/',
    array(
      'methods' => 'POST',
      'callback' => array($this,'vs_map_field_ids'),
      'permission_callback' => array($this, 'vs_plugin_permission_callback'),
    )
  );
  register_rest_route($this->namespace, '/upload-backups/',
    array(
      'methods' => 'POST',
      'callback' => array($this,'vs_upload_backup'),
      'permission_callback' => array($this, 'vs_plugin_permission_callback'),
    )
  );

  // Add path to retrive previous survey versions (NOT NECESSARY)
  // register_rest_route($this->namespace, '/retrieve-stored-survey/',
  //   array(
  //     'methods' => 'GET',
  //     'callback' => array($this,'vs_retrieve_stored_survey'),
  //     'permission_callback' => array($this, 'vs_plugin_permission_callback'),
  //   ));

    register_rest_route($this->namespace, '/update-virtue-definitions/',
      array(
      'methods' => 'POST',
      'callback' => array($this,'vs_update_definitions'),
      'permission_callback' => array($this, 'vs_plugin_permission_callback'),
      )
    );

    register_rest_route($this->namespace, '/get-virtue-definition/',
      array(
      'methods' => 'GET',
      'callback' => array($this,'vs_get_definition'),
      'permission_callback' => array($this, 'vs_plugin_permission_callback'),
      )
    );

    register_rest_route($this->namespace, '/save-user-survey/',
      array(
      'methods' => 'POST',
      'callback' => array($this,'vs_save_user_survey'),
      'permission_callback' => array($this, 'vs_plugin_permission_callback'),
      )
    );
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

/**
 * Map Gravity Forms Field Ids to Questions
 * @return string
 */

function vs_map_field_ids(){
  // Everytime we update the field ids it should be represented
  // as a new version of the survey incremented by .1
  $new_version_number = get_option('current_vs_version') + 0.1;
  update_option('current_vs_version', $new_version_number);


}



function vs_upload_backup(){
  // Get the current version number
  $version_number = get_option('current_vs_version');

  // Get the uploads directory path
  $uploads_folder = wp_upload_dir();

  $directory_name = $_POST['upload_type'];
  $file_upload_type = ($_POST['upload_type'] == 'surveys') ? "survey" : $_POST['upload_type'];
  $file_type_extension = ($_POST['upload_type'] == 'surveys') ? ".json" : ".csv";
  // The path to the custom plugin directory
  $upload_dir = $uploads_folder['basedir'] . "/virtue-survey/$directory_name";
  $upload_date = date("Y-m-d H:i:s");
    // Make sure the directory exists
    if (is_dir($upload_dir)) {
      $file_name = "$file_upload_type-version-number-$version_number-$upload_date$file_type_extension";
      $target_file = $upload_dir . "/$file_name";

      // Check to see that file doesnt already exist.
      if (file_exists($target_file)) {
        return wp_send_json_error("$file_name backup already exists!");
      }

      // Check to see file is larger than 1mb
      if ($_FILES["{$file_upload_type}ToUpload"]["size"] > 1000000) {
        return wp_send_json_error( "Sorry, the filesize is too large." );
      }

      //Try to move file into uploads directory or send error if applicable.
      if (move_uploaded_file($_FILES["{$file_upload_type}ToUpload"]["tmp_name"], $target_file)) {
      return wp_send_json_success( "The file ". htmlspecialchars($file_name). " has been uploaded to the virtue survey directory!" ) ;
      }
    }
  // If the file upload does not make it through the validation send error
  // by default.
    return wp_send_json_error("Whoops! There was an error uploading your file.");
}

// Add callback to handle downloading previous versions (We might not need this)
// as we can just generate a list of files to download with scandir()
// function vs_retrieve_stored_survey($data){
//   $version_number = $_data['vsSurveyVersion'];
//   $uploads_directory = wp_upload_dir();
//   $retrieval_path = $uploads_directory['basedir'] . '/virtue-survey/';
//
//   $target_file = "{$retrieval_path}survey-version-number-$version_number.json";
//   $button_html = "<a class='vs-download-button' href='$target_file'>Download survey json file</a>";
//   if(file_exists($target_file)){
//   return wp_send_json_success( $button_html );
//   }
//   return wp_send_json_error( "Looks like that file " );
// }


// Add callback to update virtue definitions
  function vs_update_definitions(){

  }

// Add callback to update virtue definitions
  function vs_get_definition($data){
    if(isset($data['virtue'])){
        $virtue_to_get = strtolower($data['virtue']);
        $definition_default = (get_option("vs_{$virtue_to_get}_defintion") !== '' || get_option("vs_{$virtue_to_get}_defintion") !== false) ? get_option("vs_{$virtue_to_get}_defintion") : "Enter Definition Here";
        return wp_send_json_success( $definition_default );
    }
    return wp_send_json_error( "There was an error getting that virtues definition, please refresh the page and try again." );
  }

// Add callback for saving transient result after logging in or registering (Not sure if this is necessary).
  function vs_save_user_survey(){

  }
}
