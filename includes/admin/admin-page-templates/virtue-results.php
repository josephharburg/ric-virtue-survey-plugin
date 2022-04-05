<?php
/**
 * This template handles the admin page for
 * updating the definitions of the virtues
 */
require_once VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/utils/virtue-survey-plugin-functions.php';
ob_start();
 ?>

<div class="vs-admin-settings-wrapper">
  <div class="formError" id="updateError"></div>
  <div class="formSuccess" id="updateSuccess"></div>
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <h2>Select a Virtue to update its result settings.</h2>
  <select id="virtueSelect">
    <?php
      $virtues = vs_get_virtue_list();
      foreach($virtues as $virtue){
        echo "<option value='$virtue'>".ucfirst($virtue)."</option>";
      }
    ?>
  </select>
    <small style="display:block">NOTE: To save the settings for either icon and|or definition you must click "Save [Virtue Name] Result" </small>
  <form id="updateVirtueResults" onsubmit="return false" method="post" >
    <div class="vs-image-upload">
      <?php
      $image_id = get_option('judgment-icon-id');
      if( $image = wp_get_attachment_image_src( $image_id ) ) {
    	echo '<a href="#" class="virtue-image-upl"><img id="currentVirtueImg" src="' . $image[0] . '" /></a>
    	      <a href="#" class="virtue-image-rmv vs-button-style">Remove Icon</a>
    	      <input id="virtue-image-id" type="hidden" name="virtue-img" value="' . $image_id . '">';
    } else {
    	echo '<a href="#" class="virtue-image-upl vs-button-style">Upload image for <span id="currentSelectedVirtue">Judgment</span></a>
    	      <a href="#" class="virtue-image-rmv vs-button-style" style="display:none">Remove Image</a>
    	      <input id="virtue-image-id" type="hidden" name="virtue-img" value="">';
    } ?>
    </div>
    <?php
    $default = get_option('vs-judgment-definition', "Enter Definition Here");
    wp_editor( $default, 'definitionContent', array());?>
    <input type="hidden" id="selectedVirtue" name="virtue" value="judgment">
    <input class="vs-button-style vs-space" type="submit" value="Save Judgment Result" name="submit">
  </form>
</div>

<?php
ob_end_flush();
$js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH. 'assets/js/update-virtue-results.min.js'));
wp_enqueue_script( 'update-virtue-results', VIRTUE_SURVEY_FILE_PATH.'assets/js/update-virtue-results.min.js', array('jquery'), $js_version, true );
wp_localize_script( 'update-virtue-results', 'resultsData', array(
  'nonce' => wp_create_nonce('wp_rest'),
  'apiURL' => get_site_url()."/wp-json/vs-api/v1/update-virtue-result/",
  'getVirtueResults' => get_site_url()."/wp-json/vs-api/v1/get-virtue-result/",
));
