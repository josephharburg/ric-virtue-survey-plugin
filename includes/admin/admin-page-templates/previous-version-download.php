<div class="vs-admin-settings-wrapper">
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <h3>The list of available downloadable surveys is below</h3>
 <form id="downloadSurveyForm" onSubmit="return false" method="get">
   <label for="downloadDropdown">Select survey to download:</label>
   <select id="downloadDropdown">
     <?php
     ob_start();
      $uploads_folder = wp_upload_dir();
      $upload_dir = $uploads_folder['basedir'] . '/virtue-survey';
      $uploaded_forms = scandir($upload_dir);
      foreach($uploadedforms as $key => $form){
       echo "<option value='$form'>$key</option>";
      }
      ob_get_clean();
      ?>
   </select>
</form>
  <a id="formDownloadButton" href="<?php echo "$upload_dir/$uploaded_forms[0]" ?>">Download the survey!</a>
</div>

<script type="text/javascript">
  (function($){
    $('#downloadDropdown').on('change', function($) {
      $('#formDownloadButton').attr('href',<?php echo wp_upload_dir()['basedir'] . '/virtue-survey/'; ?>$(this).val() );
    } );
  })(jQuery)

</script>
