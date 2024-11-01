=== Plugin Name ===
Contributors: Cattani Simone
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=S667YQ3Z93C7Q&lc=IT&item_name=Statpress%20Revolution&item_number=1&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedTags: stats, statistics, widget, admin, sidebar, visits, visitors, pageview, referrer, spy
Requires at least: 2.5
Tested up to: 3.0.1
Stable Tag: 1.2.1

StatSurfer is a Wordpress plugin, which allows you to surf easily and quickly in the statistics of your website. 


== Installation ==

Upload "statsurfer" directory in wp-content/plugins/ . Then just activate it on your plugin management page.


== Description ==

StatSurfer is the evolution of StatPress and StatPress Reloaded, the plugin is therefore consistent with the data of the two StatPress systems.
The plugin allows you to surf easily and quickly in the statistics of your website. Collect information about visitors, spider, search engines, browser, operating systems and more.

Once activated StatSurfer, the system will begin automatically  to collect data about visitors of your website. Thanks to the pages in the Admin Panel you’ll be able to see easily and quickly the movements of visitors.


= Dashboard Widget =

Thanks to StatSurfer and the widget for the WordPress’ Dashboard, you’ll be able to check your statistics without opening particular pages, a glance on the main page of administration is enough.


= Dates Selector =

Move back and forth in time with Dates Selector, decide the day you want to see, and get back to the statistics you desire.


= Goals =

Set goals to reach with Goals, a new function that will stimulate you more and more to enhance your site, constantly seeking to reach new records.


= Maps =

With Maps of StatSurfer you can check where users connect to your website, know from which country you receive the highest number of connections, all with the help of maps.


= StatSurfer Widget =

StatSurfer plugin include a widget for your sidebars, which lets you to show  data about your statistics to visitors. 
To use this widget you have to use the following APIs

* %thistotalvisits% - this page, total visits
* %since% - Date of the first hit
* %visits% - Today visits
* %totalvisits% - Total visits
* %os% - Operative system
* %browser% - Browser
* %ip% - IP address
* %country% - Visitor's country
* %visitorsonline% - Counts all online visitors
* %usersonline% - Counts logged online visitors
* %toppost% - The most viewed Post
* %topbrowser% - The most used Browser
* %topos% - The most used O.S.
* %thistotalpages% - Total pageviews so far
* %pagestoday% - Pageviews today
* %pagesyesterday% - Pageviews yesterday
* %latesthits%

Now you could add these values everywhere! StatPress offers a new PHP function *StatSurfer_Print()*.
* i.e. StatSurfer_Print("%totalvisits% total visits.");
Put it whereever you want the details to be displayed in your template. Remember, as this is PHP, it needs to be surrounded by PHP-Tags!


= StatPress Support =

StatSurfer is compleatelly compatible with StatSurfer systems. Your datas will not be lost: StatSurfer will use the StatPress's tabel of your DataBase.


== Frequently Asked Questions ==

= Have you a problem with StatSurfer? =

Contact icattaniweb@cattanisimone.it

= Will my StatPress datas be cancelled? =

No, data collected thanks to StatPress will be used to improve the statistics service.


== Screenshots ==

1. Dashboard Widget
<img src='http://www.cattanisimone.it/statsurfer/docs/screenshots/StatSurfer_dashboard.png' />

2. Datas Selector
<img src='http://www.cattanisimone.it/statsurfer/docs/screenshots/StatSurfer_overview_1.png' />

3. Overview
<img src='http://www.cattanisimone.it/statsurfer/docs/screenshots/StatSurfer_overview_2.png' />

4. Details
<img src='http://www.cattanisimone.it/statsurfer/docs/screenshots/StatSurfer_details.png' />

5. Spy
<img src='http://www.cattanisimone.it/statsurfer/docs/screenshots/StatSurfer_spy.png' />

6. Goals
<img src='http://www.cattanisimone.it/statsurfer/docs/screenshots/StatSurfer_goals.png' />

7. Maps
<img src='http://www.cattanisimone.it/statsurfer/docs/screenshots/StatSurfer_maps.png' />

8. Options
<img src='http://www.cattanisimone.it/statsurfer/docs/screenshots/StatSurfer_options.png' />

== Upgrade Notice ==

*Version 1.0*

*First public release

*Version 1.0.1*

*Fixed some calculation errors

*Version 1.0.2*

*Fixed errors with db's tables configuration

*Version 1.0.3*

*Fixed error with Windows Seven definition
*Fixed error with images' call in the map function for Linux User

*Version 1.0.4*

*Fixed an error on the Spy function

*Version 1.0.5*

*Fixed an error with ip information

*Version 1.1*

*Added new maps and updated the maps function to the new Google Service.
*Added the possibility to choose a map to display in the dashboard widget.
*Added the GPL License.
*Modified the file gestion for a better consultation.
*Fixed some errors when a server use a proxy system.
*Fixed some errors on the auto configuration of the StatPress tables.

*Version 1.1.1*

*Fixed errors on file includes

*Version 1.1.2*

*Fixed some errors in the code syntax

*Version 1.2*

*Operative Systems' improved definition
*Improved graphics' upload for a better display data
*Reorganization of tables and graphics of the detail.php
*Synactic reorgnization
*Correction of syntactic errors
*Correction of errors in append.php

*Version 1.2.1*

*Fixed a fatal error in the functions library*