<?php
/**
 * This template handles the admin page for
 * updating the definitions of the virtues
 */
ob_start();
 ?>

<div class="vs-admin-settings-wrapper">
  <div id="updateError"></div>
  <div id="updateSuccess"></div>
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <select id="virtueSelect">
    <?php
      $virtues = get_option('virtue_list');
      foreach($virtues as $virtue){
        echo "<option value='$virtue'>".ucfirst($virtue)."</option>";
      }
    ?>
  </select>
  <form id="updateDefinitionsForm" onSubmit="return false" method="post" >
    <label for="definition">Enter the definition</label>
    <?php
    $default = (get_option('vs_prudence_defintion') !== '' || get_option('vs_prudence_defintion') !== false) ? get_option('vs_prudence_defintion') : "Enter Definition Here";
    wp_editor( $default, 'definitionContent', array()); ?>
    <input type="hidden" id="selectedVirtue" name="virtue" value="prudence">
    <input type="submit" value="Edit Definition" name="submit">
  </form>
</div>

<?php
ob_end_flush();
$js_version =  date("ymd-Gis", filemtime( VIRTUE_SURVEY_FILE_PATH. 'assets/js/update-definitions.min.js'));
wp_enqueue_script( 'update_definitions', VIRTUE_SURVEY_FILE_PATH.'assets/js/update-definitions.min.js', array('jquery'), $js_version, true );
wp_localize_script( 'update-definitions', 'definitionsData', array(
  'nonce' => wp_create_nonce('wp_rest'),
  'apiURL' => get_site_url()."/wp-json/vs_api/v1/update-virtue-definitions/",
  'getVirtueDefinition' => get_site_url()."/wp-json/vs_api/v1/get-virtue-definition/",
));
