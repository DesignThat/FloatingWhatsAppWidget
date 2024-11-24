<?php
if (!defined('ABSPATH')) exit;

class FWW_Plugin_Updater {
    private $github_api_url = 'https://api.github.com/repos/DesignThat/FloatingWhatsAppWidget/releases/latest';
    private $plugin_slug;
    private $plugin_file;
    private $current_version;
    private $plugin_name;
    private $transient_key = 'fww_github_update_data';

    public function __construct($plugin_file, $current_version, $plugin_name) {
        $this->plugin_file = $plugin_file;
        $this->current_version = $current_version;
        $this->plugin_name = $plugin_name;
        $this->plugin_slug = dirname(plugin_basename($plugin_file));

        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_updates'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_action('admin_notices', array($this, 'display_update_notice'));
    }

    private function get_github_release_info() {
        $cached_data = get_transient($this->transient_key);
        if ($cached_data !== false) {
            return $cached_data;
        }

        $response = wp_remote_get($this->github_api_url, array(
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/Floating-WhatsApp-Widget'
            )
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response));
        if (empty($data)) {
            return false;
        }

        $release_data = array(
            'version' => ltrim($data->tag_name, 'v'),
            'download_url' => $data->zipball_url,
            'published_at' => $data->published_at,
            'description' => $data->body,
        );

        set_transient($this->transient_key, $release_data, 12 * HOUR_IN_SECONDS);
        return $release_data;
    }

    public function check_for_updates($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $release_info = $this->get_github_release_info();
        if (!$release_info) {
            return $transient;
        }

        if (version_compare($this->current_version, $release_info['version'], '<')) {
            $plugin_data = array(
                'slug' => $this->plugin_slug,
                'plugin' => plugin_basename($this->plugin_file),
                'new_version' => $release_info['version'],
                'url' => 'https://github.com/DesignThat/FloatingWhatsAppWidget',
                'package' => $release_info['download_url'],
            );

            $transient->response[$plugin_data['plugin']] = (object) $plugin_data;
        }

        return $transient;
    }

    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if (!isset($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        $release_info = $this->get_github_release_info();
        if (!$release_info) {
            return $result;
        }

        return (object) array(
            'name' => $this->plugin_name,
            'slug' => $this->plugin_slug,
            'version' => $release_info['version'],
            'author' => 'DesignThat',
            'requires' => '5.0',
            'tested' => get_bloginfo('version'),
            'last_updated' => $release_info['published_at'],
            'sections' => array(
                'description' => 'Floating WhatsApp Widget for WordPress',
                'changelog' => wp_markdown_to_html($release_info['description']),
            ),
            'download_link' => $release_info['download_url'],
        );
    }

    public function display_update_notice() {
        $release_info = $this->get_github_release_info();
        if (!$release_info || !version_compare($this->current_version, $release_info['version'], '<')) {
            return;
        }

        $update_url = wp_nonce_url(
            self_admin_url('update.php?action=upgrade-plugin&plugin=' . plugin_basename($this->plugin_file)),
            'upgrade-plugin_' . plugin_basename($this->plugin_file)
        );

        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <?php printf(
                    __('A new version (%s) of %s is available! <a href="%s">Click here to update now</a>.', 'floating-whatsapp-widget'),
                    esc_html($release_info['version']),
                    esc_html($this->plugin_name),
                    esc_url($update_url)
                ); ?>
            </p>
        </div>
        <?php
    }
}