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
$string['templateheading'] = 'Settings for ';
$string['template'] = ' template';
$string['template_desc'] = 'Put the template here, define variables by surrounding them with @@ marks at either e. eg @@variable@@';
$string['templatescript'] = ' load script';
$string['templatescript_desc'] = 'Put the load script (if any) here, define variables by surrounding them with @@ marks at either e. eg @@variable@@';
$string['templaterequirejs'] = ' javascript URL';
$string['templaterequirejs_desc'] = 'Almost all the players require script tags in the page header. Just put the URL here.';
$string['templaterequirecss'] = ' CSS URL';
$string['templaterequirecss_desc'] = 'Some players require CSS tags in the page header. Just put the URL here.';
$string['templaterequirejquery'] = ' needs JQuery';
$string['templaterequirejquery_desc'] = 'Some players require JQuery. Check here if the player does';
$string['templatedefaults'] = ' defaults';
$string['templatedefaults_desc'] = 'Defaults are usually just width, and height. But you could also define custom variables here. Define the variables in comma delimited sets of name=value pairs. eg width=800,height=900,feeling=joy';
$string['useplayer'] = "The Video Player to Use";
$string['useplayerdesc'] = "The player selected will use the information from the appropriate template below.";
$string['handle'] = "Handle";
$players = array('videojs','sublimevideo','jwplayer','flowplayer','mediaelement','playersix','playerseven','playereight','playernine','playerten');
$string['player_videojs'] = "Video.js";
$string['player_sublimevideo'] = "Sublime Video";
$string['player_jwplayer'] = "JW Player";
$string['player_flowplayer'] = "Flowplayer";
$string['player_mediaelement'] = "MediaElement.js";
$string['player_playersix'] = "Player 6";
$string['player_playerseven'] = "Player 7";
$string['player_playereight'] = "Player 8";
$string['player_playernine'] = "Player 9";
$string['player_playerten'] = "Player 10";
