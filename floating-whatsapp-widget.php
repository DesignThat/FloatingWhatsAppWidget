<?php
/*
Plugin Name: Floating WhatsApp Widget
Description: Adds a customizable WhatsApp floating widget to your website
Version: 1.3
Author: DesignThat Cloud (Mthokozisi Dhlamini)
Author URI: https://designthat.cloud/
*/

if (!defined('ABSPATH')) exit;

define('FWW_VERSION', '1.0');
define('FWW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FWW_PLUGIN_FILE', __FILE__);

// Load updater
require_once FWW_PLUGIN_DIR . 'includes/plugin-updater.php';
new FWW_Plugin_Updater(
    FWW_PLUGIN_FILE,
    FWW_VERSION,
    'Floating WhatsApp Widget'
);

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

// Register plugin settings
function fww_register_settings() {
    register_setting('fww_settings', 'fww_phone');
    register_setting('fww_settings', 'fww_whatsapp_link');
    register_setting('fww_settings', 'fww_use_link');
    register_setting('fww_settings', 'fww_color');
    register_setting('fww_settings', 'fww_position');
}
add_action('admin_init', 'fww_register_settings');

// Create the settings page
function fww_settings_page() {
    ?>
    <div class="wrap">
        <h2>WhatsApp Widget Settings</h2>
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
                        <input type="color" name="fww_color" value="<?php echo esc_attr(get_option('fww_color', '#25D366')); ?>" />
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
            </table>
            <?php submit_button(); ?>
        </form>
    </div>

    <script>
    document.getElementById('fww_use_link').addEventListener('change', function() {
        const phoneInput = document.querySelector('.phone-input');
        const linkInput = document.querySelector('.link-input');
        
        if (this.value === 'phone') {
            phoneInput.style.display = 'table-row';
            linkInput.style.display = 'none';
        } else {
            phoneInput.style.display = 'none';
            linkInput.style.display = 'table-row';
        }
    });

    // Trigger on page load
    document.getElementById('fww_use_link').dispatchEvent(new Event('change'));
    </script>
    <?php
}

// Add the widget to the frontend
function fww_add_widget() {
    $use_link = get_option('fww_use_link', 'phone');
    $phone = get_option('fww_phone');
    $whatsapp_link = get_option('fww_whatsapp_link');
    $color = get_option('fww_color', '#25D366');
    $position = get_option('fww_position', 'bottom-right');

    $link = $use_link === 'phone' 
        ? "https://wa.me/" . preg_replace('/[^0-9]/', '', $phone)
        : $whatsapp_link;

    $position_class = $position === 'bottom-left' ? 'left-20px' : 'right-20px';
    
    ?>
    <style>
        .floating-whatsapp {
            position: fixed;
            bottom: 20px;
            <?php echo $position_class; ?>: 20px;
            z-index: 9999;
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
        .floating-whatsapp svg {
            width: 35px;
            height: 35px;
            margin: 12px;
            fill: white;
        }
    </style>

    <div class="floating-whatsapp">
        <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
            </svg>
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'fww_add_widget');