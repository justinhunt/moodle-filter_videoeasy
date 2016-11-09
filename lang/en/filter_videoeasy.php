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
$string['templaterequirejs_desc'] = 'This is the URL of any required javascript library. Try to start with // .';
$string['templaterequirecss'] = ' CSS URL';
$string['templaterequirecss_desc'] = 'This is the URL of any required CSS file. Try to start with // .';
$string['templatename'] = 'Template Name';
$string['templatename_desc'] = 'The name of this template.';
$string['templatekey'] = 'Template Key';
$string['templatekey_desc'] = 'The id that identifies this template. Must be unique on the site. Users never see this. If you change it you will need to reallocate the template/player to the appropriate file extension. Try not to change it. Use alphanumeric characters only (no spaces)';
$string['templatedefaults'] = ' defaults';
$string['templatedefaults_desc'] = 'Default values for custom variables in the template/script. Usually just width, and height. Enter comma delimited sets of name=value pairs. eg width=800,height=900,feeling=joy';
$string['useplayer'] = '{$a} Player';
$string['useplayerdesc'] = "The player selected will use the information from the appropriate template.";
$string['handle'] = 'Handle {$a}';
$string['sitedefault'] = "Site Default";
$string['player'] = 'Player {$a}';
$string['player_videojs'] = "Video.js";
$string['player_sublimevideo'] = "Sublime Video";
$string['player_jplayervideo'] = "JPlayer (Video)";
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
$string['templaterequire_amd'] = 'Load via AMD';
$string['templaterequire_amd_desc'] = 'AMD is a javascript loading mechanism. If you upload or link to javascript libraries in your template, you might have to uncheck this. It only applies if on Moodle 2.9 or greater';
$string['uploadcss'] = 'Upload CSS';
$string['uploadcss_desc'] = 'You can upload one CSS file which will be loaded for your template. Only one.';
$string['defaultposterimage']='Default poster image';
$string['defaultposterimage_desc']='The standard default poster image is just a grey box. Here you can upload a custom image that better suits your site. A default poster image will only be shown if the player template specifies it.';
$string['templatestyle'] = 'Custom CSS (template {$a})';
$string['templatestyle_desc'] = 'Enter any custom CSS that your template uses here. Template variables will not work here. Just plain old css.';
$string['extensions'] = 'File Extensions';
$string['extensions_desc'] = 'A CSV (comma separated value) list of file extensions this filter can parse.';

$string['presets'] = 'Autofill template with a Preset';
$string['presets_desc'] = 'VideoEasy comes with some default presets you can use out of the box, or to help you get started with your own template. Choose one of those here, or just create your own template from scratch. You can export a template as a bundle by clicking on the green box above. You can import a bundle by dragging it onto the green box.';

$string['bundle'] = 'Bundle';

$string['templateuploadjsshim'] = 'Upload Shim export';
$string['templateuploadjsshim_desc'] = ' Leave blank unless you know what shimming is';
$string['templaterequirejsshim'] = 'Require Shim export';
$string['templaterequirejsshim_desc'] = ' Leave blank unless you know what shimming is';

$string['templatealternate'] = 'Alternate content'; 
$string['templatealternate_desc'] = 'Content that can be used when the custom and uploaded CSS and javascript content is not available. Currently this is used when the template is processed by a webservice, probably for content on the mobile app';
