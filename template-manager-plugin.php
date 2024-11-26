<?php
/**
 * Plugin Name: Template Manager
 * Description: A plugin to dynamically create templates, JS/CSS files, and enqueue them.
 * Version: 1.1
 * Author: RajatMeshram
 */

if (!defined('ABSPATH')) exit;

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/class-template-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-script-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';

// Initialize the plugin
add_action('plugins_loaded', function () {
    new MyPlugin\TemplateManager();
    new MyPlugin\ScriptManager();
});
