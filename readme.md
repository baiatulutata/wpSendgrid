# SendGrid Email Override

Replaces the default `wp_mail()` function in WordPress with SendGrid's API for reliable email delivery.

## Features

- ✅ Enable/disable SendGrid email override
- 🔑 Input and store your SendGrid API key securely
- 📨 Customize the sender name and email
- 📬 Send a test email from the settings page
- 🖌️ Uses native WordPress settings UI

## Installation

1. Upload this plugin to your WordPress `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu.
3. Navigate to **Settings → SendGrid Email**.
4. Enter your API key, sender details, and enable the feature.

## Development Notes

- Plugin uses `pre_wp_mail` to intercept and replace email logic.
- Settings are stored in `sendgrid_email_override_options`.
- Test email is sent using AJAX with nonce validation.

## Roadmap

- [ ] Support HTML emails and custom headers
- [ ] Add logging or status feedback on email send
- [ ] Multi-recipient and CC/BCC support

## Author

**Ionut Baldazar**

## License

GPLv2 or later — [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)
