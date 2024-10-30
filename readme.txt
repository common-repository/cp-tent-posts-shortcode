=== CP Tent Posts Shortcode ===
Contributors: brooks24
Tags: tent, tent.io, shortcode, widget, sidebar, feed
Requires at least: 3.4
Tested up to: 3.5-beta2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shortcode and widget to display a list of recent public Tent posts. Visit tent.io to learn about Tent.

== Description == 

This is a very very simple plugin. Use the shortcode to display a list of posts from a specified Tent server.

Tent is a social networking protocol. To use this plugin, insert the shortcode [cp-tent] into a post.

To specify your tent server, use the format [cp-tent tent="https://yourname.tent.is"].

To limit the number of posts displayed, use the format [cp-tent tent="https://yourname.tent.is" limit=10].

Here are a few more options you can set:

* tent: "https://yourname.tent.is"
* limit: 10
* must_mention: "https://yourname.tent.is"
* mentions: "off", "on"
* links: "on", "https://example.tent.is/posts/"
* header: "on"

'limit' is the number of posts to display. 
'mentions' is off by default. when 'on' a list of mentioned profiles is displayed. 
'links' is "on" by default (adds '/posts/' to the end of 'tent', and uses this url scheme to display a link to each post), or supply a new base url to precede the post id. 
'header' displays the user name and avatar above the list. 

This plugin will never authenticate with your tent server. It's only meant for public posts.

Because space is limited in the sidebar, no options are available for the widget.

== Changelog ==

= 1.0 =
* Improved status query
* Limit number of items in widget feed

= 0.1 =
* Added sidebar widget to display posts

= 0.0.3 =
* Improved the discovery of servers
* Follows redirects 1 time

= 0.0.1 =
* First version
