

=== Mobile Contact Bar ===
Contributors: anna.bansaghi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YXJAZ7Q5EJFUA
Tags: social media, icon, cta button, call to action, woocommerce cart
Requires at least: 5.4
Tested up to: 5.9
Requires PHP: 5.6.20
Stable tag: 3.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Allow your visitors to contact you via mobile phones, or access your site's pages instantly.



== Description ==

Mobile Contact Bar is a compact and highly customizable plugin, which allows your visitors to contact you directly via mobile phones, or access your site's pages instantly.

The settings page is available under the *MCB Contact Bar* menu in the WordPress admin.



= Features =

* SVG icons for social media, call-to-actions, or any links to web pages
* Simple and intuitive editing with live preview
* Serve the generated CSS file from the `uploads` folder
* Built-in icon picker with
    * [Font Awesome 5](https://fontawesome.com/) (CC BY 4.0 License) and
    * [Tabler Icons](https://tabler-icons.io/) (MIT License) integration
* Customizable URLs using query string parameters
* Show bar on selected post types
* No data collection from your website's visitors
* Easy to use, no coding required!



= Special Actions =

* Go Back and Forward in Browser History
* Scroll to Top of the page
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



== Installation ==

= First time Mobile Contact Bar user =

Thank you for choosing Mobile Contact Bar! In order to create your bar, simply activate the plugin and visit the plugin's page by clicking on *MCB Contact Bar* in your left navigation menu.
Once the plugin page loads, open the *Bar* box, choose the *Display on Devices* option, select the device type to enable the bar and then press the *Save Changes* button at the top of the page.
Mobile Contact Bar will automatically create a default bar with a couple of buttons for you.

= Adding buttons to your bar =

To add more buttons to your bar, open the *Button Builder* box, find a particular list item, select the checkbox, customize the icon and fill in the URI field.
In order to add more buttons, click on the *New Button* at the bottom of the list.


= Positioning and styling your bar =

To set options for bar (positions, colors, borders, width, height, space, placeholder, etc.), open the *Bar* box and check the changes on the *Preview*.
Open the *Icons*, *Badges*, or *Toggle* box and set options for icons, badges, or toggle, respectively.


== Frequently Asked Questions ==

= JavaScript disabled =
The plugin works fine without JavaScript on the front-end of your site.
We use JavaScript on the front-end in two cases:
1. if the toggle is activated, then the plugin has an option for saving the toggle state in a cookie, and
2. *Scroll to Top*, *Back in History*, and *Forward in History* actions use inline JavaScript for their functionality.

= Cookies =
You have full control over the single cookie which is called *mobile_contact_bar_toggle*.



== Screenshots ==

1. Button Builder meta box
2. Buttons, Toggle meta boxes
3. Bar meta box
4. Settings &rarr; Mobile Contact Bar



== Changelog ==

= 3.0.0 =
* [Upgrade] Minimum supported WordPress version is 5.4
* [Upgrade] Minimum supported PHP version is 5.6.20
* [Upgrade] Replace icon fonts with SVG icons
* [Upgrade] Reimplement plugin
* [Rename] `#mobile-contact-bar-outer` to `#mobile-contact-bar-nav`
* [Deprecate] Bar Width, Bar Placeholder, Icons Alignment
* [Add] Introduce Tabler Icons 1.50.0
* [Add] Browser history back and forward actions
* [Add] Save CSS to file, and enqueue it [css-in-css-file](https://wordpress.org/support/topic/css-in-css-file/)
* [Add] Show bar only on specific post types [hide-the-contact-bar-in-home-page](https://wordpress.org/support/topic/hide-the-contact-bar-in-home-page/), [remove-bar-from-a-specific-page](https://wordpress.org/support/topic/remove-bar-from-a-specific-page/)
+ [Add] viber [viber-chat](https://wordpress.org/support/topic/problem-whit-link/)
* [Add] Position bar on left or right side of the screen
* [Add] CSS ID selector and color customization
* [Add] Display label above, below, and instead of the icon [text-below-icon](https://wordpress.org/support/topic/text-below-icon/), [add-label-under-icons](https://wordpress.org/support/topic/add-label-under-icons/)
* [Add] Option for setting the bar to be closed as default [toggle-state](https://wordpress.org/support/topic/toggle-state-4/), [default-toggle-state](https://wordpress.org/support/topic/default-toggle-state/)
* [Add] Let the + sign be optional in tel and sms protocols [telephone-remove](https://wordpress.org/support/topic/telephone-remove/)
* [Fix] Decode / encode query string [e-mail-body](https://wordpress.org/support/topic/e-mail-body/)
* [Fix] Move migration process to `init` in order to run it on both admin and public pages
* [Update] Font Awesome 5.15.4

= 2.0.9 =
* [Fix] Missing contact field "checked" notice

= 2.0.8 =
* [Fix] Add `rel="noopener"` for links opening in new tab [links-to-cross-origin-destinations-are-unsafe](https://wordpress.org/support/topic/links-to-cross-origin-destinations-are-unsafe-7/)

= 2.0.7 =
* [Fix] Do not show meta boxes on foreign pages [menu-bar-settings-appearing-for-ohter-users-than-admin](https://wordpress.org/support/topic/menu-bar-settings-appearing-for-ohter-users-than-admin/)

= 2.0.6 =
* [Fix] Domain Path

= 2.0.5 =
* [Fix] License version
* [Fix] Requires at least has been changed to 4.6

= 2.0.4 =
* [Fix] Add padding zero to toggle

= 2.0.3 =
* [Fix] Add margin and padding to list items
* [Update] Font Awesome 5.13.0

= 2.0.2 =
* [Fix] Forgotten log message in source [your-update-just-broke-my-site](https://wordpress.org/support/topic/your-update-just-broke-my-site-2/)

= 2.0.1 =
* [Fix] Extracted cookie into an option Toggle:Cookie
* [Fix] Restored Bar:Opacity option [a-few-more-minor-things-in-2-0](https://wordpress.org/support/topic/a-few-more-minor-things-in-2-0/)
* [Update] Font Awesome 5.0.13

= 2.0.0 =
* [Upgrade] Reimplement plugin with new options in the database
* [Upgrade] Font Awesome 5.0.12 [can-add-support-fontawesome-v5-0-8](https://wordpress.org/support/topic/can-add-support-fontawesome-v5-0-8/)
* [Rename] `mcb_front_render_html` to `mcb_public_render_html`
* [Deprecate] `mcb_admin_update_contacts` and `mcb_admin_update_settings` filters
* [Add] UI for managing contacts and details (add, delete, modify) [a-couple-of-more-feature-suggestions](https://wordpress.org/support/topic/a-couple-of-more-feature-suggestions/)
* [Add] Option for setting label on the toggle [a-couple-of-more-feature-suggestions](https://wordpress.org/support/topic/a-couple-of-more-feature-suggestions/)
* [Add] Option for adding space above/below the bar [contact-bar-overlaying-footer-credits-on-site](https://wordpress.org/support/topic/contact-bar-overlaying-footer-credits-on-site/), [position](https://wordpress.org/support/topic/position-20/), [hidding-menu](https://wordpress.org/support/topic/hidding-menu/)
* [Add] Storing toggle state in a cookie [toggle-state](https://wordpress.org/support/topic/toggle-state/), [toggle-issue](https://wordpress.org/support/topic/toggle-issue/)
* [Add] WhatsApp [a-couple-of-feature-ideas](https://wordpress.org/support/topic/a-couple-of-feature-ideas/), [whatsapp-chat](https://wordpress.org/support/topic/whatsapp-chat/)
* [Add] WooCommerce Cart with Item Counter action [a-couple-of-feature-ideas](https://wordpress.org/support/topic/a-couple-of-feature-ideas/)

= 1.4.1 =
* [Fix] Bar width and bar alignment issues

= 1.4.0 =
* [Add] Option for setting bar width [bar-width-2](https://wordpress.org/support/topic/bar-width/)
* [Add] Icon for Instagram [no-instagram-icon](https://wordpress.org/support/topic/no-instagram-icon/)
* [Fix] Plugin upgrade on network

= 1.3.1 =
* [Test] Tested up to WordPress 4.9

= 1.3.0 =
* [Add] Icon for texting (sms) [text-with-pre-filled-option](https://wordpress.org/support/topic/text-with-pre-filled-option/)

= 1.2.3 =
* [Fix] array_filter() issue

= 1.2.2 =
* [Fix] array_filter() issue

= 1.2.1 =
* [Fix] Empty arrays issues

= 1.2.0 =
* [Add] UI for sorting contacts
* [Add] Option for setting subject, body, cc, bcc of email [add-subject-and-body-to-email](https://wordpress.org/support/topic/add-subject-and-body-to-email/)
* [Add] Refreshed option page UI using meta boxes
* [Fix] Prepared plugin for localization
* [Fix] Sanitized phone number and add a plus sign (+) prefix
* [Update] Font Awesome 4.7.0

= 1.1.2 =
* [Fix] Left aligned icons in the CSS [does-your-plugin-support-the-hemingway-theme](https://wordpress.org/support/topic/does-your-plugin-support-the-hemingway-theme/)
* [Update] Font Awesome 4.6.3

= 1.1.1 =
* [Fix] Admin styles
* [Fix] Public styles
* [Update] Font Awesome 4.6.1

= 1.1.0 =
* [Add] Option for Bar:Opening links in a new tab [no-instagram-icon](https://wordpress.org/support/topic/no-instagram-icon/)

= 1.0.1 =
* [Fix] Improved setting and contact validation (sanitization)
* [Fix] Set the default value of the fixed bar position to true
* [Fix] Removed obsolated workarounds

= 1.0.0 =
* [Upgrade] Official release

= 0.1.1 =
* [Fix] Default option issue during network activation

= 0.1.0 =
* [Add] Initial release



== Upgrade Notice ==

= 3.0.0 =
* Minimum supported PHP version is 5.6.20, you can rollback the plugin to its previous version if needed.

= 2.0.0 =
* Structure of the plugin's option has been changed, supported Font Awesome version is 5.

= 1.0.0 =
* Official release.
