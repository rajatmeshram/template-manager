<?php
namespace MyPlugin;

class ScriptManager {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'conditionally_enqueue_scripts']);
    }

    public function conditionally_enqueue_scripts() {
        // Get current template
        $current_template = basename(get_page_template());
        $script_data = get_option('my_plugin_scripts', []);

        // Check if the script data exists for the current template
        if (isset($script_data[$current_template])) {
            $files = $script_data[$current_template];

            // Enqueue JS file
            if (!empty($files['js'])) {
                $this->enqueue_once(
                    $current_template . '-js',
                    $files['js'],
                    'script'
                );
            }

            // Enqueue CSS file
            if (!empty($files['css'])) {
                $this->enqueue_once(
                    $current_template . '-css',
                    $files['css'],
                    'style'
                );
            }
        }
    }

    private function enqueue_once($handle, $file, $type) {
        $functions_file = get_template_directory() . '/functions.php';

        // Check if the file path exists in functions.php
        $content = file_get_contents($functions_file);
        $enqueue_code = $this->generate_enqueue_code($handle, $file, $type);

        if (strpos($content, $enqueue_code) === false) {
            // Append the enqueue code to functions.php
            file_put_contents($functions_file, PHP_EOL . $enqueue_code, FILE_APPEND);
        }
    }

    private function generate_enqueue_code($handle, $file, $type) {
        $file_path = "<?php echo get_template_directory_uri(); ?>/$file";
        if ($type === 'script') {
            return "wp_enqueue_script('$handle', $file_path, [], null, true);";
        } elseif ($type === 'style') {
            return "wp_enqueue_style('$handle', $file_path, [], null);";
        }

        return '';
    }
}
