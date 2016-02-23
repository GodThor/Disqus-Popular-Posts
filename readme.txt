=== Disqus Popular Posts ===
Contributors: godthor
Tags: disqus, comments, widget, posts, shortcode
Requires at least: 3.0.1
Tested up to: 4.4.2
Stable tag: 2.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a widget to show the most popular posts and pages on your site based on Disqus comments. Can also be used with a shortcode.

== Description ==

This will create a new widget which will display your most popular posts and pages based on comment count with Disqus. Alternatively, you can use a shortcode as well.

It's very simple to use. Just drag the widget into a sidebar and configure it, or visit the settings under the WordPress Settings menu.

**Just Some of the Options:**

* Show featured image.
* Choose featured image size.
* Choose featured image alignment.
* Show the post date.
* Select how many days to check, IE: past 90 days.
* Set how many posts to show.
* Save the results for faster loading.
* Load results using Ajax. Perfect to avoid caching results.

**Note:** This plugin requires you have an application registered with Disqus: https://disqus.com/api/applications/
It's free and simple to setup, just follow that link.

== Installation ==

1. Upload `dpp.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place the Disqus Popular Posts widget into a sidebar.
4. Configure the widget. This requires you have an application registered with Disqus: https://disqus.com/api/applications/

== Frequently Asked Questions ==

= Why are no posts showing up? =

1. Verify you have entered the correct Disqus API Key and Disqus Shortname.

2. Take a look at the setting under Posts for Count Over How Many Days. If you have that set to a low value then try increasing it. What will happen is if you have that set to something like 1 day but you didn't have any comments during that time then you will have no results and nothing will show. The same is true if you are saving results and notice the last update was a long time ago. If it has nothing new in that time period then it will not update.

= I have results set to save and update every X hours but it's not updating. =

Follow the steps in the above question, Why are no posts showing up?

= How do I adjust the look of the results beyond the options available in the widget? =

Each element within the results has its own class. You can edit your theme's CSS and put in entries for the classes used in the results.

* **Article Container Class** = dpp_result (div that holds all the below items)
* **Featured Image Class** = dpp_featured_image
* **Post Title Class** =  dpp_post_title
* **Article Date Class** = dpp_post_date
* **Article Comments Class** = dpp_comments

Because the widget creates a `style` entry on the `div` tags, you may need to use the `!important` flag on your CSS entries to override any margins you may be setting.

== Screenshots ==

1. An example of the widget being shown on the site.

== Changelog ==

= 2.0.4 =

Released: February 23rd, 2016

* **Bug Fixes**

	* Code cleanup to remove warnings that would appear with WP_DEBUG on.

= 2.0.3 =

Released: February 4th, 2016

* **Updates**

	* Just adjusting some code documentation, and WordPress 4.4.2 validation.

= 2.0.2 =

Released: September 14th, 2015

* **Updates**

	* The *Clear All Saved Data* option under *Debugging* relabled *Clear All Saved Results* to be more clear and a note provided to explain the feature better.

* **Bug Fixes**

	* The *Clear All Saved Results* feature will now work properly.

= 2.0.1 =

Released: June 16th, 2015

* **Updates**

	* Changed the logic for showing an article to only show it if it has a title to display and or an image. This will avoid showing odd results if the page can't be found for whatever reason.
	* Moved the text for *Comments Text* to be outside the link to comments. Disqus automatically updates the text inside the link to comments if it's being loaded and will use the settings you define in Disqus. By moving this text outside the link it will show up regardless of what is set in Disqus.
	* *Comments Text* will no longer default to the word Comments if left blank. You can now delete the text to have nothing show.

* **Bug Fixes**
	
	* The widget will now properly save the information the first time you drag it into the sidebar. Previously it wouldn't save the first time and you had to enter information a second time.

= 2.0.0 =

Released: May 8th, 2015

**Note:** After upgrading go to *Posts*->*Count Over How Many Days* and set that value. By default it will use 90 days if you don't set this.

* **New Features**

	* Disqus Popular Posts can now be done with a shortcode! You will find the configuration for this under the WordPress Settings menu.
	* Under *Debugging* there is now an option to *Clear All Saved Data*.

* **Updates**

	* Put a check in place where if results need to be pulled from Disqus and it fails then it will instead show saved results if you have them.
	* Added a *Disqus API Call URL* to the *Debugging* area to help track down any issues.
	* Changed the field for *Count Over How Many Days* under *Posts* to a drop down. I had not realized Disqus offers only certain values to be used here, so the drop down only offers the values available. If you had problems getting posts to show up before then this may be a likely reason. **You need to save this option once you upgrade.**
	* Lots of coding changes and optimizations to make future versions easier to implement.
	* Added some more questions to the FAQ section.

* **Bug Fixes**

	* Fixed the error that would appear when adding the widget. It was trying to connect to Disqus without any settings defined yet.
	* Settings if using more than one widget will now work correctly. Previously the settings of one widget would be applied to all widgets when being displayed.
	* Results if using more than one widget will now also properly save per-widget.
	* Fixed the error that showed up if it tried to get results from Disqus and failed.

= 1.7.0 =

Released: April 16th, 2015

* **Updates**

	* Setup a proper class called dpp instead of having a bunch of functions.
	* Added more function documentation.
	* Redid this readme file to be easier to read through.

= 1.6.3 =

Released: March 20th, 2015

* **New Features**

	* *Debugging* area on the widget that displays useful information I can use to diagnose issues with the widget.

* **Bug Fixes**

	* Added fallback methods for getting a post ID from Disqus to use to show the featured image and post date.
	* Put a check in place if results are set to save but the period of time to save them is not set.
	* The shown article title now pulls from WordPress - if a post ID can be fetched, instead of using the one return by Disqus because sometimes Disqus won't have the current title correct.

A big thanks to [icstee](https://profiles.wordpress.org/icstee/) for helping me with fixing the bugs.

= 1.6.2 =

Released: March 19th, 2015

* **Updates**

	* Only render the featured image `div` and post date `div` if there are values to show.

* **Bug Fixes**

	* Featured images and post dates should now show if they weren't previously.

= 1.6.1 =

Released: March 19th, 2015

* **Bug Fixes**

	* I had a bug introduced in 1.6.0 (like 2 hours ago!), that would disallow you from managing other widgets in your sidebar that appeared below this one. All fixed now and one of these days I can push out an update without needing to immediately fix issues...

= 1.6.0 =

Released: March 19th, 2015

* **New Features**

	* See when saved results were last updated. Shown under the *Posts* group.
	* You can change the text that appears beside the comment count. Previously it would always show as something like: 12 Comments. Now you can change that word **Comments** to whatever text you want. Especially useful for non-English sites. This option appears under the *Options* group.
	* Ability to set the spacing below the article titles. Shown in *Styling* group.
	* Ability to set the spacing below the article date. Shown in *Styling* group.

* **Updates**

	* Moved CSS styling related features to a new *Styling* group in the widget.
	* Changed the date formatting for saved results to use your local time. All saved results will be updated immediately once you update to this version.
	* Coding changes to speed up the article results displaying faster by moving unnecessary code outside the loop.
	* Changed the results to put each item (image, title, date, comments), into their own `<div>` with a class given. For those inclined, you can modify your theme's CSS to Updated those elements now. Further information is now included in the FAQ.

= 1.5.1 =

Released: March 9th, 2015

* **Updates**

	* Changed the *Display Article Title* default from &lt;b&gt; to &lt;strong&gt;

* **Bug Fixes**

	* Corrected an issue with a missing quote which could cause buggy display.

= 1.5.0 =

Released: March 9th, 2015

* **New Features**

	* Ability to set the spacing between articles.
	* Ability to set the space between the featured image and article info.

* **Updates**

	* Added some explantion of values that are entered.
	* Reorganized the widget options into sections that can be toggled.

= 1.4.0 =

Released: March 7th, 2015

* **New Features**

	* Ability to adjust how the article's title is displayed.
	* *Featured Image Alignment* now has an option for none as well as left and right.
	* Link to rate and review now appears on the installed plugins screen.

= 1.3.0 =

Released: February 23rd, 2015

* **New Features**

	* Ajax Mode option. This will render the results using jQuery. Great if you're use caching and don't want the results cached.
	* Option to hide the comment count.
	* Moved around the widget options in a more logical manner and condensed the space used.

= 1.2.4 =

Released: February 19th, 2015

* Verified compatability with WordPress 4.1.1

= 1.2.3 =

Released: February 3rd, 2015

* **New Features**

	* PHP documentation.

* **Updates**

	* Rate and review link shows on the widget configuration for ease of access :)
	* Moved the screenshots to the assets folder instead of the plugin folder.

= 1.2.2 =

Released: January 25th, 2015

* **New Features**

	* Fail-safe if you are set to save the results and the results are empty then it will query Disqus to get results and save them.

* **Updates**

	* Unchecking *Save the Results* will now clear out any previously saved results when you reload your site. You can then check it off again, reload the site and in turn force the widget to query Disqus for updated results to save.

= 1.2.1 =

Released: January 25th, 2015

* **New Features**

	* Database version variable to allow saved results to be reloaded as needed with plugin version changes, like this version.

* **Updates**

	* Results from Disqus will now sort on most comments *overall* in the day range, not just most comments in the given period. If you had comments on an article prior to the period you gave, IE: 30 days, and comments within the day range then those articles could sort oddly in the results. Thanks to jrrera for pointing this out.

* **Bug Fixes**
	* The *Save the Results* option will now actually save results.

= 1.2.0 =

Released: December 30th, 2014

* **New Features**

	* Option to save the Disqus results. This will reduce API calls to Disqus for your application and load faster. You can configure how frequently these results refresh.

* **Updates**

	* Cleaned up the widget and added some more informative text for the features.

= 1.1.1 =

Released: December 29th, 2014

* **Updates**
	* Updated the readme.txt

= 1.1.0 =

Released: December 29th, 2014

* **New Features**

	* Option to show the post date.
	* Option for featured image alignment.

= 1.0.11 =

Released: December 23rd, 2014

* **Updates**

	* Removed the previous disqus.php and replaced it with dpp.php. Sorry about this. First time with SVN.

= 1.0.1 =

Released: December 23rd, 2014

* **Updates**

	* Renamed the plugin file to avoid it showing a link to settings for the Disqus plugin.
	* Reformatted this readme file.

= 1.0.0 =

Released: December 23rd, 2014

* Initial release.

== Upgrade Notice ==

= 2.0.4 = 

Code cleanup to remove warnings apppearing with WP_DEBUG turned on.

= 2.0.3 = 

Code documentation and WordPress 4.4.2 validation.

= 2.0.2 =

Bug fix for clearing saved results.

= 2.0.1 =

Bug fixes for the widget and minor updates.

= 2.0.0 =

Introduces a new shortcode ability!