=== Loading Page with Loading Screen ===
Contributors: codepeople
Donate link: http://wordpress.dwbooster.com/content-tools/loading-page
Tags:loading page,loadin screen,animation,page performance,page effects,performance,render time,wordpress performance,image,images,load,loading,lazy,screen,lazy loading,fade effect,posts,Post,admin,plugin,fullscreen,ads
Requires at least: 3.0.6
Tested up to: 7.0
Stable tag: 1.2.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Loading Page with Loading Screen plugin performs a pre-loading of images on your website and displays a loading progress screen with percentage of completion. Once everything is loaded, the screen disappears.

== Description ==

Loading Page with Loading Screen features:

→ Displays a screen showing loading percentage of a given page
→ Includes multiple loading screen alternatives (Bar, Logo, and Text) you can choose from the plugin settings page
→ Displays the page's content with an animation after complete the loading process
→ Increase the WordPress performance
→ Allows to select the colors of the loading progress screen,
→ As background colors and images
→ Allows to display or remove the text showing the loading percentage
→ Pre-loads the page images

Loading Page with Loading Screen plugin performs a pre-loading of image on your website and displays a loading progress screen with percentage of completion. Once everything is loaded, the screen disappears.

**More about the Main Features:**

* Displays a screen showing loading percentage of a given page. In heavy pages the "Loading Page with Loading Screen" plugin allows to know when the page appearance is ready.
* Allows to display the loading screen on homepage only, or in all pages of website.
* Allows to select the colors of the loading progress screen, or select images as background. By default the colour of loading screen is black, but it may be modified to adjust the look and feel of the loading screen with website's design.
* Allows to display or remove the text showing the loading percentage.

The base plugin, available for free from the WordPress Plugin Directory, has all the features you need to displays an loading screen on your website.

**Premium Features:**

* Allows to choose a loading progress screen. The premium version of plugin includes additional loading screens not available in the free version.
* Allows to select from multiple possible animations, to display the page's content after complete the loading process.
* Improves the page performance.
* Lazy Loading feature allows to load faster and reduce the bandwidth consumption. The images are big consumers of bandwidth and loading time, so a WordPress website with multiple images can improve its performance and reduce the loading time with the lazy loading feature.
* Allows to select an image as a placeholder, to replace the real images during pre-loading. It's recommended to select the lighter images possible to increase the WordPress performance, the image selected will be used instead of the original images, in the loading page process.

**Demo of Premium Version of Plugin**

[https://demos.dwbooster.com/loading-page/wp-login.php](https://demos.dwbooster.com/loading-page/wp-login.php "Click to access the Administration Area demo")

[https://demos.dwbooster.com/loading-page/](https://demos.dwbooster.com/loading-page/ "Click to access the Public Page")



**What is Lazy Loading?**

Lazy Loading means that the original images are not loaded until finalize the loading of page. This action improves the download speed of webpages.

If you want more information about this plugin or another one don't doubt to visit my website:

[http://wordpress.dwbooster.com](http://wordpress.dwbooster.com "CodePeople WordPress Repository")

== Installation ==

**To install Loading Page with Loading Screen, follow these steps:**

1. Download the zipped plugin.
2. Go to the **Plugins** section on your Wordpress dashboard.
3. Click on **Add New**.
4. Click on the **Upload** link.
5. Browse and locate the zipped plugin that you have just downloaded.
6. Once installed, activate the plugin by clicking on **Activate**.

== Interface ==

To use Loading Page with Loading Screen on your website, simply activate the plugin. If you wish to modify any of the default options, go to the plugin's Settings. They can be found either by going to Settings > Loading Page on your Wordpress dashboard, or by going to Plugins; a link to Settings can be found in the plugin description.

The Loading Page with Loading Screen setup is divided in two sections: the first one is dedicated to the activation and  setup of the loading screen, and the second to the delayed loading of the images that are not shown immediately ( images that require on-page scrolling in order to be seen).

**Loading Screen Setup**

The setup options for the loading screen are:

* **Enable loading screen**: activates preloading of images and displays a loading screen while the webpage is loading.
* **Display the loading screen once per session**: display the loading screen only once per session.
* **Display the loading screen on**: display the loading screen with all screens sizes, or if the screens sizes satisfy the conditions.
* **Display loading screen only in**: displays a loading screen only on homepage, all pages, or specific pages or posts. In the last case the IDs of pages or posts should be separated by comma symbol ","
* **Devices**: Allows selecting the devices where to show the loading screens (Desktop, Mobile, or Both).
* **Exclude the loading screen from**: excludes the loading screen from pages or posts whose IDs are entered separated by comma symbol ","
* **Select the loading screen**: allows to choose a loading screen. The premium version of plugin include multiple loading screens.
* **Select background color**: allows to select the background color for your loading screen compatible with the design guidelines of your website.
* **Select images as background**: allows to display an image as loading screen background, the image can be displayed tiled or centered.
* **Display image in fullscreen**: allows to adjust the background image in fullscreen mode.
* **Select foreground color**: Allows to select the color of the graphics and texts that display the loading progress information.
* **Additional seconds**: Allows to add seconds before remove the loading screen at the end of the load process.
* **Include an ad, or your own block of code**: Allows to add ads, or other block of code, to the loading screen.
* **Apply the effect on page**: Display the page's content with an animation after complete the loading process.
* **Display loading percent**: Shows the percentage of loading. The loading percent is calculated in function of images in the page.

* **Troubleshoot Area - Loading Screen**: allows disabling/enabling the search in deep.

**Lazy Loading Setup (in premium version only)**

The options to set up Lazy Loading and increase the WordPress performance are:

* **Enable lazy loading**: Enables the delayed loading of images outside of the current viewing area of the user improving the rendering time of complete page.
* **Select the image to load by default**: Choose an image to be shown as a placeholder of the actual images, the loading of which will be delayed. It's recommended the selection of a light image to increase the WordPress performance.

* **Troubleshoot Area - Lazy Loading**: allows entering some texts to exclude the images tags with the entered texts in the classes or attributes.

== Frequently Asked Questions ==

= Q: What to do if the loading screen is stopping in determined percentage? =

A: Tick the checkbox: "Disable the search in deep", in the "Troubleshoot Area - Loading Screen" section of the settings page.

= Q: How to use a custom image for the loading progress? =

A: From the settings page of the plugin, selects the "Logo Screen" in the list of available screens, and select the image to use in the loading screen through the new input field associated to the "Logo Screen".

= Q: How the lazy loading increase the WordPress performance? =

A: The lazy loading doesn't load the website images until images be in the viewport. If the user never scrolls the webpage, some images won't download with a reduction in the bandwidth consumption.

= Q: I've installed a plugin for images galleries, that applies a lazy load to the images. How to prevent a conflict with the lazy loading of the "Loading Page" plugin? =

A: Simply, identify a class name, or the value of an attribute applied to the images tags by the gallery, and enter the text through the attribute: "Exclude images whose tag includes the class or attribute" in the "Troubleshoot Area - Lazy Loading" section of settings page.

= Q: Could I display the loading screen on homepage only? =

A: Yes, that's possible. Go to the settings page of plugin and check the option "homepage only".

= Q: Is possible display the loading screen in some pages only? =

A: Yes, that's possible. Go to the settings page of plugin and check the option "the specific pages", and enter the posts or pages IDs, separated by the comma symbol ",". Another alternative would be to select the "all pages" option and enter the pages' URLs in the text area (one URL per row). Note you can enter partial URLs by using wildcard symbols *.

= Q: Might I display an image as loading screen background? =

A: Yes, that's possible. Go to the settings page of plugin and select the image in the option "Select image as background". The image can be displayed tiled or centred.

= Q: Are the loading screens supported by all browsers? =

A: There are some loading screens that require of the canvas object, all modern browsers include the canvas object. The screens with special requirements display a caveat text when are selected.

= Q: Can I exclude some links from the "Show loading screen when clicking on link"? =

A: Yes, that's possible. Please assign the no-loading-screen class name to the links where you want to deactivate the loading screen on the origin page.

= Q: Why can't I see the animation effect after complete the loading screen? =

A: Please be sure you are using a browser with CSS3 support.

== Screenshots ==
1. Loading Page Preview
2. Loading Screen Available
3. Benefits to use Lazy Load
4. Plugin Settings

== Changelog ==

= 1.1.0 =

* Implements the loading_page_settings filter to allow you to customize the Loading Page settings at the runtime. The callback receives two parameters: an array with the loading page settings and the URL of the page that is being visited.

= 1.1.1 =

* Accept style and link tags in the Ads block.

= 1.1.2 =

* Identifies links with window.open.

= 1.1.3 =

* Modifies the Show loading screen when clicking on link feature.

= 1.1.6 =
= 1.1.5 =
= 1.1.4 =

* Hides the logo image tag in the Logo Loading Screen until it is loaded.

= 1.1.7 =

* Removes deprecated JS code.

= 1.1.8 =

* Modifies the method of detecting search engine crawlers to avoid conflicts with cache management plugins.

= 1.1.9 =

* Modifies the module that handles the session variables.

= 1.1.10 =

* Modifies the plugin installation process.

= 1.1.11 =

* Fixes a conflict in the activation process with WP6.5.

= 1.1.12 =

* Manages possible exceptions generated by the parse_ini_file function.

= 1.1.13 =

* Includes a new attribute in the plugin settings to allow displaying the loading screen on pages filtered by URLs.

= 1.1.14 =

* Modify the module that displays the loading screen in onclick events to exclude links with mailto and tel schemes, and links with non-loading-screen class names.

= 1.1.15 =

* Modify the links to exclude checking by javascript code in the href attributes.

= 1.1.16 =

* Improves the use of the internationalization functions.

= 1.1.17 =

* Implements Text Loading Screen. The Text loading screen allows you to configure a loading screen animating the website's title or any other short text you enter through the plugin settings page.

= 1.1.18 =

* Prevents to display loading screen with Skype links.

= 1.1.19 =

* Allows displaying the loading screen even on websites with the LiteSpeed Cache plugin active.

= 1.1.20 =

* Introduces a new feature in the plugin settings that enables users to adjust the transparency percentage of the loading screen's background color.

= 1.2.0 =

* Modifies the plugin to prevent overlapping loading screens that occur when the browser's reload button is pressed multiple times.

= 1.2.1 =

* Allows you to specify custom width and height attributes for the logo screen image.

= 1.2.2 =

* Updates the Elementor integration to include exceptions for coming soon and maintenance modes based on user login status and roles.

= 1.2.3 =

* Implements support for the force parameter in the cp_loadingpage.onLoadComplete method. It hides the loading screen even when additional seconds where entered through the plugin settings page.

= 1.2.4 =

* Refines plugin behavior for the loading screen by ensuring the logo is visible whenever a user navigates to another page via links.

= 1.2.6 =
= 1.2.5 =

* Fixes a minor issue with code block.

= 1.2.7 =

* Triggers a resize event after hiding the loading screen, allowing third-party plugins to dynamically adjust the page's elements.

= 1.2.8 =

* Improves the WordPress 7.0 integration.