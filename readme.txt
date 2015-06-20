=== In Memoriam (Light a Candle) ===

Contributors: nsfetcu
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BTBXYQ5JMC89J
Tags: in memoriam,light a candle,candles,candle,widget,plugin,wordpress,obituary, devotion, inspiration
Requires at least: 3.0.1
Tested up to: 4.2.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Light a candle and pray for a loved one, for a cause, or for yourself.

== Description ==

In Memoriam (Light a Candle) enables you and your site's visitors to easily and quickly light candles in the the memory of a died person, to support a cause, or even for your own wellness. The plugin integrates seamlessly with your existing WordPress admin area, so you will feel right at home.

There are a few simple ways to manage your candles:

1. Easy to use shortcodes when editing content in the WordPress admin area.
2. The built-in Candle Widget to display a specific or random candle
3. PHP code if you are a developer (see FAQ).

This plugin is in constant development. If you have any feature requests or questions, please feel free to submit them via the support forum.

= Features =

* Creates a "Candles" menu in your WordPress admin area which allows you to manage the submissions.
* Leverages the simplicity of WordPress shortcodes, allowing you to easily display your Candles wherever you like (see FAQ for individual shortcodes).
* Categorize candles any way you see fit and display those categories wherever you like.
* Creates a Candle Widget which allows you to display your candles in sidebar or widgetized areas.
* Display random candles using either a shortcode or the built-in widget.
* Ships with CAPTCHA support, but you can turn it off if you like
* Allows powerful customizations for developers.
* Setup for translation, so if you're a translator, get translating!
* Supports **WP-Paginate** if installed.

**Please Note:** Although any output In Memoriam (Light a Candle) generates is well structured, no styling is shipped out of the box. This means it is up to your theme to decide how the output will be styled.

If you have found this plugin useful, consider taking a moment to rate it, or perhaps even a small donation that will be used for charity.

= Supported languages =

*English by default). If you translated it in a need language please contact us to include it in the next release. If you find any mistakes in the translation, please contact us and we will make relevant corrections.*  

Romanian (ro_RO)

= Resources =

WordPress.org Plugin: http://wordpress.org/plugins/in-memoriam-light-a-candle/
Plugin Home: http://www.teleactivities.com/in-memoriam-light-a-candle-wordpress-plugin/
Plugin Demo: http://www.obituaries.ro/memorial/

== Installation ==

1. Log in to the control panel as admin.   
2. Go to Plugins Add New > Upload Plugin.     
3. Click `Choose file` (`Browse`) and select the downloaded zip file of the plugin.     
For Mac Users*: Go to your Downloads folder and locate the folder with the gallery plugin. Right-click on the folder and select Compress. Now you have a newly created .zip file which can be installed as described here.     
4. Click `Install Now` button.     
5. Click `Activate Plugin` button for activating the plugin. 
6. Use the shortcode [candles] to display all the candles in a page or post.
7. Use the shortcode [candle-submission-form] to implement an online form for the visitors and users to submit candles and prayers.
8. See FAQ section for more shortcodes.
9. Manage the candles from the control panel, using the menu Candles.

If you get an error something like "An internal error occurred: 518E5D10893F9.A6B37D5.9F002051", you have to replace the existing keys for reCAPTCHA with your own keys. For this:

a) Go to https://www.google.com/recaptcha/admin, register your site and get the Site key and Secret key.
b) Replace, in shortcodes.php, the existing keys from 
echo recaptcha_get_html('SITE_KEY') 
and 
recaptcha_check_answer('SECRET_KEY'

== Frequently Asked Questions ==

= Can candles be submitted by my visitors automatically? =

Yes. On top of your ability to add and edit candles in the WordPress admin area, you can also use a shortcode to display a form on any page you like. This form will create a new candle when a user fills it out. The shortcode to do this is [candle-submission-form]

When a user submits a candle, it will default to "Draft" status. You will need to publish any candle this way before it will display on your site. Just in case someone writes anything naughty!

= How do I display all of my candles? =

To display all candles with pagination, use the [candles] shortcode.

= How do I display a candle? =

To display a single candle you can use the [candle id="xyz"] shortcode, where "xyz" is the ID of the candle you wish to display.
You can copy and paste the candle shortcode complete with ID from your Candles admin page in WordPress.

= How can I display a category of candles? =

To display a category of candles (with pagination!), you can use the [candles category="xyz"] shortcode. where "xyz" is the ID of the candle category you wish to display.
You can also display candles from multiple categories by using [candles category="xyz,abc"] where "xyz" is the first ID and "abc" is the second. You can pass in as many ID's as you like, just separate them with commas.

You can copy and paste this shortcode complete with ID from the Candles -> Categories admin page in WordPress.

= Can I display a random candle? =

Yes, you can use the Candle Widget and specify the "random" option, or you can use the [candle id="random"] shortcode.

= Can I change the number of candles shown per page? =

Yes. Specify the "per_page" attribute when using the [candles] shortcode. EG, [candles category="10" per_page="5"].

= Available filters =

The following filters are available in In Memoriam (Light a Candle) so you can customise it to your liking.

1. new_candle_notification - Filter whether an email notification should be sent to the administrator when a new candle is submitted.
2. new_candle_email - Filter which email address which the new candle notification email is sent to. Defaults to administrator email.
3. new_candle_confirmation_message - Filter the text which is displayed when a new candle is received.
4. new_candle_failure_message - Filter the text which is displayed when a candle submission fails.
5. ct_disable_captcha - Return true to disable captcha on the candle submission form

= Available actions =

The following actions are available in In Memoriam (Light a Candle) so you can customise it to your liking.
1. ct_before_render_candle - Fires before a candle is rendered. Passes in $candle and $context
1. ct_after_render_candle - Fires after a candle is rendered. Passes in $candle and $context

= Information =

The following information might be handy for you to know.

1. Candles operate via a custom post type which is simply named "candle".
2. Candles are grouped in a custom taxonomy named "candle_category".
3. The Candle widget class name is "Candle_Widget" and of course extends WP_Widget.

== Screenshots ==

1. banner-772x250.jpg  - Banner 772x250 px
2. banner-1544x500.jpg - Banner 1544x500 px
3. icon-128x128.png    - Icon 128x128 px
4. icon-256x256.png    - Icon 256x256 px
5. icon.svg            - Icon SVG
6. screenshot-1.jpg    - Example use of the [candles] shortcode. This will display all candles with pagination.
7. screenshot-2.jpg    - Example use of the [candle-submission-form] shortcode. This shortcode will turn this page into a Candle submission page for your users.
8. screenshot-3.jpg    - Example use of the Candle widget.
9. screenshot-4.jpg    - Managing Candles section in control panel

== Changelog ==

= 1.0 =
* Initial release of plugin

== Upgrade Notice ==

No upgrade notice necessary


== Aknowledgement ==

I am not a programmer, but I needed this kind of plugin, so I built it starting from the plugin Clean Testimonials, https://wordpress.org/plugins/clean-testimonials/, with author: lukerollans. And now I want to share it with you.