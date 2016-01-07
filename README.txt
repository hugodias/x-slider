=== X-Slider ===
Contributors: hugooodias
Tags: slider
Requires at least: 3.9.2
Tested up to: 4.4.1
Stable tag: 1.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple, beautiful, responsive and lightweight slider that don't suck.

== Description ==
The most famous sliders are only based on images and titles, but in most cases we use these sliders to display the posts, and there lies the problem.

Most plugins makes it very difficult to connect a slider to a post, and this is wrong !

A blog is made of posts and everything should revolve around them.

I present to you the X- slider, a slider based on posts! No complicated interfaces and billions of options that do not even use.

The X-slider was created with the purpose of being simple, easy to use, easy to set up and of course, easy to change, because it was made for Developers.

**Features**

* Beautiful full width responsive image slider.
* Extreme simple and efficient user interface.
* All slides work based on Posts. You attach a slide directly inside the post editor.
* Super easy to customize, as a developer you'll love it.
* Super easy to add, change and remove the slides. The admin user will also love it.
* Shortcode for showing the slider anywhere

== Installation ==
1. Upload `x-slider` to the `/wp-content/plugins/` directory
1. Activate the plugin through the \'Plugins\' menu in WordPress
1. Go to **"X-Slider settings"** on your blog sidebar for tweak some options
1. Enter in any post and locate the **"Slider image"**
1. Click on **"Choose image"** and upload your image
1. If it\'s good to go, check the **"active"** checkbox and save your post
1. In any place of your theme call the function: `x_slider()`
1. Done!


== Changelog ==

= 1.4.2 =
* Added schema.org markup

= 1.3.1 =
* Compatible with Wordpress 4.4

= 1.3.0 =
* Added query feature to match Wordpress Wp Query style. Now you can query your sliders the way you already know: using
the wp query style.
* Added a FAQ section on the docs

= 1.2.0 =
* New Featured: Slider Shortcode. Usage: [x-slider]
* Settings page: Added a getting started section
* Bugfix: Some CSS and Javascript changes for better compatibility with other themes
* Added some screenshots in plugin page

= 1.1.1 =
* Bugfix: Checkbox not working correctly on settings page

= 1.1.0 =
* Added width, height and crop options to settings page
* Minor admin layout changes

== Screenshots ==

1. The slide upload box lives is located inside the post page. Easy and quick to use.
2. After uploading the image, you can choose to show or not on the slider.
3. The settings page has only essential options , we do not want to confuse the user.
4. Create a beautiful, responsive and lightweight slider in seconds.


== Frequently Asked Questions ==

= How to install this plugin and getting it working on my theme? =
First of all you'll need yo install this plugin. Go to your plugins page on your Wordpress dashboard and search for X-slider.
After installing you just need to set it in your theme.
Open your favorite Text editor wherever you want to display it, for example, if you want to display on the top of every page,
open your header.php theme and inside the <body> tag, insert the following code:

`<?php x_slider(); ?>`

= How to filter posts showing on slider? =

You can use [WP_Query](https://codex.wordpress.org/Class_Reference/WP_Query) syntax on X-slider, so let's say you want to only show slides for the "Featured" category,
you should write:

`<?php x_slider(array('category_name' => 'featured')); ?>`

= I want to change the order, how to do that? =

As already mentioned before, you can use [WP_Query](https://codex.wordpress.org/Class_Reference/WP_Query) syntax for customizing your slider.
Ex: Show only posts from "Featured" category ordered by date limiting to 5 slides max

`<?php x_slider(array('category_name' => 'featured', 'orderby' => 'date', 'order' => 'DESC', posts_per_page => 5)); ?>`

= How to change the slider image dimensions? =

Accessing X_slider settings page right on the sidebar, click in "X-Slider Settings", on the "Slider Upload Options"
section you'll be able to change the width and height settings.

= How to change the slider timeout? =

On settings page :)

= This slider is too ugly, how to customize it? =

You can write your own CSS if you want to. The slider structure is very simple. Basically it's a div with the x-slider class ".x-slider"
with a UL and the slides are LI's with a container wrapping a title, description and a button.

= Can i have multiple sliders on the same page? =

Sure, just call `<?php x_slider(); ?>` as many times you want to.
