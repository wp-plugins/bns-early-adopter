=== BNS Early Adopter ===
Contributors: cais
Donate link: http://buynowshop.com
Tags: admin, widget-only
Requires at least: 3.2
Tested up to: 3.9
Stable tag: 0.8
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Show off you are an early adopter of WordPress

== Description ==
Show off you are an early adopter of WordPress by displaying a message showing what version you are running (alpha, beta, release candidate and/or stable versions).

== Installation ==
Read this article for further assistance: http://wpfirstaid.com/2009/12/plugin-installation/

----
= Shortcode: bnsea =
The parameters are very similar to the widget

* 'title'       => Can be used as a lead-in message, remember to wrap phrases in double-quotation marks
* 'show_alpha'  => false by default, use true to activate
* 'show_beta'   => false by default, use true to activate
* 'show_rc'     => false by default, use true to activate
* 'show_stable' => false by default, use true to activate
* 'only_admin'  => false by default, use true to activate

NB: Custom CSS may be required when this shortcode is used with some Themes.

== Frequently Asked Questions ==
= Q: Why would I want to use this plugin / widget? =
To show your faith in WordPress code and the developers who write it.

= Q: My custom styles are no working, what happened? =
WordPress essentially removes the existing files and folders of a plugin and replaces them with the new updated package, including any custom stylesheet you may have added. This would remove you old customizations. To future proof these additions you can now use the `/bns-customs/` folder (you may need to create this folder) under `/wp-content/`.

== Screenshots ==
1. Default widget options panel

== Other Notes ==
* Copyright 2012-2014  Edward Caissie  (email : edward.caissie@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License version 2,
  as published by the Free Software Foundation.

  You may NOT assume that you can use any other version of the GPL.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

  The license for this software can also likely be found here:
  http://www.gnu.org/licenses/gpl-2.0.html
  
* Please note, support may be available on the WordPress Support forums; but, it may be faster to visit http://buynowshop.com/plugins/bns-early-adopter/ and leave a comment with the issue you are experiencing.

== Upgrade Notice ==
Please stay current with your WordPress installation, your active theme, and your plugins.

== Changelog ==
= 0.8 =
* Released May 2014
* Added optional shortcode_atts filter variable
* Added `plugin_data` method
* Added new FAQ referencing the `/bns-customs/` folder location
* Define location for BNS plugin customizations
* Move to use generic folder for all "BNS" plugins to use
* Updated required version of WordPress to 3.6
* Updated compatibility version
* Updated copyright years

= 0.7.1 =
* Released December 2013
* Code Reformatting and version compatibility update

= 0.7 =
* Released July 2013
* Corrected Administrator Only conditional and added admin only classes
* Added filter for "Administrator ONLY View" text

= 0.6.1 =
* Released April 2013
* Fixed conditional logic used to display plugin
* Refactored where $ea_display boolean value is set

= 0.6 =
* Release February 2013
* Added code block termination comments
* Added more i18n compatibility
* Change to PHP5 style class code format
* Changed constructor function name (BNS_Early_Adopter_Widget) to __construct (i.e.: PHP5 code format)
* Moved all code into class structure
* Moved `bnsea_display` out of `widget` method
* Pushed minimum required WordPress version to 3.2 to handle PHP5 requirement
* Renamed `BNSEA_Scripts_and_Styles` to `scripts_and_styles`

= 0.5 =
* Release November 2012
* Documentation updates
* Optimized output buffer code used in the shortcode function
* Remove load_plugin_textdomain as redundant

= 0.4.2 =
* Documentation updates
* Programmatically add version number to enqueue calls

= 0.4.1 =
* Compatible with WordPress 3.4.1

= 0.4 =
* Fix issue of plugin not displaying with stable versions

= 0.3.1 =
* Added `margin: 0` to 'h3.bnsea-output'
* Updated screenshot

= 0.3 =
* Added `release candidate` option

= 0.2 =
* Added conditional display widget check: if no version is checked, or no matching version is found the widget will not be displayed.
* Cleaned up grammar conditional statements
* Added only show administrators option
* Added shortcode support
* Added screenshot of default widget option panel

= 0.1 =
* Initial Release.