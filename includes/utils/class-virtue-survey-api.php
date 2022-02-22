<?php
/**
 * This REST API handles updates and settings for plugin.
 *
 */
class Virtue_Survey_API
{
  private $namespace;

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
    register_rest_route($this->namespace, '/upload-backups/',
      array(
        'methods' => 'POST',
        'callback' => array($this,'vs_upload_backup'),
        'permission_callback' => array($this, 'vs_plugin_permission_callback'),
      )
    );

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
   * Callback for uploading backups
   *
   * @return string|object
   */

  function vs_upload_backup(){
    // Get the current version number
    $version_number = get_option('current-vs-version');

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
      return wp_send_json_error("Whoops! There was an error uploading your file.", 400);
  }

  /**
   * Callback for updating definitions
   *
   * @return string|object
   */

    function vs_update_definitions(){
      if(isset($_POST['virtue'])){
        $virtue_to_update = $_POST['virtue'];
        update_option("vs-$virtue_to_update-definition", wp_kses($_POST['definitionContent']));
        return wp_send_json_success( "$virtue_to_update definition has been updated!" );
      }
        return wp_send_json_error( "There was an error updating $virtue_to_update's definition.", 400 );
    }

    /**
     * Callback for getting definitions
     *
     * @return string|object
     */
    function vs_get_definition($data){
      if(isset($data['virtue'])){
          $virtue_to_get = strtolower($data['virtue']);
          $definition_default = (get_option("vs-{$virtue_to_get}-definition") !== '' || get_option("vs-{$virtue_to_get}-definition") !== false) ? get_option("vs-{$virtue_to_get}-definition") : "Enter Definition Here";
          return wp_send_json_success( $definition_default );
      }
      return wp_send_json_error( "There was an error getting that virtues definition, please refresh the page and try again.", 400 );
    }
}
