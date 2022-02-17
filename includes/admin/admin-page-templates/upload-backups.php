
<div class="vs-admin-settings-wrapper">
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <h3>When uploading the filename of the file will be automatically generated based on the current version number of the survey.</h3>
  <div id="uploadError"></div>
  <div id="uploadSuccess">Your survey upload was a sucess!</div>
  <h4>Upload Survey Backup</h4>
 <form id="uploadSurveyForm" onSubmit="return false" method="post" enctype="multipart/form-data">
   Select survey to upload:
   <input type="file" name="surveyToUpload" id="surveyToUpload">
   <input type='hidden' name='upload_type' value='surveys'>
   <input type="submit" value="Upload Survey" name="submit">
 </form>
<h4>Upload Entry Backup</h4>
 <form id="uploadEntryForm" onSubmit="return false" method="post" enctype="multipart/form-data">
   Select Entry to upload:
   <input type="file" name="entryToUpload" id="entryToUpload">
   <input type='hidden' name='upload_type' value='entries'>
   <input type="submit" value="Upload Entry" name="submit">
 </form>
</div>
<?php
$js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_FILE_PATH. 'assets/js/upload-backups.min.js'));
wp_enqueue_script( 'upload-backups', VIRTUE_SURVEY_FILE_PATH.'assets/js/upload-backups.min.js', array('jquery'), $js_version, true );
wp_localize_script( 'upload-backups', 'uploadDataObject', array(
  'nonce' => wp_create_nonce('wp_rest'),
  'apiURL' => get_site_url()."/wp-json/vs_api/v1/upload-backups/",
));
