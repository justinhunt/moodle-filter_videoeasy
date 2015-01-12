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
 * Returns the JS for a specified player
 * Its php but looks to browser like js file, cos that is what it returns.
 *
 * @package    filter_videoeasy
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//define('AJAX_SCRIPT', true);
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$ext = required_param('ext',PARAM_TEXT);
$player = optional_param('t','',PARAM_TEXT);

$conf = get_config('filter_videoeasy');
if(empty($player)){
	$player=$conf->{'useplayer' . $ext};
}

//get presets
$thescript=$conf->{'templatescript_' . $player};
$defaults=$conf->{'templatedefaults_' . $player};
//merge defaults with blank proparray  to get all fields
$defaultsarray = filter_videoeasy_parsepropstring($defaults);
$proparray=array_merge(filter_videoeasy_fetch_emptyproparray(), $defaultsarray);


//these props are in the opts array in the allopts[] array on the page
//since we are writing the JS we write the opts['name'] into the js, but 
//have to remove quotes from template eg "@@VAR@@" => opts['var'] //NB no quotes.
//thats worth knowing for the admin who writed the JS load code for the template.
foreach($proparray as $propname=>$propvalue){
	//case: single quotes
	$thescript = str_replace("'@@" . $propname ."@@'",'opts["' . $propname . '"]',$thescript);
	//case: double quotes
	$thescript = str_replace('"@@' . $propname .'@@"',"opts['" . $propname . "']",$thescript);
	//case: no quotes
	$thescript = str_replace('@@' . $propname .'@@',"opts['" . $propname . "']",$thescript);
}
/*
$theloop = "M.filter_videoeasy.gyui.Array.each(M.filter_videoeasy.allopts['". $ext . "'], function(opts) {" . $thescript. "});";
$thefunction = "function filter_videoeasy_doscripts(){ " . $theloop . "}";
*/
$thefunction = "if(typeof filter_videoeasy_extfunctions == 'undefined'){filter_videoeasy_extfunctions={};}";
$thefunction .= "filter_videoeasy_extfunctions['" . $ext . "']= function(opts) {" . $thescript. "};";
//$thefunction = "function filter_videoeasy_doscripts(){ " . $thescript . "}";
header('Content-Type: application/javascript');
echo $thefunction;
