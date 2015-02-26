<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for filter_videoeasy
 *
 * @package    filter
 * @subpackage videoeasy
 * @copyright  2014 Justin Hunt <poodllsupport@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['filtername'] = 'Video Easy';
$string['pluginname'] = 'Video Easy';
$string['filterdescription'] = 'Convert video links strings into players';
$string['extensionheading'] = 'File Extension Handlers';
$string['templateheading'] = 'Settings for {$a}';
$string['template'] = ' template';
$string['template_desc'] = 'Put the template here, define variables by surrounding them with @@ marks at either e. eg @@variable@@';
$string['templatescript'] = ' load script';
$string['templatescript_desc'] = 'Put the load script (if any) here, define variables by surrounding them with @@ marks at either end. eg @@variable@@';
$string['templaterequirejs'] = ' javascript URL';
$string['templaterequirejs_desc'] = 'Almost all the players require script tags in the page header. Just put the URL here.';
$string['templaterequirecss'] = ' CSS URL';
$string['templaterequirecss_desc'] = 'Some players require CSS tags in the page header. Just put the URL here.';
$string['templatename'] = 'Template Name';
$string['templatename_desc'] = 'The name of this template.';
$string['templatekey'] = 'Template Key';
$string['templatekey_desc'] = 'The id that identifies this template. Must be unique on the site. Users never see this. If you change it you will need to reallocate the template/player to the appropriate file extension. Try not to change it.';
$string['templaterequirejquery'] = ' needs JQuery';
$string['templaterequirejquery_desc'] = 'Some players require JQuery. Check here if the player does';
$string['templatedefaults'] = ' defaults';
$string['templatedefaults_desc'] = 'Default values for custom variables in the template/script. Usually just width, and height. Enter comma delimited sets of name=value pairs. eg width=800,height=900,feeling=joy';
$string['useplayer'] = '{$a} Player';
$string['useplayerdesc'] = "The player selected will use the information from the appropriate template.";
$string['handle'] = 'Handle {$a}';
$string['sitedefault'] = "Site Default";
$string['player'] = 'Player {$a}';
$string['player_videojs'] = "Video.js";
$string['player_sublimevideo'] = "Sublime Video";
$string['player_jwplayer'] = "JW Player";
$string['player_flowplayer'] = "Flowplayer";
$string['player_mediaelement'] = "MediaElement.js";
$string['player_playersix'] = "YouTube Lightbox";
$string['player_playerseven'] = "Player 7";
$string['player_playereight'] = "Player 8";
$string['player_playernine'] = "Player 9";
$string['player_playerten'] = "Player 10";
$string['player_playereleven'] = "Player 11";
$string['player_playertwelve'] = "Player 12";
$string['player_playerthirteen'] = "Player 13";
$string['player_playerfourteen'] = "Player 14";
$string['player_playerfifteen'] = "Player 15";
$string['templatepageheading'] = 'Template: {$a}';
$string['templatepageheading_handlers'] = 'General Settings';
$string['uploadjs'] = 'Upload JS';
$string['uploadjs_desc'] = 'You can upload one js library file which will be loaded for your template. Only one.';
$string['jqueryurl'] = 'JQuery URL';
$string['jqueryurl_desc'] = 'The URL VideoEasy will be used when adding jquery to the host page. Defaults to the CDN hosted version. If your site does not have internet access you will probably need to point this to a location on your Moodle server. Do not start with http: or https:. Begin the url with // ';
$string['uploadcss'] = 'Upload CSS';
$string['uploadcss_desc'] = 'You can upload one CSS file which will be loaded for your template. Only one.';
$string['defaultposterimage']='Default poster image';
$string['defaultposterimage_desc']='The standard default poster image is just a grey box. Here you can upload a custom image that better suits your site. A default poster image will only be shown if the player template specifies it.';
$string['presets'] = 'Autofill template with a Preset';
$string['presets_desc'] = 'VideoEasy comes with some default presets you can use out of the box, or to help you get started with your own template. Choose one of those here, or just create your own template from scratch.';
$string['templatestyle'] = 'Custom CSS (template {$a})';
$string['templatestyle_desc'] = 'Enter any custom CSS that your template uses here. Template variables will not work here. Just plain old css.';