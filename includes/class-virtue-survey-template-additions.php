<?php

class Virtue_Survey_Page_Templates {

    /**
     * Templates folder inside the plugin
     */
    private $template_dir;

    /**
     * Templates to be merged with WP
     */
    private $templates;

    /**
     * Filtering and loading templates
     */
    public function __construct ( ) {
        $this->template_dir = VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/page-templates/';
        // $this->template_dir = VIRTUE_SURVEY_PLUGIN_DIR_PATH . 'includes/page-templates/';

        $this->templates = $this->vs_load_plugin_templates();

        add_filter('theme_page_templates', array($this, 'vs_register_plugin_templates'),10, 1);
        add_filter('template_include', array($this, 'vs_add_template_filter' ), 10, 1);
    }

    /**
     * Loading templates from the templates folder inside the plugin
     */
    private function vs_load_plugin_templates(){

        $template_dir = $this->template_dir;

        // Reads all templates from the folder
        if (is_dir($template_dir)) {
            if ($dh = opendir($template_dir)) {
                while (($file = readdir($dh)) !== false) {

                    $full_path = $template_dir . $file;

                    if (filetype($full_path) == 'dir') {
                        continue;
                    }

                    // Gets Template Name from the file
                    $filedata = get_file_data($full_path, array(
                        'Template Name' => 'Template Name',
                    ));

                    $template_name = $filedata['Template Name'];

                    $templates[$full_path] = $template_name;

                }
                closedir($dh);
            }
      }

      return $templates;
    }

    /**
     * theme_page_templates Filter callback
     *
     * Merges plugins' template with theme's, making them available for the user
     *
     * @param array $theme_templates
     * @return array $theme_templates
     */
    public function vs_register_plugin_templates ( $theme_templates ) {

        // Merging the WP templates with this plugin's active templates
        $theme_templates = array_merge($theme_templates, $this->templates);

        return $theme_templates;
    }

    /**
     * template_include Filter callback
     *
     * Include plugin's template if there's one chosen for the rendering page
     *
     * @param string $template path
     * @return string $template path
     */
     public function vs_add_template_filter( $template ) {

        $user_selected_template = get_page_template_slug($post->ID);

        // We need to check if the selected template
        // is inside the plugin folder
        $file_name = pathinfo($user_selected_template, PATHINFO_BASENAME);
        $template_dir = $this->template_dir;

        if (file_exists($template_dir . $file_name)) {
            $is_plugin = true;
        }
        // If selected template is not empty, it's not the Default Template
        // AND if it's a plugin template, we replace the normal flow to
        // include the selected template
        if ( $user_selected_template != '' AND $is_plugin ) {
            $template = $template_dir . $file_name;
        }

        return $template;
    }

}
