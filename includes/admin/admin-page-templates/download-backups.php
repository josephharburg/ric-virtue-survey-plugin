<div class="vs-admin-settings-wrapper">
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <h2>Select what you want to download:</h2>
  <select id="vs-select-type">
    <option value="survey-download-form">Survey</option>
    <option value="entries-download-form">Entry File</option>
  </select>
  <div class="active" id="survey-download-form">
    <h3>The list of available downloadable surveys is below.</h3>
    <form id="downloadSurveyForm" onSubmit="return false" method="get">
     <label for="downloadSurveyDropdown">Select survey to download:</label>
     <select id="downloadSurveyDropdown">
       <?php
       ob_start();
        $uploads_folder = wp_upload_dir();
        $survey_upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/virtue-survey/surveys';

        // $survey_upload_dir = $uploads_folder['basedir'] . '/virtue-survey/surveys';
        $uploaded_forms = scandir($survey_upload_dir);
        foreach($uploaded_forms as $key => $form){
          if($form !== '.' && $form !== '..'){
            echo "<option value='$form'>$form</option>";
          }

        }
        ob_end_flush();

        ?>
     </select>
    </form>
    <a class="vs-button-style" id="formSurveyDownloadButton" href="<?php echo get_site_url()."/wp-content/uploads/virtue-survey/entries/".$uploaded_entries[0]; ?>" download>Download!</a>
  </div>
  <div id="entries-download-form" style='display: none;'>
    <h3>The list of available downloadable entries is below.</h3>
   <form id="downloadEntriesForm" onSubmit="return false" method="get">
     <label for="downloadEntriesDropdown">Select which entry file to download:</label>
     <select id="downloadEntriesDropdown">
       <?php
       ob_start();
        $entries_upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/virtue-survey/entries';
        // $entries_upload_dir = $uploads_folder['basedir'] . '/virtue-survey/entries';
        $uploaded_entries = scandir($entries_upload_dir);
        foreach($uploaded_entries as $key => $entries_file){
          if($entries_file !== '.' && $entries_file !== '..'){
            echo "<option value='$entries_file'>$entries_file</option>";
          }
        }
        ob_end_flush();
        ?>
     </select>
   </form>
   <a class="vs-button-style" id="formEntriesDownloadButton" href="<?php echo get_site_url()."/wp-content/uploads/virtue-survey/entries/".$uploaded_entries[2] ?>" download>Download!</a>
  </div>
</div>

<script type="text/javascript">
  (function($){
    $('#vs-select-type').on('change', function(e){
      var item = `#${$(this).val()}`;
      $('.active').hide();
      $('.active').removeClass('active');
      $(item).addClass('active');
      $('.active').show();
      }
    );

    $('#downloadSurveyDropdown').on('change', function(e) {
      var file = $(this).val();
      $('#formSurveyDownloadButton').attr('href',`<?php echo get_site_url(). '/wp-content/uploads/virtue-survey/surveys/'; ?>${file}` );});

    $('#downloadEntriesDropdown').on('change', function($) {
      $('#formEntriesDownloadButton').attr('href',`<?php echo  get_site_url(). '/wp-content/uploads/virtue-survey/entries/'; ?>${file}`);
    } );
  })(jQuery)
</script>
