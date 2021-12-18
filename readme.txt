

=== Mobile Contact Bar ===
Contributors: anna.bansaghi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YXJAZ7Q5EJFUA
Tags: social media, icon, contact, mobile, woocommerce cart
Requires at least: 4.9
Tested up to: 5.8
Requires PHP: 5.6.20
Stable tag: 3.0.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.en.html

Allow your visitors to contact you via mobile phones, or access your site's pages instantly.



== Description ==

Mobile Contact Bar is a compact and highly customizable plugin, which allows your visitors to contact you directly via mobile phones, or access your site's pages instantly.

The settings page is available under the *Settings &rarr; Mobile Contact Bar* menu in the WordPress dashboard.



= Features =

* Icons for social media, call-to-actions, or any links to web pages
* Simple and intuitive styling with the aid of the Real-time Model
* Built-in icon picker with [Font Awesome 5](https://fontawesome.com/) integration
* Customizable URLs using query string parameters
* No data collection from your website's visitors
* Super easy to use, no coding required!



= Special Icons =

* Scroll to Top
* WooCommerce Cart with Item Counter



= Supported Protocols =

* `http`
* `https`
* `mailto`
* `skype`
* `sms`
* `tel`
* `viber`



= Tested with =

* Twenty Twenty-One
* Twenty Twenty
* Twenty Nineteen
* Twenty Seventeen
* Twenty Sixteen
* Twenty Fifteen
* Twenty Fourteen
* Twenty Thirteen
* Twenty Twelve
* Twenty Eleven
* Twenty Ten



= Credits =

* [Font Awesome](https://fontawesome.com) SVG, font, and CSS framework
* [Codestar Color Picker](https://github.com/bhaskarkc/codestar-wp-color-picker) with alpha channel



== Installation ==

= First time Mobile Contact Bar user =

Thank you for choosing Mobile Contact Bar! In order to create your bar, simply activate the plugin and visit the plugin's settings page by clicking on *Settings &rarr; Mobile Contact Bar* in your left navigation menu.
Once the plugin page loads, open the *Bar* box, choose the *Display on Devices* option, select the device type to enable the bar and then press the *Save Changes* button at the bottom of the page.
Mobile Contact Bar will automatically create a default bar with an envelope icon, which uses the email address of your site's admin.

= Adding icons to your bar =

To add more icons to your bar, open the *Contact List* box, find a particular list item, select the checkbox, customize the icon and fill in the URI field.
In order to add custom links, click on the *New Contact* button or on one of the icons at the top of the list.


= Positioning and styling your bar =

To set options for bar (positions, colors, borders, width, height, space, placeholder, etc.), open the *Bar* box and check the changes on the *Real-time Model*.
Open the *Icons*, *Badges*, or *Toggle* box and set options for icons, badges, or toggle, respectively.


== Frequently Asked Questions ==

= JavaScript disabled =
The plugin works fine without JavaScript on the front-end of your site.
We use JavaScript on the front-end in two cases:
1. if the toggle is activated, then the plugin has an option for saving the toggle state in a cookie, and
2. if the *Scroll to Top* icon is added, then the scrolling position is calculated in an inline script.

= Cookies =
You have full control over the single cookie which is called *mobile_contact_bar_toggle*.




== Screenshots ==

1. Contact List box
2. Icons box, Toggle box
3. Bar box
4. Settings &rarr; Mobile Contact Bar



== Changelog ==

= 3.0.0 =
* [Upgraded] Minimum supported WordPress version is 4.9.18
* [Upgraded] Minimum supported PHP version is 5.6.20
* [Upgraded] Reimplement plugin
* [Renamed] *#mobile-contact-bar-outer* to *#mobile-contact-bar-nav*
+ [Added] viber [viber-chat](https://wordpress.org/support/topic/problem-whit-link/)
* [Added] Contact colors customization
* [Added] Display label above / below and instead of the icon - [text-below-icon](https://wordpress.org/support/topic/text-below-icon/), [add-label-under-icons](https://wordpress.org/support/topic/add-label-under-icons/)
* [Added] Toggle option for setting the bar to be closed as default - [toggle-state](https://wordpress.org/support/topic/toggle-state-4/), [default-toggle-state](https://wordpress.org/support/topic/default-toggle-state/)
* [Added] Instead of Font Awesome's CSS classes use font-size for setting the icons' size
* [Added] Remove + sign from tel and sms protocols - [telephone-remove](https://wordpress.org/support/topic/telephone-remove/)
* [Added] CSS class selectors for contacts, icons and labels
* [Fixed] Move migration process to init in order to run on both admin and public faces
* [Updated] Font Awesome 5.15.4

= 2.0.9 =
* [Fixed] Missing contact field "checked" notice

= 2.0.8 =
* [Fixed] Add rel="noopener" for links opening in new tab - [links-to-cross-origin-destinations-are-unsafe](https://wordpress.org/support/topic/links-to-cross-origin-destinations-are-unsafe-7/)

= 2.0.7 =
* [Fixed] Do not show meta boxes on foreign pages - [menu-bar-settings-appearing-for-ohter-users-than-admin](https://wordpress.org/support/topic/menu-bar-settings-appearing-for-ohter-users-than-admin/)

= 2.0.6 =
* [Fixed] Domain Path

= 2.0.5 =
* [Fixed] License version
* [Fixed] Requires at least has been changed to 4.6

= 2.0.4 =
* [Fixed] Add padding zero to toggle

= 2.0.3 =
* [Fixed] Add margin and padding to list items
* [Updated] Font Awesome 5.13.0

= 2.0.2 =
* [Fixed] Forgotten log message in source - [your-update-just-broke-my-site](https://wordpress.org/support/topic/your-update-just-broke-my-site-2/)

= 2.0.1 =
* [Fixed] Extracted cookie into an option Toggle:Cookie
* [Fixed] Restored Bar:Opacity option - [a-few-more-minor-things-in-2-0](https://wordpress.org/support/topic/a-few-more-minor-things-in-2-0/)
* [Updated] Font Awesome 5.0.13

= 2.0.0 =
* [Upgraded] New data scheme for options in the database
* [Upgraded] Font Awesome 5.0.12 - [can-add-support-fontawesome-v5-0-8](https://wordpress.org/support/topic/can-add-support-fontawesome-v5-0-8/)
* [Added] UI for managing contacts and their parameters (add, delete, modify) - [a-couple-of-more-feature-suggestions](https://wordpress.org/support/topic/a-couple-of-more-feature-suggestions/)
* [Added] Option for setting label on the toggle Toggle:Label - [a-couple-of-more-feature-suggestions](https://wordpress.org/support/topic/a-couple-of-more-feature-suggestions/)
* [Added] Option for adding space above/below the bar Bar:Space_Height - [contact-bar-overlaying-footer-credits-on-site](https://wordpress.org/support/topic/contact-bar-overlaying-footer-credits-on-site/), [position](https://wordpress.org/support/topic/position-20/), [hidding-menu](https://wordpress.org/support/topic/hidding-menu/)
* [Added] Storing toggle state in a cookie - [toggle-state](https://wordpress.org/support/topic/toggle-state/), [toggle-issue](https://wordpress.org/support/topic/toggle-issue/)
* [Added] WhatsApp - [a-couple-of-feature-ideas](https://wordpress.org/support/topic/a-couple-of-feature-ideas/), [whatsapp-chat](https://wordpress.org/support/topic/whatsapp-chat/)
* [Added] WooCommerce Cart with Item Counter - [a-couple-of-feature-ideas](https://wordpress.org/support/topic/a-couple-of-feature-ideas/)
* [Renamed] `mcb_front_render_html` to `mcb_public_render_html`
* [Removed] `mcb_admin_update_contacts` and `mcb_admin_update_settings` filters

= 1.4.1 =
* [Fixed] Bar:Width and Bar:Alignment issues

= 1.4.0 =
* [Added] Option for setting Bar:Width - [bar-width-2](https://wordpress.org/support/topic/bar-width/)
* [Added] Icon for Instagram - [no-instagram-icon](https://wordpress.org/support/topic/no-instagram-icon/)
* [Fixed] Plugin upgrade on network

= 1.3.1 =
* [Tested] Tested up to WordPress 4.9

= 1.3.0 =
* [Added] Icon for texting - [text-with-pre-filled-option](https://wordpress.org/support/topic/text-with-pre-filled-option/)

= 1.2.3 =
* [Fixed] array_filter() issue

= 1.2.2 =
* [Fixed] array_filter() issue

= 1.2.1 =
* [Fixed] Empty arrays issues

= 1.2.0 =
* [Added] UI for sorting contacts
* [Added] Option for setting subject, body, cc, bcc of email - [add-subject-and-body-to-email](https://wordpress.org/support/topic/add-subject-and-body-to-email/)
* [Added] Refreshed option page UI using meta boxes
* [Fixed] Prepared plugin for localization
* [Fixed] Sanitized phone number and add a plus sign (+) prefix
* [Updated] Font Awesome 4.7.0

= 1.1.2 =
* [Fixed] Left aligned icons in the CSS - [does-your-plugin-support-the-hemingway-theme](https://wordpress.org/support/topic/does-your-plugin-support-the-hemingway-theme/)
* [Updated] Font Awesome 4.6.3

= 1.1.1 =
* [Fixed] Admin styles
* [Fixed] Public styles
* [Updated] Font Awesome 4.6.1

= 1.1.0 =
* [Added] Option for Bar:Opening links in a new tab - [no-instagram-icon](https://wordpress.org/support/topic/no-instagram-icon/)

= 1.0.1 =
* [Fixed] Improved setting and contact validation (sanitization)
* [Fixed] Set the default value of the fixed bar position to true
* [Fixed] Removed obsolated workarounds

= 1.0.0 =
* [Upgraded] Official release

= 0.1.1 =
* [Fixed] Default option issue during network activation

= 0.1.0 =
* [Added] Initial release



== Upgrade Notice ==

= 3.0.0 =
* Minimum supported PHP version is 5.6.20, you can rollback the plugin to its previous version if needed.

= 2.0.0 =
* Structure of the plugin's option has been changed, supported Font Awesome version is 5.

= 1.0.0 =
* Official release.
