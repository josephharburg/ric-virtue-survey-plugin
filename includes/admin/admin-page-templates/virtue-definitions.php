<?php
/**
 * This template handles the admin page for
 * updating the definitions of the virtues
 */

 ?>

<div class="vs-admin-settings-wrapper">
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <form id="updateDefinitions" onSubmit="return false" method="post" >
    Select survey to upload:
    <input type="file" name="surveyToUpload" id="surveyToUpload">
    <input type='hidden' name='upload_type' value='surveys'>
    <input type="submit" value="Upload Survey" name="submit">
  </form>
</div>

<?php
$js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_FILE_PATH. 'assets/js/update-definitions.min.js'));
wp_enqueue_script( 'update_definitions', VIRTUE_SURVEY_FILE_PATH.'assets/js/update-definitions.min.js', array('jquery'), $js_version, true );
wp_localize_script( 'update-definitions', 'definitionsDataObject', array(
  'nonce' => wp_create_nonce('wp_rest'),
  'apiURL' => get_site_url()."/wp-json/vs_api/v1/update-virtue-definitions/",
));
