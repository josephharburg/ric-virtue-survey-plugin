
<div class="vs-admin-settings-wrapper">
  <div class="formError" id="uploadError"></div>
  <div class="formSuccess" id="uploadSuccess">Your upload was a success!</div>
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <h3>When uploading the filename of the file will be automatically generated based on the current version number of the survey.</h3>
  <h2>Select what you want to upload:</h2>
  <select id="vs-select-type">
    <option value="survey-uploads-form">Survey</option>
    <option value="entries-uploads-form">Entry File</option>
  </select>
  <div class='active' id="survey-uploads-form">
    <h4>Upload Survey Backup</h4>
    <form id="uploadSurveyForm" onsubmit="return false" method="post" enctype="multipart/form-data">
     Select survey to upload:
     <input type="file" name="fileToUpload" id="surveyToUpload">
     <input type='hidden' name='upload-type' id="surveyUploadType" value='surveys'>
     <input type="submit" value="Upload Survey" name="submit">
     </form>
  </div>
  <div id="entries-uploads-form" style="display: none;">
   <h4>Upload Entry Backup</h4>
   <form id="uploadEntryForm" onsubmit="return false" method="post" enctype="multipart/form-data">
   Select Entry to upload:
   <input type="file" name="fileToUpload" id="entryToUpload">
   <input type='hidden' name='upload-type' value='entries'>
   <input type="submit" value="Upload Entry" name="submit">
   </form>
  </div>
</div>
<?php
$js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH. 'assets/js/upload-backups.js'));
wp_enqueue_script( 'upload-backups', VIRTUE_SURVEY_FILE_PATH.'assets/js/upload-backups.js', array('jquery'), $js_version, true );
wp_localize_script( 'upload-backups', 'uploadDataObject', array(
  'nonce' => wp_create_nonce('wp_rest'),
  'apiURL' => get_site_url()."/wp-json/vs-api/v1/upload-backups/",
));
