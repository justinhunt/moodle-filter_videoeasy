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
 * Prints a particular instance of timedpage
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    filter_videoeasy
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//define('AJAX_SCRIPT', true);
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');


$conf = get_config('filter_videoeasy');
$player=$conf->useplayer;

//get presets
$thescript=$conf->{'templatescript_' . $player};
//get blank props array
$propsarray=filter_videoeasy_fetch_emptyproparray();

//these props are in the opts array in the allopts[] array on the page
//since we are writing the JS we write the opts['name'] into the js, but 
//have to remove quotes from template eg "@@VAR@@" => opts['var'] //NB no quotes.
//thats worth knowing for the admin who writed the JS load code for the template.
foreach($propsarray as $propname=>$propvalue){
	//case: single quotes
	$thescript = str_replace("'@@" . $propname ."@@'",'opts["' . $propname . '"]',$thescript);
	//case: double quotes
	$thescript = str_replace('"@@' . $propname .'@@"',"opts['" . $propname . "']",$thescript);
	//case: no quotes
	$thescript = str_replace('@@' . $propname .'@@',"opts['" . $propname . "']",$thescript);
}

$theloop = "M.filter_videoeasy.gyui.Array.each(M.filter_videoeasy.allopts, function(opts) {" . $thescript. "});";
$thefunction = "function filter_videoeasy_doscripts(){ " . $theloop . "}";
//$thefunction = "function filter_videoeasy_doscripts(){ " . $thescript . "}";
header('Content-Type: application/javascript');
echo $thefunction;
