<?php
if (!defined('ABSPATH')) exit;

class FWW_Version_Checker {
    private $github_api_url = 'https://api.github.com/repos/DesignThat/FloatingWhatsAppWidget/releases/latest';
    private $current_version;
    private $transient_key = 'fww_github_version_check';
    private $transient_expiry = 12 * HOUR_IN_SECONDS;

    public function __construct($current_version) {
        $this->current_version = $current_version;
        add_action('admin_notices', array($this, 'display_update_notice'));
    }

    public function check_version() {
        $cached_version = get_transient($this->transient_key);
        
        if ($cached_version !== false) {
            return $cached_version;
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

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (!empty($data->tag_name)) {
            $latest_version = ltrim($data->tag_name, 'v');
            set_transient($this->transient_key, $latest_version, $this->transient_expiry);
            return $latest_version;
        }

        return false;
    }

    public function needs_update() {
        $latest_version = $this->check_version();
        if (!$latest_version) {
            return false;
        }
        return version_compare($this->current_version, $latest_version, '<');
    }

    public function display_update_notice() {
        if (!$this->needs_update()) {
            return;
        }

        $latest_version = $this->check_version();
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <?php printf(
                    __('A new version (%s) of Floating WhatsApp Widget is available. Please <a href="%s" target="_blank">download the latest version</a> from GitHub.', 'floating-whatsapp-widget'),
                    esc_html($latest_version),
                    'https://github.com/DesignThat/FloatingWhatsAppWidget/releases/latest'
                ); ?>
            </p>
        </div>
        <?php
    }
}