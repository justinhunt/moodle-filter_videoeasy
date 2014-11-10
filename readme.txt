VideoEasy Filter
===============
VideoEasy is a filter that will replace links to video files with players from various video player providers.
To give real world example, the Moodle flowplayer doesn't do all file types, or make html5 look pretty. 
The video providers like video.js and sublimevideo do this, and the videoeasy filter puts them into Moodle/

Usage
===============
Define video templates at 
Site Administration / plugins / filters / videoeasy

Several are already made for you. You can edit them if you wish. 
You can make up to ten templates though only one can be active at any time.

Installation
==============
If you are uploading videoeasy, first expand the zip file and upload the videoeasy folder into:
[PATH TO MOODLE]/filters.

Then visit your Moodle server's Site Administration -> Notifications page. Moodle will guide you through the installation.
On the final page of the installation you will be able to register templates. You can choose to skip that and do it later from the videoeasy settings page if you wish.

After installing you will need to enable the videoeasy filter. You can enable the videoeasy filter when you visit:
Site Administration / plugins / filters / manage filters

Templates and Variables and Configuration
==========================================
There are ten templates and the first five are taken by the most popular html5 players, Video.js, Sublime Video, JW Player, Flowplayer and MediaElement.js
SublimeVideo and JW Player require you register with their site to get a personal javascript link. So you will need to do that first then enter it in the template before you can use them.

Each template has :
1) javascript url : The url of the JS file the html5 player requires (probably always)
2) css url : The url of the CSS file the html5 player requires (if any)
3) requires jquery : True or False
4) template : The html that goes on the page. Often this is just a div , with a unique id. Sometimes it is html5 video tags.
5) load script : Any script which the player runs to load an individual player, usually with the unique id of a container div
6) defaults : Custom variables you may use in the template or load script, or default values for existing variables (ie width and height).

The ready made variables you can use in the template. Note that these are used to replace place holders in the template and loader scripts only.
The template replacement is a simple swap out of the placeholder text. The loader script replacement will remove surrounding quotes and the placeholder, and put a JS variable in their place.
 eg
 template: <video id="@@AUTOID@@" 
 becomes: <video id="123456"
 
 loader script: player{ id: "AUTOID"
 becomes: player{ id: opts['AUTOID']

The Variables (you can define your own in the defaults section also):
AUTOMIME = video file mime type determined by file extension.
FILENAME = filename of video
AUTOPNGFILENAME = the url to a file of the same name as the video, but with a png extension.
AUTOJPGFILENAME = the url to a file of the same name as the video, but with a jpg extension.
VIDEOURL = the url of the video
AUTOPOSTERURLJPG = the video filename but with a jpg extension
AUTOPOSTERURLPNG = the video filename but with a png extension
DEFAULTPOSTERURL = url to a default poster image of static
TITLE = the video title (from linked text)
AUTOID = an auto generated id to use in the container div or elsewhere
CSSLINK = used internally to poke the css link into the head after the header has already loaded.
PLAYER = the type of player
WIDTH = the width of video
HEIGHT = the height of video

Enjoy

Justin Hunt
poodllsupport@gmail.com





