<?php
/*
Plugin Name: Floating WhatsApp Widget
Plugin URI: https://designthat.cloud/downloads/floating-whatsapp-widget
Description: Creates a floating WhatsApp widget on the front-end.
Version: 1.3.3
Author: DesignThat Cloud (Mthokozisi Dhlamini)
Author URI: https://designthat.dev/
*/

// Add the plugin settings page to the admin menu
function floating_whatsapp_widget_add_settings_page() {
    add_options_page( 'WhatsApp Widget Settings', 'WhatsApp Widget', 'manage_options', 'floating-whatsapp-widget', 'floating_whatsapp_widget_render_settings_page' );
}
add_action( 'admin_menu', 'floating_whatsapp_widget_add_settings_page' );

// Render the plugin settings page
function floating_whatsapp_widget_render_settings_page() {
    $whatsapp_number = get_option( 'floating_whatsapp_widget_number' ); // Get the WhatsApp number from plugin settings
    ?>
    <div class="wrap">
        <h1>WhatsApp Widget Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'floating_whatsapp_widget_settings' );
            do_settings_sections( 'floating-whatsapp-widget' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">WhatsApp Number</th>
                    <td>
                        <input type="text" name="floating_whatsapp_widget_number" value="<?php echo esc_attr( $whatsapp_number ); ?>" />
                        <p class="description">Enter your WhatsApp number in the format: country code followed by phone number. Example: +1234567890</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register plugin settings
function floating_whatsapp_widget_register_settings() {
    register_setting( 'floating_whatsapp_widget_settings', 'floating_whatsapp_widget_number' );
}
add_action( 'admin_init', 'floating_whatsapp_widget_register_settings' );

// Enqueue the necessary styles and scripts
function floating_whatsapp_widget_scripts() {
    wp_enqueue_style( 'floating-whatsapp-widget', plugin_dir_url( __FILE__ ) . 'css/floating-whatsapp-widget.css' );
    wp_enqueue_script( 'floating-whatsapp-widget', plugin_dir_url( __FILE__ ) . 'js/floating-whatsapp-widget.js', array( 'jquery' ), '', true );
}
add_action( 'wp_enqueue_scripts', 'floating_whatsapp_widget_scripts' );

// Add the WhatsApp widget HTML to the footer
function floating_whatsapp_widget_html() {
    $whatsapp_number = get_option( 'floating_whatsapp_widget_number' ); // Get the WhatsApp number from plugin settings
    ?>
    <div id="floating-whatsapp-widget">
        <a href="https://wa.me/<?php echo esc_attr( $whatsapp_number ); ?>" target="_blank">
            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/whatsapp.svg'; ?>" alt="WhatsApp Icon">
        </a>
    </div>
    <?php
}
add_action( 'wp_footer', 'floating_whatsapp_widget_html' );