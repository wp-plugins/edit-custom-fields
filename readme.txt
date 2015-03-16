=== Plugin Name ===
Contributors: topdownjimmy
Tags: custom fields, customfields, custom field, customfield, admin
Requires at least: 3.5.1
Tested up to: 4.1.1
Stable tag: 0.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple interface to edit or delete Custom Fields.

== Description ==

This plugin offers a simple interface to edit or delete any Custom Field names/keys you may have created in the post/page editor.

Deleting a Custom Field *also* deletes the associated content, so use with caution!

This is not meant to be a powerful plugin in the vein of [Advanced Custom Fields](http://www.advancedcustomfields.com/).

Only users with role Editor and above have access to this plugin.

== Frequently Asked Questions ==

== Upgrade Notice ==

== Screenshots ==

== Installation ==

1. Upload `edit-custom-fields.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 0.1.4 =

- Fixed regression from 0.1.2 onward that broke plugin when custom field key was
	used in multiple posts.

= 0.1.3 =

- Removed broken function that broke 0.1.2
- Fixed some Markdown formatting errors in readme.

= 0.1.2 =

- DUH: Allowed for db table prefixes other than `wp_` :self-flagellates:
- Embarrasing typo on "Confirm Custom Field Deleteion" screen :S
- Querying by ID now rather than key so that keys with spaces work smh

= 0.1.1 =

Fixed the stable tag in the readme.txt file.

= 0.1 =

Initial release.
