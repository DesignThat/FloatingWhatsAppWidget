<?php
/*
Plugin Name: Floating WhatsApp Widget
Description: Creates a floating WhatsApp widget on the front-end.
Version: 1.1
Author: DesignThat Cloud (Mthokozisi Dhlamini)
*/

// Add the plugin settings page to the admin menu
function floating_whatsapp_widget_add_settings_page() {
    add_options_page( 'WhatsApp Widget Settings', 'WhatsApp Widget', 'manage_options', 'floating-whatsapp-widget', 'floating_whatsapp_widget_render_settings_page' );
}
add_action( 'admin_menu', 'floating_whatsapp_widget_add_settings_page' );

// Render the plugin settings page
function floating_whatsapp_widget_render_settings_page() {
    $whatsapp_number = get_option( 'floating_whatsapp_widget_number' ); // Get the WhatsApp number from plugin settings
    $position = get_option( 'floating_whatsapp_widget_position', 'right' ); // Get the widget position from plugin settings
    $placement = get_option( 'floating_whatsapp_widget_placement', 'bottom' ); // Get the widget placement from plugin settings
    $widget_color = get_option( 'floating_whatsapp_widget_color', '#25D366' ); // Get the widget color from plugin settings
    $widget_radius = get_option( 'floating_whatsapp_widget_radius', 50 ); // Get the widget border radius from plugin settings
    $widget_shadow = get_option( 'floating_whatsapp_widget_shadow', 5 ); // Get the widget box shadow from plugin settings
    $widget_font_size = get_option( 'floating_whatsapp_widget_font_size', 24 ); // Get the widget font size from plugin settings
    ?>
    <div class="wrap">
        <h1>WhatsApp Widget Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'floating_whatsapp_widget_settings' );
            do_settings_sections( 'floating-whatsapp-widget' );
            ?>
            <h2>Widget Preview</h2>
            <div id="widget-preview" class="floating-whatsapp-widget" style="background-color: <?php echo esc_attr( $widget_color ); ?>; border-radius: <?php echo esc_attr( $widget_radius ); ?>px; box-shadow: 0 <?php echo esc_attr( $widget_shadow ); ?>px <?php echo esc_attr( $widget_shadow ); ?>px rgba(0, 0, 0, 0.3); font-size: <?php echo esc_attr( $widget_font_size ); ?>px;">
                <a href="https://wa.me/<?php echo esc_attr( $whatsapp_number ); ?>" target="_blank">
                    <i class="fa fa-whatsapp"></i>
                </a>
            </div>
            <h2>Widget Settings</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">WhatsApp Number</th>
                    <td>
                        <input type="text" name="floating_whatsapp_widget_number" value="<?php echo esc_attr( $whatsapp_number ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Position</th>
                    <td>
                        <select name="floating_whatsapp_widget_position">
                            <option value="left" <?php selected( $position, 'left' ); ?>>Left</option>
                            <option value="right" <?php selected( $position, 'right' ); ?>>Right</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Placement</th>
                    <td>
                        <select name="floating_whatsapp_widget_placement">
                            <option value="top" <?php selected( $placement, 'top' ); ?>>Top</option>
                            <option value="bottom" <?php selected( $placement, 'bottom' ); ?>>Bottom</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Widget Color</th>
                    <td>
                        <input type="text" name="floating_whatsapp_widget_color" class="floating-whatsapp-widget-color-picker" value="<?php echo esc_attr( $widget_color ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Widget Border Radius</th>
                    <td>
                        <input type="number" name="floating_whatsapp_widget_radius" value="<?php echo esc_attr( $widget_radius ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Widget Box Shadow</th>
                    <td>
                        <input type="number" name="floating_whatsapp_widget_shadow" value="<?php echo esc_attr( $widget_shadow ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Widget Font Size</th>
                    <td>
                        <input type="number" name="floating_whatsapp_widget_font_size" value="<?php echo esc_attr( $widget_font_size ); ?>" />
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
    register_setting( 'floating_whatsapp_widget_settings', 'floating_whatsapp_widget_position' );
    register_setting( 'floating_whatsapp_widget_settings', 'floating_whatsapp_widget_placement' );
    register_setting( 'floating_whatsapp_widget_settings', 'floating_whatsapp_widget_color' );
    register_setting( 'floating_whatsapp_widget_settings', 'floating_whatsapp_widget_radius' );
    register_setting( 'floating_whatsapp_widget_settings', 'floating_whatsapp_widget_shadow' );
    register_setting( 'floating_whatsapp_widget_settings', 'floating_whatsapp_widget_font_size' );
}
add_action( 'admin_init', 'floating_whatsapp_widget_register_settings' );

// Enqueue the necessary scripts and styles
function floating_whatsapp_widget_scripts() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'floating-whatsapp-widget', plugin_dir_url( __FILE__ ) . 'js/floating-whatsapp-widget.js', array( 'jquery', 'wp-color-picker' ), '', true );
    wp_enqueue_style( 'floating-whatsapp-widget', plugin_dir_url( __FILE__ ) . 'css/floating-whatsapp-widget.css' );
}
add_action( 'admin_enqueue_scripts', 'floating_whatsapp_widget_scripts' );

// Add the WhatsApp widget HTML to the footer
function floating_whatsapp_widget_html() {
    $whatsapp_number = get_option( 'floating_whatsapp_widget_number' ); // Get the WhatsApp number from plugin settings
    $position = get_option( 'floating_whatsapp_widget_position', 'right' ); // Get the widget position from plugin settings
    $placement = get_option( 'floating_whatsapp_widget_placement', 'bottom' ); // Get the widget placement from plugin settings
    $widget_color = get_option( 'floating_whatsapp_widget_color', '#25D366' ); // Get the widget color from plugin settings
    $widget_radius = get_option( 'floating_whatsapp_widget_radius', 50 ); // Get the widget border radius from plugin settings
    $widget_shadow = get_option( 'floating_whatsapp_widget_shadow', 5 ); // Get the widget box shadow from plugin settings
    $widget_font_size = get_option( 'floating_whatsapp_widget_font_size', 24 ); // Get the widget font size from plugin settings
    ?>
    <div id="floating-whatsapp-widget" class="floating-whatsapp-widget" style="background-color: <?php echo esc_attr( $widget_color ); ?>; border-radius: <?php echo esc_attr( $widget_radius ); ?>px; box-shadow: 0 <?php echo esc_attr( $widget_shadow ); ?>px <?php echo esc_attr( $widget_shadow ); ?>px rgba(0, 0, 0, 0.3); font-size: <?php echo esc_attr( $widget_font_size ); ?>px; <?php echo esc_attr( $position . ': ' . $placement . ';' ); ?>">
        <a href="https://wa.me/<?php echo esc_attr( $whatsapp_number ); ?>" target="_blank">
            <i class="fa fa-whatsapp"></i>
        </a>
    </div>
    <?php
}
add_action( 'wp_footer', 'floating_whatsapp_widget_html' );