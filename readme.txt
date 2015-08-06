VideoEasy Filter
=========================================
VideoEasy is a filter that replaces links to media files, with  html5 players. Primarliy intended for html 5 video, it will also work for audio, youtube or rss links. The Video Easy filter uses templates to support multiple players, and allows the user to add new players or customize existing ones, all from the Video Easy filter settings pages. By default players are already defined, and can be used as is, for:
Video.js, Sublime Video, JW Player, Flowplayer and MediaElement.js

But preset templates are available for other players, and you are encouraged to use the existing players and presets as examples, and make your own templates.

Installation
=========================================
If you are uploading videoeasy, first expand the zip file and upload the videoeasy folder into:
[PATH TO MOODLE]/filters.

Then visit your Moodle server's Site Administration -> Notifications page. Moodle will guide you through the installation. On the final page of the installation you will be able to register templates. Since there are 15 template slots available, and each has a lot of fields, You should just scroll to the bottom and press "save." After this each template has its own settings page and it is much easier to work with the settings that way.

After installing you will need to enable the videoeasy filter. You can enable the videoeasy filter when you visit:
Site Administration / plugins / filters / manage filters

Finally you will need to associate a player/template with each of the file extensions, and check the file extensions that VideoEasy should handle.
Do this at: Site Administration / plugins / filters / VideoEasy / General Settings

(NB Sublime Video and JW Player, require you to register on their sites, and you will be given a license code. You will need to enter that into the template for those players. See "Templates" in this document.)

Configuration
=========================================
Configure the site wide settings  and define/edit templates at 
Site Administration / plugins / filters / Video Easy

On the general settings page you need to check the filetypes you wish the Video Easy filter to handle at the top of the settings page, and select the player template(drop down list) that will handle that file extension.

Many player templates will require JQuery. We used to load this as required. And you still can. (The checkbox for that is still on each template page.) But that is now unchecked by default. Please do not use it. It will be removed in a subsequent version. Instead you should use a theme that loads JQuery already (Essential, BCU are two), or add a call to load JQuery to the Moodle site header.
To add that, go to: Site Administration -> Appearance -> Additional HTML (within HEAD) ,and add:
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>

NB You should TURN OFF file handling for any extensions you select here in the Moodle Multi Media Plugins filter, and the PoodLL filter if they are installed.
Multi Media Plugins filter settings can be found at:
Site Administration / appearance / media embedding

PoodLL filter settings can be found at:
Site Administration / plugins / filters / PoodLL

Local Configuration
========================================
One of the strengths of Video Easy is that it makes use of the under utilized Moodle feature that allows you to configure filters at the course and at the activity level. Using this, for example, it is possible to display videos in a particular page using a different template/player to that used elsewhere. This would make it possible to make a page with 100 videos embedded, behave differently to a page with just a single video.

NB There seem to be conflicts (jquery?) that prevent some player types loading on the same screen at the same time. e.g. mediaelement and flowplayer won't load properly when the youtube lightbox is also present on the page. So when you use local filter settings, be cautions with labels and blocks since these create the possibility of different player types being on the screen at the same time.

The rest of this document gets a bit technical and I don't want to scare non-techies off. So from here its not strictly necessary to read on.  

Templates
=========================================
There are fifteen templates available to use. The first six are ready made, though they can be altered.They are: Video.js, Sublime Video, JW Player, Flowplayer, MediaElement.js, and Youtube lightbox.

SublimeVideo and JW Player require that you register with their site to get a personal javascript link. So you will need to do that first then enter it in the requires_js field of the template before you can use them.

Each template has several fields, but only the name/key field is required:
1) required javascript url : The url of the JS file the html5 player requires.
2) required css url : The url of the CSS file the html5 player requires.
3) requires jquery : True or False. If your theme already loads JQuery you can always leave this as false. The JQuery URL can be set on the VideoEasy general settings page.
4) template : The html that goes on the page. Often this is just a div , with a unique id. Sometimes it is html5 video tags.
5) load script : Any script which the player runs to load an individual player, usually with the unique id of a container div
6) defaults : Custom variables you may use in the template or load script, or default values for existing variables (ie width and height).
7) custom css: CSS that you need on the page, that can be edited here on the settings page.
8) upload css: It is possible to upload a CSS file for inclusion on the page. This is probably in the case that the file is not available online to be be simply linked to. 
9) upload js: It is possible to upload a JS file for inclusion on the page. This is probably in the case that the file is not available online to be be simply linked to. 

Presets
=====================================
Each template's page contains a drop down with a number of "preset" templates. (template templates ..I guess). The list of presets will grow hopefully as people submit them to me, or I dream them up. Using these you can quickly make new templates, or use as a starting point for your own template. The current presets are:
Video.js, Sublime Video, JW Player, Flowplayer, MediaElement.js,Youtube Lightbox, YouTube(standard),Multi Source Audio, Multi Source Video, JW Player RSS, and SoundManager 2 

In order to keep VideoEasy small, there are no actual JS players bundled. Flowplayer, Sublime Video etc are all included on the page via CDN hosting sources. In some cases, notably SoundManager 2, it will work better if you have those players installed on your own web server. SoundManager2 has flash components, which are sensitive to crossdomain hosting issues. 

The Video Easy Variables
=====================================

Variables are used to replace placeholders in the template and load scripts. A placeholder for a variable looks like this: @@VARIABLE@@ (variable name surrounded by @@ marks.)

These variables are generated by Video Easy after parsing the media link on the Moodle page. You can define your own in the defaults section if you wish. 
NB Video Easy supports the ?d=[width]x[height] notation that Moodle's multi media plugins filter uses, for all extensions, but not for Youtube links. But since almost nobody ever uses it, in most cases you will want to specify a width and height in the defaults section for the template. 

AUTOMIME = video file mime type determined by file extension.
FILENAME = filename of video
AUTOPNGFILENAME = the video filename but with a png extension
AUTOJPGFILENAME = the video filename but with a jpg extension
VIDEOURL = the url of the video
URLSTUB = the url of the video minus the file extension. 
AUTOPOSTERURLJPG = the full video url but with a jpg extension
AUTOPOSTERURLPNG = the full video url but with a png extension
DEFAULTPOSTERURL = url to a default poster image. VideoEasy ships with  bland grey image. But you can upload your own default poster image on the Video Easy general settings page.
TITLE = the video title (from linked text)
AUTOID = an auto generated id to use in the container div or elsewhere
CSSLINK = used internally to load a CSS file if needed.
PLAYER = the type of player (videojs, flowplayer ...etc)
WIDTH = the width of video
HEIGHT = the height of video

Note that while the template replacement is a simple swap out of the placeholder text, the loader script replacement is a little different. The loader script replacement will remove surrounding quotes as well as the placeholder, and put a JS variable in their place.
 eg
 template: <video id="@@AUTOID@@" 
 becomes: <video id="123456"
 
 loader script: player{ id: "@@AUTOID@@"
 becomes: player{ id: opts['AUTOID']
 
And a final caution, Video Easy generates a loader script from the template (if required) but this will be cached by Moodle in most cases. Thats a good thing too. But it means you will need to run Moodle "purge all caches" after making changes to anything on the Video Easy filter settings page.

The Future
===========
The next important step for VideoEasy is to offer up a form for each template with a field for each of the variables defined there. Given the correct permissions a user could then customize the player behaviour without the CSS and JS complexity. And the settings could be made available at the course and activity level, so that users can have, for example, different sized players on different pages. 

Enjoy

Justin Hunt
poodllsupport@gmail.com





