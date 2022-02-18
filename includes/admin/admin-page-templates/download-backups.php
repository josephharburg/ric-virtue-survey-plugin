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
        $survey_upload_dir = $uploads_folder['basedir'] . '/virtue-survey/surveys';
        $uploaded_forms = scandir($survey_upload_dir);
        foreach($uploaded_forms as $key => $form){
         echo "<option value='$form'>$key</option>";
        }
        ob_end_clean();
        ?>
     </select>
    </form>
    <a id="formSurveyDownloadButton" href="<?php echo "$upload_dir/$uploaded_forms[0]" ?>">Download!</a>
  </div>
  <div id="entries-download-form">
    <h3>The list of available downloadable entries is below.</h3>
   <form id="downloadEntriesForm" onSubmit="return false" method="get">
     <label for="downloadEntriesDropdown">Select which entry file to download:</label>
     <select id="downloadEntriesDropdown">
       <?php
       ob_start();
        // $uploads_folder = wp_upload_dir();
        $entries_upload_dir = $uploads_folder['basedir'] . '/virtue-survey/entries';
        $uploaded_entries = scandir($entries_upload_dir);
        foreach($uploaded_entries as $key => $entries_file){
         echo "<option value='$entries_file'>$key</option>";
        }
        ob_end_clean();
        ?>
     </select>
   </form>
   <a id="formEntriesDownloadButton" href="<?php echo "$upload_dir/$uploaded_forms[0]" ?>">Download!</a>
  </div>
</div>

<script type="text/javascript">
  (function($){
    $('#vs-select-type').on('change', function(e){
      console.log($(this).val());
      var item = `#${$(this).val()}`;
      $('.active').hide();
      $('.active').removeClass('active');
      $(item).addClass('active');
      $('.active').show();
      }
    );

    $('#downloadSurveyDropdown').on('change', function($) {
      $('#formSurveyDownloadButton').attr('href',<?php echo wp_upload_dir()['basedir'] . '/virtue-survey/surveys/'; ?>$(this).val() );});

    $('#downloadEntriesDropdown').on('change', function($) {
      $('#formEntriesDownloadButton').attr('href',<?php echo wp_upload_dir()['basedir'] . '/virtue-survey/entries/'; ?>$(this).val() );
    } );
  })(jQuery)
</script>
