
<div class="vs-admin-settings-wrapper">
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <h3>When uploading the filename of the survey will be automatically generated based on the current version number of the survey.</h3>
 <form id="uploadSurveyForm" onSubmit="return false" method="post" enctype="multipart/form-data">
   Select survey to upload:
   <input type="file" name="surveyToUpload" id="surveyToUpload">
   <input type="submit" value="Upload Survey" name="submit">
</form>
<div id="uploadError"></div>
<div id="uploadSucess">Your survey upload was a sucess!</div>
</div>
<?php
$js_version =  date("ymd-Gis", filemtime(   VIRTUE_SURVEY_FILE_PATH. 'assets/js/upload-virtue-survey.min.js'));
wp_enqueue_script( 'upload-virtue-survey', VIRTUE_SURVEY_FILE_PATH.'assets/js/upload-virtue-survey.min.js', array('jquery'), $js_version, true );
wp_localize_script( 'upload-virtue-survey', 'surveyUploadObject', array(
  'nonce' => wp_create_nonce('wp_rest'),
  'apiURL' => get_site_url()."/wp-json/vs_api/v1/upload-survey/",
));
