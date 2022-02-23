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
  <h2>Select a Virtue to update its definition.</h2>
  <select id="virtueSelect">
    <?php
      $virtues = vs_get_virtue_list();
      foreach($virtues as $virtue){
        echo "<option value='$virtue'>".ucfirst($virtue)."</option>";
      }
    ?>
  </select>
  <form id="updateDefinitionsForm" onSubmit="return false" method="post" >
    <?php
    $default = get_option('vs-prudence-definition', "Enter Definition Here");
    wp_editor( $default, 'definitionContent', array());?>
    <input type="hidden" id="selectedVirtue" name="virtue" value="prudence">
    <input class="vs-button-style vs-space" type="submit" value="Save Prudence Definition" name="submit">
  </form>
</div>

<?php
ob_end_flush();
$js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_PLUGIN_DIR_PATH. 'assets/js/update-definitions.js'));
wp_enqueue_script( 'update-definitions', VIRTUE_SURVEY_FILE_PATH.'assets/js/update-definitions.js', array('jquery'), $js_version, true );
wp_localize_script( 'update-definitions', 'definitionsData', array(
  'nonce' => wp_create_nonce('wp_rest'),
  'apiURL' => get_site_url()."/wp-json/vs-api/v1/update-virtue-definitions/",
  'getVirtueDefinition' => get_site_url()."/wp-json/vs-api/v1/get-virtue-definition/",
));
