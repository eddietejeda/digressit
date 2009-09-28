=== digress.it ===
Contributors: visudo
Donate link: http://digress.it
Tags: comments, annotation, discussion, commenting, documents
Requires at least: 2.6
Tested up to: 2.8.4
Stable tag: 2.1.8

digress.it lets you comment paragraph by paragraph in the margins of a text

== Description ==

digress.it lets you comment paragraph by paragraph in the margins of a text

*	Floating Comment Box: Comment box not only stays with you in the right margin as you scroll, but you can resize and position it anywhere on the page. Expand and collapse comment threads by clicking paragraphs, or by selecting from the in-box menu.
*	Configurability: Choose from a variety of settings for presenting table of contents of your doc, plus appearance and mobility of the comment box. digress.it comes with its own default theme but already works well with a handful of popular WP themes. Help us expand that number!
*	Trackable discussions: four types of RSS feeds: full site, individual page, individual paragraph and even individual commenter.
*	Real-Time Notifications: See other readers' comments appear in real time without having to refresh the page.
*	Uber-addressability: Permalinks provided for each paragraph and comment. Reference from elsewhere, or cross-reference from within.
*	Paragraph Embedding: Embed paragaphs YouTube-style on other sites. (Hey Ted Nelson!)


== Installation ==

1. Upload `digressit` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Appearance menu and review the settings. The digress.it theme should have been automatically activated.
== Changelog ==


= 2.1.8 =
* RSS minor switch: change "on paragraph number #9" to "(paragraph no. 9)"
* FIXED: The embed Object code was not loading properly when clicked. Now it does
* ENHANCEMENT: html embed is now a blockquote instead of div
* FIXED: alert would appear when set to classic mode
* ISSUE#4 FIXED CommentBrowser now much clearer
* ISSUE#1 FIXED empty tags generate empty selection paragraph
* ISSUE#12 FIXED typo when outputting xml in embed code
* NEW: added support for google frame when available
* NEW: stylized debug mode
* If simpleXml fails it automatically reverts to regexp parsing
* ISSUE#3 FIXED comment appearing multiple times fix
* ISSUE#8 FIXED The Comments by Section page, displays the sections in reverse order
* Removed debug code to measure function speeds
* Removed mu-plugins support. For mu-support use Plugin-Commander
* FIXED: commentcount and commenticon positioning IE6
* FIXED: highlight scales properly in IE6/7
* NEW FEATURE: added the ability to enable Digress.it with different post status for private review.

= 2.1.7 =
* containerTable is now transparent so that the round edges on commentbox stay round over text
* jquery/utils not loaded in when js is compressed, which caused IE to fail
* positioning of commentbox relative
* better detection of previous commentpress installation to properly upgrade
* In addition to JSON, HTML, TEXT, please provide an RSS switch for each paragaph
* in digress.it.embed on line 34, the post was not being parsed with embed param set to true, which doesn't print all the comment crud
* Changed 'Comments by post' to 'Comments by section' - The Table of Contents refers to them as sections, so this would be consistent.
* Embed code changed to include link to paragraph to produce trackbacks
* resizing works a bit better. resize bars would get lost of they were set smaller than min-height of commentbox
* Fixed bug MU install which caused it to spit out a blank space before headers were ready
* support for language localization

= 2.1.6 =
* Disabled polling in IE, which caused it to break on all version. Fixed now!

= 2.1.5 =
* Text content now expands and shrinks depending on screen. commentbox box now uses percent instead of pixel to position accordingly
* removed #embed-code anchor tags, which did nothing, instead using javascript:return
* FIXED: unapproved comments appeared on the comment bubble count. now they don't
* Styling on blog titles, navigation, fontsizes made a bit cleaner
* enhanced the default and classic themes
* MAJOR: Safely upgrades from CommentPress
* FIXED: A bug in positioning when there was no skin
* FIXED: pingbacks are properly styled
* better support from chrome
* user can select what page appears in the front page
* Javascript now printed on footer for better load time
* deactivate all in MU uninstalls the mu link
* MAJOR: trackbacks now appear on the comment stream with proper paragraph
* working on better support for IE6/7 (renders properly but still has features disabled)
* paragraph and user rss feeds work when using directory based hosting

= 2.1.4 =
* FIXED: The right-floating paragraph level rss feed was breaking thread behavior

= 2.1.3 =
* pages/archives/search have a bit better styling. doesn't break sidebar
* password protected pages load safely
* removed broken stylesheets. will add later


= 2.1.2 =
* notice to join community more prominent. it appears on the "posts" page and users have options to hide or the options page, where it remains
* CommentBrowser widget is enabled by default
* Users have ability to define URL for community server. Server will be released open source when ready.
* Communication with community server now has password
* install now puts an install file in the mu-plugins folder to ensure plugin is loaded properly in MU
* FIXED: in webkit(safari) when no cookie is set fixed position did not work.
* There is now a nice fade effect when unselecting a paragraph
* There is now an option to make the sidebar can now appear on frontpage
* userfeed prints out user name in title
* new paragraph feed appears on the commentbox
* Parsing now down with XPath..which allows for more complex structures. If there is an error it reverts back to Regular Expression.
* Nested tags work now (thanks to XPath). i call force_balance_tags to make sure tags match then load into simplexml then use xpath. if xpath throws and error revert to old regexp
* FIXED: bug that prevented posts from appearing under "whole page"
* FIXED: on commentbrowser/listposts, we were not getting all posts. now limit is removed
* Archive pages look a bit better
* FIXED: empty posts no longer spit out errors. instead print a message saying the post is empty

= 2.1.1 =
* Initial release

