<?php
namespace MyPlugin;

class TemplateManager {
    public function __construct() {
        add_action('init', [$this, 'handle_template_creation']);
    }

    public function handle_template_creation() {
        // Check if form is submitted
        if (isset($_POST['template_name']) && current_user_can('edit_themes')) {
            // Sanitize user input
            $template_name = sanitize_file_name($_POST['template_name']);
            $js_file = sanitize_file_name($_POST['js_file']);
            $css_file = sanitize_file_name($_POST['css_file']);
            $template_dir = get_stylesheet_directory();
    
            // Paths for the files
            $template_path = "{$template_dir}/{$template_name}.php";
            $js_path = "{$template_dir}/{$js_file}";
            $css_path = "{$template_dir}/{$css_file}";
    
            // Create template file if it doesn't exist
            if (!file_exists($template_path)) {
                $header = "<?php\n/*\nTemplate Name: $template_name\n*/\n";
                $content = "$header\nget_header();\n?>\n\n<h1>$template_name</h1>\n\n<?php\nget_footer();";
                file_put_contents($template_path, $content);
                chmod($template_path, 0777);
            }
    
            // Create JS file if it doesn't exist
            if (!empty($js_file) && !file_exists($js_path)) {
                file_put_contents($js_path, "// JS for $template_name");
                chmod($js_path, 0777);
            }
    
            // Create CSS file if it doesn't exist
            if (!empty($css_file) && !file_exists($css_path)) {
                file_put_contents($css_path, "/* CSS for $template_name */");
                chmod($css_path, 0777);
            }
    
            // Append enqueue code to functions.php if necessary
            $this->append_enqueue_code($template_name, $js_file, $css_file);
        }
    }
    
    private function append_enqueue_code($template_name, $js_file, $css_file) {
        $functions_php_path = get_stylesheet_directory() . '/functions.php';
    
        // Check if functions.php is writable
        if (!file_exists($functions_php_path) || !is_writable($functions_php_path)) {
            error_log("functions.php does not exist or is not writable.");
            return;
        }
    
        $current_code = file_get_contents($functions_php_path);
    
        // Enqueue code block
        $enqueue_code = "\n// Enqueue JS and CSS for template: $template_name\n";
        $enqueue_code .= "if (is_page_template('{$template_name}.php')) {\n";
    
        if ($js_file) {
            $enqueue_code .= "    wp_enqueue_script(\n";
            $enqueue_code .= "        '{$template_name}-js',\n";
            $enqueue_code .= "        get_template_directory_uri() . '/$js_file',\n";
            $enqueue_code .= "        [],\n";
            $enqueue_code .= "        null,\n";
            $enqueue_code .= "        true\n";
            $enqueue_code .= "    );\n";
        }
    
        if ($css_file) {
            $enqueue_code .= "    wp_enqueue_style(\n";
            $enqueue_code .= "        '{$template_name}-css',\n";
            $enqueue_code .= "        get_template_directory_uri() . '/$css_file',\n";
            $enqueue_code .= "        [],\n";
            $enqueue_code .= "        null\n";
            $enqueue_code .= "    );\n";
        }
    
        $enqueue_code .= "}\n";
    
        // Check for duplication
        if (strpos($current_code, "// Enqueue JS and CSS for template: $template_name") === false) {
            file_put_contents($functions_php_path, PHP_EOL . $enqueue_code, FILE_APPEND);
        }
    }
}    