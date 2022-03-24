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
    $this->namespace = 'vs-api/v1/';
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

    register_rest_route($this->namespace, '/update-virtue-result/',
        array(
        'methods' => 'POST',
        'callback' => array($this,'vs_update_virtue_result'),
        'permission_callback' => array($this, 'vs_plugin_permission_callback'),
        )
      );

    register_rest_route($this->namespace, '/get-virtue-result/',
        array(
        'methods' => 'GET',
        'callback' => array($this,'vs_get_virtue_result'),
        'permission_callback' => array($this, 'vs_plugin_permission_callback'),
        )
      );

    register_rest_route($this->namespace, '/get-random-survey/',
        array(
        'methods' => 'POST',
        'callback' => array($this,'vs_generate_random_url'),
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

    function vs_update_virtue_result(WP_REST_Request $request){
      $virtue_to_update = $request->get_param('virtue');
      $definition = $request->get_param('definition');
      $image_id = $request->get_param('imageID');

      if(empty($virtue_to_update)){
        return wp_send_json_error( "There was an error updating $virtue_to_update's definition.", 400 );
      }

      if(empty($definition) && empty($image_id)){
        return wp_send_json_error( "There was an error updating $virtue_to_update's definition.", 400 );
      }

      if(!empty($definition)){
        update_option("vs-$virtue_to_update-definition", wp_kses($definition, 'post'));
      }

      if(!empty($image_id)){
        update_option("$virtue_to_update-icon-id", wp_kses($image_id, 'post'));
      }

      return wp_send_json_success( "<h2 style='color: white;'>The update to $virtue_to_update's definition has been sucesseful!</h2>", 200 );
    }

    /**
     * Callback for getting definitions
     *
     * @return string|object
     */
    function vs_get_virtue_result($data){
      if(isset($data['virtue'])){
          $virtue_to_get = strtolower($data['virtue']);
          $definition_default = get_option("vs-{$virtue_to_get}-definition", "Enter Definition Here");
          $image_id = get_option("$virtue_to_get-icon-id", '');
          $image = wp_get_attachment_image_src( $image_id );
          $image_URL = ($image)?  $image[0] : '';
          return wp_send_json_success( array('definition' => $definition_default, 'imgURL' => $image[0]), 200 );
      }
      return wp_send_json_error( "There was an error getting that virtues definition, please refresh the page and try again.", 400 );
    }


    /**
     * Create a randomly generated url for the take survey button.
     *
     */
    function vs_generate_random_url($data){
      session_start();
      $available_surveys = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);

      // Set Session variable as array if not set
      if(!isset($_SESSION['surveys-taken'])){
        $_SESSION['surveys-taken'] = array();
      }

      // Add completed survey ID to Session Variable if on the results page
      if($data['retake'] == 'YES'){
        if(!in_array($data['formID'], $_SESSION['surveys-taken'])){
          $_SESSION['surveys-taken'][] = $data['formID'];
        }
      }

      // Create array with 16 numbers representing survey numbers
      if(!empty($_SESSION['surveys-taken'])){
        foreach($available_surveys as $k=> $v){
          if(in_array($v, $_SESSION['surveys-taken'])){
            unset($available_surveys[$k]);
          }
        }
      }
      $site_url = get_site_url();
      $shuffled = $available_surveys;
      shuffle($shuffled);
      $random_key = array_rand($available_surveys);
      $url_to_return = "$site_url/survey-version-{$available_surveys[$random_key]}";
      return wp_send_json_success($url_to_return , 200);
    }
}
