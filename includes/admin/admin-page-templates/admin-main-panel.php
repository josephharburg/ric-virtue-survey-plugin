<?php ?>
<div class="vs-admin-settings-wrapper">
  <h1 class="vs-admin-h1"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <div class="vs-navigation">
    <div class="vs-navigation-wrapper">
        <div class="vs-nav-btn-container">
        <a href="<?php echo get_admin_url().'admin.php?page=virtue-definitions' ?>" class="vs-button-style">Virtue Definitions and Links</a>
        <div class="vs-nav-btn-desc">Change the virtue definitions and links to resources.</div>
      </div>
      <div class="vs-nav-btn-container">
        <a href="<?php echo get_admin_url().'admin.php?page=download-backups' ?>" class="vs-button-style">Get Previous Surveys</a>
        <div class="vs-nav-btn-desc">Download previous verison of the survey or uploaded entries.</div>
      </div>
      <div class="vs-nav-btn-container">
        <a href="<?php echo get_admin_url().'admin.php?page=upload-backups' ?>" class="vs-button-style">Upload Previous Surveys and Entries</a>
        <div class="vs-nav-btn-desc">Upload the currrent version of the survey or entries.</div>
      </div>
    </div>
  </div>
</div>
