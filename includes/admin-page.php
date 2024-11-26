<?php
namespace MyPlugin;

class AdminPage {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_page']);
    }

    public function add_admin_page() {
        add_menu_page(
            'Template Manager',
            'Template Manager',
            'manage_options',
            'template-manager',
            [$this, 'render_admin_page']
        );
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Create a New Template</h1>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="template_name">Template Name</label></th>
                        <td><input type="text" id="template_name" name="template_name" required></td>
                    </tr>
                    <tr>
                        <th><label for="js_file">JS File</label></th>
                        <td><input type="text" id="js_file" name="js_file" placeholder="example.js"></td>
                    </tr>
                    <tr>
                        <th><label for="css_file">CSS File</label></th>
                        <td><input type="text" id="css_file" name="css_file" placeholder="example.css"></td>
                    </tr>
                </table>
                <?php submit_button('Create Template'); ?>
            </form>
        </div>
        <?php
    }
}
new AdminPage();
