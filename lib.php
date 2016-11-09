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
 * @package filter_videoeasy
 * @copyright  2014 Justin Hunt (http://poodll.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

define('FILTER_VIDEOEASY_TEMPLATE_COUNT', 15);


/**
 * Return an array of template ids. A legacy of when each had a hard coded name really. 
 * But we still need an unchanging id for each template, and this is it
 * @return array of template ids
 */
function filter_videoeasy_fetch_players(){

	$players = array();
	for ($i=1;$i<=FILTER_VIDEOEASY_TEMPLATE_COUNT;$i++){
		$players[]=$i;
	}
	return $players;
}

/**
 * Return an array of OLD player names. For mapping to template indexes, so early adopters 
 * don't get errors and lose templates etc
 * @return array of player names
 */
function filter_videoeasy_fetch_oldplayers(){
	$oldplayers = array('videojs','jplayervideo','jwplayer','flowplayer','mediaelement','playersix','playerseven','playereight','playernine','playerten','playereleven','playertwelve','playerthirteen','playerfourteen','playerfifteen');
	$players = array();
	for ($i=1;$i<=FILTER_VIDEOEASY_TEMPLATE_COUNT;$i++){
		$players[$i]=$oldplayers[$i-1];
	}
	return $players;
}

/**
 * Fetch the old value of the old style template name, and for use as a default
 * when upgrading. If no old value, just the new defaults
 * @return string default value
 */
function filter_videoeasy_fetch_default($conf, $oldpropname, $newdefault){
	if($conf && property_exists($conf,$oldpropname)){
			$defvalue=$conf->{$oldpropname} ;
	}else{
		$defvalue=$newdefault;
	}
	return $defvalue;
}

/**
 * Return an array of extensions we might handle
 * @return array of variable names parsed from template string
 */
function filter_videoeasy_fetch_extensions($adminconfig = false){
	if(!$adminconfig){
		$adminconfig = get_config('filter_videoeasy');
	}
	$default_extensions = filter_videoeasy_fetch_default_extensions();
	$have_custom_extensions = $adminconfig && isset($adminconfig->extensions) && !empty($adminconfig->extensions);
	return $have_custom_extensions ? explode(',',$adminconfig->extensions) : $default_extensions;
}	

/**
 * Return an array of extensions we might handle
 * @return array of variable names parsed from template string
 */
function filter_videoeasy_fetch_default_extensions(){
	return array('mp4','webm','ogg','ogv','flv','mp3','rss','youtube');
}	

/**
 * Return an array of properties, empty, to ensure we have all the variables when we need them
 * @return array of properties
 */
function filter_videoeasy_fetch_emptyproparray(){
	$proparray=array();
	$proparray['AUTOMIME'] = '';
	$proparray['FILENAME'] = '';
	$proparray['FILENAMESTUB'] = '';
	$proparray['FILETITLE'] = '';
	$proparray['AUTOPNGFILENAME'] = '';
	$proparray['AUTOJPGFILENAME'] = '';
	$proparray['VIDEOURL'] = '';
	$proparray['RAWVIDEOURL'] = '';
	$proparray['RAWPARAMS'] = '';
	$proparray['URLSTUB'] = '';
	$proparray['AUTOPOSTERURLJPG'] = '';
	$proparray['AUTOPOSTERURLPNG'] = '';
	$proparray['DEFAULTPOSTERURL'] = '';
	$proparray['TITLE'] = '';
	$proparray['AUTOID'] = '';
	$proparray['CSSLINK'] = '';
	$proparray['CSSUPLOAD'] = '';
	$proparray['TEMPLATEID'] = '';
	$proparray['WIDTH'] = '';
	$proparray['HEIGHT'] = '';
	$proparray['FILEEXT'] = '';
	$proparray['COURSEID'] = '';
	$proparray['COURSECONTEXTID'] = '';
	return $proparray;
}

/**
 * Takes a string of prop="value",prop="value" and returns a nice array of prop/values
 * @param string a defaults string. prop="value",prop="value"
 * @return array of prop/values
 */
function filter_videoeasy_parsepropstring($rawproperties){
	//Now we just have our properties string
	//Lets run our regular expression over them
	//string should be property=value,property=value
	//got this regexp from http://stackoverflow.com/questions/168171/regular-expression-for-parsing-name-value-pairs
	$regexpression='/([^=,]*)=("[^"]*"|[^,"]*)/';
	$matches; 	

	//here we match the filter string and split into name array (matches[1]) and value array (matches[2])
	//we then add those to a name value array.
	$itemprops = array();
	if(!$rawproperties ||$rawproperties==''){return $itemprops;}
	
	if (preg_match_all($regexpression, $rawproperties,$matches,PREG_PATTERN_ORDER)){		
		$propscount = count($matches[1]);
		for ($cnt =0; $cnt < $propscount; $cnt++){
			// echo $matches[1][$cnt] . "=" . $matches[2][$cnt] . " ";
			$newvalue = $matches[2][$cnt];
			//this could be done better, I am sure. WE are removing the quotes from start and end
			//this wil however remove multiple quotes id they exist at start and end. NG really
			$newvalue = trim($newvalue,'"');
			$itemprops[$matches[1][$cnt]]=$newvalue;
		}
	}
	return $itemprops;
}

/**
       * Serves files for this plugin
       *
       * 
       */
function filter_videoeasy_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
	$players = filter_videoeasy_fetch_players();
	foreach($players as $player){
    	if($context->contextlevel == CONTEXT_SYSTEM){
    		if($filearea === 'uploadjs_' . $player || $filearea === 'uploadcss_' . $player || $filearea === 'defaultposterimage' ) {
        		return filter_videoeasy_internal_file_serve($filearea,$args,$forcedownload, $options);
        	}
		} 
	}
	send_file_not_found();
}


/**
   * Returns URL to the videoeasyjs or videoeasycss php files, or the defaulposter image 
   *
  *
   * @param string $filepath
   * @param string $filearea
   * @return string protocol relative URL or null if not present
   */
function filter_videoeasy_internal_file_url($filepath, $filearea) {
          global $CFG;

 
         $component = 'filter_videoeasy';
         $itemid = 0;
         $syscontext = context_system::instance();
  
          $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/$syscontext->id/$component/$filearea/$itemid".$filepath);
         return $url;
     }

/**
   * Serves either the videoeasyjs or videoeasycss php files, or the defaulposter image 
   *
   * theme revision is used instead of the itemid.
  *
   * @param string $filearea
   * @param array $args the bits that come after the itemid in the url
   * @param boolean $forcedownload passed straight in from above
   * @param array $options passed straight in from above
   * @return string protocol relative URL or null if not present
   */
function filter_videoeasy_internal_file_serve($filearea, $args, $forcedownload, $options) {
         global $CFG;
         require_once("$CFG->libdir/filelib.php");
  
          $syscontext = context_system::instance();
         $component = 'filter_videoeasy';
  
          $revision = array_shift($args);
         if ($revision < 0) {
             $lifetime = 0;
         } else {
              $lifetime = 60*60*24*60;
         }
  
          $fs = get_file_storage();
         $relativepath = implode('/', $args);
  
         $fullpath = "/{$syscontext->id}/{$component}/{$filearea}/0/{$relativepath}";
          $fullpath = rtrim($fullpath, '/');
          if ($file = $fs->get_file_by_hash(sha1($fullpath))) {
              send_stored_file($file, $lifetime, 0, $forcedownload, $options);
              return true;
          } else {
             send_file_not_found();
          }
      }
