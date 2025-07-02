<?php
/*
Plugin Name: SendGrid Email Override
Description: Replaces WordPress wp_mail with SendGrid API. Includes settings page with toggle, API key input, and test button.
Version: 1.0
Author: Ionut Baldazar
*/

if (!defined('ABSPATH')) exit;

class SendGrid_Email_Override {
    private $option_name = 'sendgrid_email_override_options';

    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('pre_wp_mail', [$this, 'intercept_wp_mail'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_ajax_sendgrid_send_test_email', [$this, 'ajax_send_test_email']);
    }

    public function add_settings_page() {
        add_options_page('SendGrid Email Override', 'SendGrid Email', 'manage_options', 'sendgrid-email-override', [$this, 'render_settings_page']);
    }

    public function register_settings() {
        register_setting('sendgrid_email_override_group', $this->option_name);
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook === 'settings_page_sendgrid-email-override') {
            wp_enqueue_script('sendgrid-admin', plugin_dir_url(__FILE__) . 'sendgrid-admin.js', ['jquery'], null, true);
            wp_localize_script('sendgrid-admin', 'sendgrid_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('sendgrid_test_email_nonce')
            ]);        }
    }

    public function render_settings_page() {
        $options = get_option($this->option_name);
        ?>
        <div class="wrap">
            <h1>SendGrid Email Override</h1>
            <form method="post" action="options.php">
                <?php settings_fields('sendgrid_email_override_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Enable SendGrid Override</th>
                        <td><input type="checkbox" name="<?= $this->option_name ?>[enabled]" value="1" <?= isset($options['enabled']) ? 'checked' : '' ?>></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">SendGrid API Key</th>
                        <td><input type="password" name="<?= $this->option_name ?>[api_key]" value="<?= esc_attr($options['api_key'] ?? '') ?>" class="regular-text"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">From Email</th>
                        <td><input type="email" name="<?= $this->option_name ?>[from_email]" value="<?= esc_attr($options['from_email'] ?? get_bloginfo('admin_email')) ?>" class="regular-text"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">From Name</th>
                        <td><input type="text" name="<?= $this->option_name ?>[from_name]" value="<?= esc_attr($options['from_name'] ?? get_bloginfo('name')) ?>" class="regular-text"></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <hr>
            <h2>Send Test Email</h2>
            <p><label for="sendgrid-test-email-to">Recipient Email:</label>
                <input type="email" id="sendgrid-test-email-to" class="regular-text"></p>
            <button id="sendgrid-test-email" class="button button-secondary">Send Test Email</button>
            <div id="sendgrid-test-result"></div>
        </div>
        <?php
    }

    public function intercept_wp_mail($null, $atts) {
        $options = get_option($this->option_name);
        if (empty($options['enabled']) || empty($options['api_key'])) return null;

        $to = is_array($atts['to']) ? implode(',', $atts['to']) : $atts['to'];
        $subject = $atts['subject'];
        $body = $atts['message'];
        $headers = $atts['headers'];

        $from_email = !empty($options['from_email']) ? $options['from_email'] : get_bloginfo('admin_email');
        $from_name = !empty($options['from_name']) ? $options['from_name'] : get_bloginfo('name');

        $email_data = [
            'personalizations' => [[
                'to' => [['email' => $to]],
                'subject' => $subject
            ]],
            'from' => [
                'email' => $from_email,
                'name' => $from_name
            ],
            'content' => [[
                'type' => 'text/plain',
                'value' => $body
            ]]
        ];

        $response = wp_remote_post('https://api.sendgrid.com/v3/mail/send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $options['api_key'],
                'Content-Type' => 'application/json'
            ],
            'body' => wp_json_encode($email_data),
            'method' => 'POST',
            'data_format' => 'body'
        ]);

        return [
            'to' => $atts['to'],
            'subject' => $subject,
            'message' => $body,
            'headers' => $headers,
            'attachments' => [],
            'send_result' => !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 202
        ];
    }

    public function ajax_send_test_email() {
        check_ajax_referer('sendgrid_test_email_nonce', 'nonce');
        $options = get_option($this->option_name);

        if (empty($options['enabled']) || empty($options['api_key'])) {
            wp_send_json_error('SendGrid not enabled or API key missing');
        }

        $to = sanitize_email($_POST['to'] ?? get_bloginfo('admin_email'));
        if (!is_email($to)) {
            wp_send_json_error('Invalid test email address');
        }

        $result = wp_mail($to, 'SendGrid Test Email', 'This is a test email sent using SendGrid.');
        wp_send_json_success($result ? 'Test email sent!' : 'Failed to send email.');
    }
}

new SendGrid_Email_Override();
