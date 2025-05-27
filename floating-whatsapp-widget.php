<?php
/*
Plugin Name: Floating WhatsApp Widget
Description: Adds a customizable WhatsApp floating widget to your website

Version: 1.4.2
Author: DesignThat Cloud (Mthokozisi Dhlamini)
Author URI: https://designthat.cloud/
*/

if (!defined('ABSPATH')) exit;


define('FWW_VERSION', '1.4.2');

define('FWW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FWW_PLUGIN_FILE', __FILE__);

// Load updater
require_once FWW_PLUGIN_DIR . 'includes/plugin-updater.php';
new FWW_Plugin_Updater(
    FWW_PLUGIN_FILE,
    FWW_VERSION,
    'Floating WhatsApp Widget'
);

// Sanitize callbacks
function fww_sanitize_phone($input) {
    return preg_replace('/[^0-9]/', '', $input);
}

function fww_sanitize_whatsapp_link($input) {
    return esc_url_raw($input);
}

function fww_sanitize_use_link($input) {
    return $input === 'link' ? 'link' : 'phone';
}

function fww_sanitize_color($input) {
    $color = sanitize_hex_color($input);
    return $color ? $color : '#25D366';
}

function fww_sanitize_position($input) {
    return $input === 'bottom-left' ? 'bottom-left' : 'bottom-right';
}

function fww_sanitize_enabled($input) {
    return $input ? '1' : '0';
}

// Add menu item to WordPress admin
function fww_add_admin_menu() {
    add_menu_page(
        'WhatsApp Widget Settings',
        'WhatsApp Widget',
        'manage_options',
        'floating-whatsapp-widget',
        'fww_settings_page',
        'dashicons-whatsapp'
    );
}
add_action('admin_menu', 'fww_add_admin_menu');

// Enqueue admin assets
function fww_enqueue_admin_assets($hook) {
    if ($hook !== 'toplevel_page_floating-whatsapp-widget') {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('fww-admin', plugins_url('assets/admin.css', __FILE__), array('wp-color-picker'), FWW_VERSION);
    wp_enqueue_script('fww-admin', plugins_url('assets/admin.js', __FILE__), array('jquery', 'wp-color-picker'), FWW_VERSION, true);
}
add_action('admin_enqueue_scripts', 'fww_enqueue_admin_assets');

// Register plugin settings
function fww_register_settings() {
    register_setting('fww_settings', 'fww_phone', array('sanitize_callback' => 'fww_sanitize_phone'));
    register_setting('fww_settings', 'fww_whatsapp_link', array('sanitize_callback' => 'fww_sanitize_whatsapp_link'));
    register_setting('fww_settings', 'fww_use_link', array('sanitize_callback' => 'fww_sanitize_use_link'));
    register_setting('fww_settings', 'fww_color', array('sanitize_callback' => 'fww_sanitize_color'));
    register_setting('fww_settings', 'fww_position', array('sanitize_callback' => 'fww_sanitize_position'));
    register_setting('fww_settings', 'fww_enabled', array('sanitize_callback' => 'fww_sanitize_enabled'));
}
add_action('admin_init', 'fww_register_settings');

// Create the settings page
function fww_settings_page() {
    ?>
    <div class="wrap fww-settings">
        <h2>WhatsApp Widget Settings</h2>

        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php
            settings_fields('fww_settings');
            do_settings_sections('fww_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Contact Method</th>
                    <td>
                        <select name="fww_use_link" id="fww_use_link">
                            <option value="phone" <?php selected(get_option('fww_use_link'), 'phone'); ?>>Phone Number</option>
                            <option value="link" <?php selected(get_option('fww_use_link'), 'link'); ?>>WhatsApp Link</option>
                        </select>
                    </td>
                </tr>
                <tr class="phone-input">
                    <th scope="row">Phone Number</th>
                    <td>
                        <input type="text" name="fww_phone" value="<?php echo esc_attr(get_option('fww_phone')); ?>" />
                        <p class="description">Enter phone number with country code (e.g., 14155552671)</p>
                    </td>
                </tr>
                <tr class="link-input">
                    <th scope="row">WhatsApp Link</th>
                    <td>
                        <input type="url" name="fww_whatsapp_link" value="<?php echo esc_attr(get_option('fww_whatsapp_link')); ?>" />
                        <p class="description">Enter your complete WhatsApp link</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Widget Color</th>
                    <td>
                        <input type="text" class="fww-color-field" name="fww_color" value="<?php echo esc_attr(get_option('fww_color', '#25D366')); ?>" />
                        <button type="button" onclick="document.querySelector('input[name=fww_color]').value='#25D366'">Reset to Default</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Widget Position</th>
                    <td>
                        <select name="fww_position">
                            <option value="bottom-right" <?php selected(get_option('fww_position'), 'bottom-right'); ?>>Bottom Right</option>
                            <option value="bottom-left" <?php selected(get_option('fww_position'), 'bottom-left'); ?>>Bottom Left</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Enable Widget</th>
                    <td>
                        <input type="checkbox" name="fww_enabled" value="1" <?php checked(get_option('fww_enabled', '1'), '1'); ?> />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>

    <?php
}

// Add the widget to the frontend
function fww_add_widget() {
    if (is_admin() || !get_option('fww_enabled', '1')) {
        return;
    }

    $use_link      = get_option('fww_use_link', 'phone');
    $phone         = get_option('fww_phone');
    $whatsapp_link = get_option('fww_whatsapp_link');
    $color         = get_option('fww_color', '#25D366');
    $position      = get_option('fww_position', 'bottom-right');

    $link = $use_link === 'phone' 
        ? "https://wa.me/" . preg_replace('/[^0-9]/', '', $phone)
        : $whatsapp_link;

    $position_class = $position === 'bottom-left' ? 'left: 20px;' : 'right: 20px;';
    
    ?>
    <style>
        .floating-whatsapp {
            position: fixed;
            bottom: 20px;
            <?php echo $position_class; ?>
            z-index: 9999;
            padding: 10px; /* Add padding */
        }
        .floating-whatsapp a {
            display: block;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: <?php echo esc_attr($color); ?>;
            box-shadow: 2px 2px 6px rgba(0,0,0,0.4);
            transition: all 0.3s ease;
        }
        .floating-whatsapp a:hover {
            transform: scale(1.1);
        }
        .floating-whatsapp img {
            width: 35px;
            height: 35px;
            margin: 12px;
            display: block;
        }
    </style>

    <div class="floating-whatsapp">
        <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?php echo esc_url( plugins_url( 'assets/whatsapp.svg', __FILE__ ) ); ?>" alt="WhatsApp" width="35" height="35" />
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'fww_add_widget');