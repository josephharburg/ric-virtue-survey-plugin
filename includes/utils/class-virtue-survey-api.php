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
    $this->namespace = 'vs-api/v1';
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

  function vs_plugin_permission_callback(WP_REST_Request $request){
    if(!wp_verify_nonce( $request->get_header('X-WP-Nonce'), 'wp_rest' )){
      wp_send_json_error( "Nonce was not verified". var_dump($_POST), 403 );
      return false;
    }
    return true;
  }

  /**
   * Callback for uploading backups
   *
   * @return string|object
   */

  function vs_upload_backup(WP_REST_Request $request){
    $files = $request->get_file_params();
    $headers = $request->get_headers();
    // This is also the directory name

    if ( empty( $files ) || empty( $files['file'] ) ) {
      return wp_send_json_error("Whoops! There was an error uploading your file.", 400);
    }

    $file = $files['file'];
      // Get the current version number
    $version_number = get_option('current-vs-version');
    // return wp_send_json_error( "test" , 403 );
    // Get the uploads directory path
    // $uploads_folder = wp_upload_dir();
    $upload_type = $request->get_param('upload-type');
    $file_upload_type = ($upload_type == 'surveys') ? "survey" : $upload_type;
    $file_type_extension = ($upload_type == 'surveys') ? ".json" : ".csv";
    // The path to the custom plugin directory
    // $upload_dir = $uploads_folder['basedir'] . "/virtue-survey/$upload_type";
    $upload_date = date("Y-m-d");
      // Make sure the directory exists
      // if (is_dir($upload_dir)) {

        $file_name = "$file_upload_type-version-number-$version_number-$upload_date$file_type_extension";
        // THIS IS FOR LOCAL DEVELOPMENT
        $target_file = $_SERVER["DOCUMENT_ROOT"] . "/wp-content/uploads/virtue-survey/$upload_type/$file_name";
        // $target_file = $upload_dir . "/$file_name";

        // Check to see that file doesnt already exist.
        if (file_exists($target_file)) {
          return wp_send_json_error("$file_name backup already exists!", 400);
        }

        // Check to see file is larger than 1mb
        if ($file["size"] > 1000000) {
          return wp_send_json_error( "Sorry, the filesize is too large.", 400 );
        }

        //Try to move file into uploads directory or send error if applicable.
        if(move_uploaded_file($file["tmp_name"], $target_file)) {
        return wp_send_json_success( "The file ". htmlspecialchars($file_name). " has been uploaded to the virtue survey directory!", 201 ) ;
      }else{
        return wp_send_json_error($file["error"], 400);
      }
      // }
    // If the file upload does not make it through the validation send error
    // by default.
      return wp_send_json_error("Whoops! There was an error uploading your file.", 400);
  }

  /**
   * Callback for updating definitions
   *
   * @return string|object
   */

    function vs_update_definitions(WP_REST_Request $request){
      $virtue_to_update = $request->get_param('virtue');
      $definition = $request->get_param('definition');
      if(empty($virtue_to_update)){
        return wp_send_json_error( "There was an error updating $virtue_to_update's definition.", 400 );
      }
      if(!empty($definition)){
        update_option("vs-$virtue_to_update-definition", wp_kses($definition, 'post'));
        return wp_send_json_success( "<h2 style='color: #4BB543;'>The update to $virtue_to_update's definition has been sucesseful!</h2>", 200 );
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
          $definition_default = get_option("vs-{$virtue_to_get}-definition", "Enter Definition Here");
          return wp_send_json_success( $definition_default, 200 );
      }
      return wp_send_json_error( "There was an error getting that virtues definition, please refresh the page and try again.", 400 );
    }
}
