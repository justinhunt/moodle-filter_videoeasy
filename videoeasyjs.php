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
$templateid = optional_param('t','',PARAM_TEXT);

$conf = get_config('filter_videoeasy');
if(empty($templateid)){
	$templateid=$conf->{'useplayer' . $ext};
}

//are we AMD and Moodle 2.9 or more?
$require_amd = $conf->{'template_amd_' . $templateid} && $CFG->version>=2015051100;

//get presets
$thescript=$conf->{'templatescript_' . $templateid};
$defaults=$conf->{'templatedefaults_' . $templateid};
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

if($require_amd){

	//figure out if this is https or http. We don't want to scare the browser
	$scheme='http:';
	if(strpos(strtolower($CFG->wwwroot),'https')===0){$scheme='https:';}


	//this is for loading as dependencies the uploaded or linked files
	//massage the js URL depending on schemes and rel. links etc. Then insert it
		$requiredjs = $conf->{'templaterequire_js_' . $templateid};
		if($requiredjs){
			if(strpos($requiredjs,'//')===0){
				$requiredjs = $scheme . $requiredjs;
			}elseif(strpos($requiredjs,'/')===0){
				$requiredjs = $CFG->wwwroot . $requiredjs;
			}
			//remove .js from end
			//$requiredjs = substr($requiredjs, 0, -3);
		}
	
		//if we have an uploaded JS file, then lets include that
		$uploadjsfile = $conf->{'uploadjs_' . $templateid};
		if($uploadjsfile){
			$uploadjs = filter_videoeasy_setting_file_url($uploadjsfile,'uploadjs_' . $templateid);
		}

	//Create the dependency stuff in the output js
	$requires = array("'" . 'jquery' . "'");
	$params = array('$');

	if($requiredjs){
		$requires[] =  "'" . $requiredjs . "'";
		$params[] = "recjs" . $templateid;
	
	}elseif($uploadjsfile){
		$requires[] =  "'" . $uploadjs . "'";
		//$requires[] ="'uploadjs" . $templateid . "'";
		$params[] = "uploadjs" . $templateid;
	
	}

	$thefunction = "define('filter_videoeasy_d" . $templateid . "',[" . implode(',',$requires) . "], function(" . implode(',',$params) . "){ ";
	$thefunction .= "return function(opts){" . $thescript. "}; });";

//If not AMD
}else{

	$thefunction = "if(typeof filter_videoeasy_extfunctions == 'undefined'){filter_videoeasy_extfunctions={};}";
	$thefunction .= "filter_videoeasy_extfunctions['" . $ext . "']= function(opts) {" . $thescript. "};";

}
//$thefunction = "function filter_videoeasy_doscripts(){ " . $thescript . "}";
header('Content-Type: application/javascript');
echo $thefunction;
