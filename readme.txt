=== SendGrid Email Override ===
Contributors: baiatulutata
Tags: sendgrid, email, smtp, wp_mail, override, api
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Replaces WordPress `wp_mail()` with SendGrid's API. Adds a settings page to manage API key, sender info, and test email functionality.

== Description ==

This plugin overrides the default WordPress email sending function with SendGrid’s v3 Mail Send API.

**Features:**

- Toggle SendGrid email override on/off
- Enter and save your SendGrid API key
- Set a custom From email and From name
- Send a test email from the settings page
- Uses WordPress styling and settings API

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/sendgrid-email-override/` directory.
2. Activate the plugin through the “Plugins” screen in WordPress.
3. Go to **Settings > SendGrid Email** to configure the plugin.
4. Enter your SendGrid API key, set the sender email/name, and enable the override.

== Frequently Asked Questions ==

= What happens when the override is disabled? =
The default WordPress `wp_mail()` function will behave normally.

= Does it support HTML emails or attachments? =
Not yet. Currently only plain text is supported.

= Is the plugin secure? =
The API key is stored securely in the WordPress options table, and nonce validation is used for AJAX actions.

== Screenshots ==

1. Settings page with SendGrid API key and sender fields
2. Test email section with recipient input and send button

== Changelog ==

= 1.0 =
* Initial release with API override, test email function, and settings UI.

== Upgrade Notice ==

= 1.0 =
Initial stable version.

== External Services ==

This plugin integrates with the [SendGrid](https://sendgrid.com/) email delivery service to send outgoing emails via its v3 API instead of the default `wp_mail()` function.

When enabled, the plugin communicates with the following SendGrid API endpoint: https://api.sendgrid.com/v3/mail/send


**Data sent to SendGrid includes:**
- Sender name and email
- Recipient email(s)
- Email subject
- Message body (text or HTML)
- Optional headers (Cc, Bcc)

The plugin requires a valid SendGrid API key to function. This key is stored in your WordPress database and is used only for authenticating API requests to SendGrid.

**Privacy Notice:**
By using this plugin, you agree to SendGrid’s [Terms of Service](https://www.twilio.com/legal/tos) and [Privacy Policy](https://www.twilio.com/legal/privacy). Ensure your use of SendGrid complies with applicable data protection laws and your organization’s privacy policies.

