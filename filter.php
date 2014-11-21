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
 * Filter for expanding videoeasy templates 
 *
 * @package    filter
 * @subpackage videoeasy
 * @copyright  2014 Justin Hunt <poodllsupport@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php');

class filter_videoeasy extends moodle_text_filter {

    /**
     * Apply the filter to the text
     *
     * @see filter_manager::apply_filter_chain()
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     */
    public function filter($text, array $options = array()) {
	
			if (!is_string($text)) {
				// non string data can not be filtered anyway
				return $text;
			}
			$newtext = $text;
		
			//No links .. bail
			$havelinks = !(stripos($text, '</a>') ===false);
			if(!$havelinks){return $text;}
			
			 //$conf = get_object_vars(get_config('filter_videoeasy'));
			 $conf = get_config('filter_videoeasy');			
			
			//check for mp4
			if ($conf->handlemp4) {
					$search = '/<a\s[^>]*href="([^"#\?]+\.mp4)(\?d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
					$newtext = preg_replace_callback($search, 'filter_videoeasy_mp4_callback', $newtext);
			}
			
			//check for webm
			if ($conf->handlewebm) {
					$search = '/<a\s[^>]*href="([^"#\?]+\.webm)(\?d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
					$newtext = preg_replace_callback($search, 'filter_videoeasy_webm_callback', $newtext);
			}
			
			//check for ogg
			if ($conf->handleogg) {
					$search = '/<a\s[^>]*href="([^"#\?]+\.ogg)(\?d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
					$newtext = preg_replace_callback($search, 'filter_videoeasy_ogg_callback', $newtext);
			}
			
			//check for mp3
			if ($conf->handlemp3) {
					$search = '/<a\s[^>]*href="([^"#\?]+\.mp3)(\?d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
					$newtext = preg_replace_callback($search, 'filter_videoeasy_mp3_callback', $newtext);
			}
		
		if (is_null($newtext) or $newtext === $text) {
			// error or not filtered
			return $text;
		}

		return $newtext;
    }
   
}//end of class


/**
 * Replace mp4 links with player
 *
 * @param  $link
 * @return string
 */
function filter_videoeasy_mp4_callback($link) {
	return filter_videoeasy_process($link,'mp4');
}

/**
 * Replace ogg links with player
 *
 * @param  $link
 * @return string
 */
function filter_videoeasy_ogg_callback($link) {
	return filter_videoeasy_process($link,'ogg');
}

/**
 * Replace webm links with player
 *
 * @param  $link
 * @return string
 */
function filter_videoeasy_webm_callback($link) {
	return filter_videoeasy_process($link,'webm');
}

/**
 * Replace mp3 links with player
 *
 * @param  $link
 * @return string
 */
function filter_videoeasy_mp3_callback($link) {
	return filter_videoeasy_process($link,'mp3');
}

/**
 * Replace mp4 or flv links with player
 *
 * @param  $link
 * @param  $ext
 * @return string
 */
function filter_videoeasy_process($link, $ext) {
global $CFG, $PAGE;
/*
* 1 = url, 2=?, 3=width,4=height,5=linkedtext
*
*/
//	print_r($link);

	//clean up url
	$url = $link[1];
    $url = str_replace('&amp;', '&', $url);
    $rawurl = $url;
	$url = clean_param($url, PARAM_URL);
	
	//get our template info
	//$conf = get_object_vars(get_config('filter_videoeasy'));
	 $conf = get_config('filter_videoeasy');
	$player=$conf->{'useplayer' . $ext};
	
	$require_js = $conf->{'templaterequire_js_' . $player};
	$require_css = $conf->{'templaterequire_css_' . $player};
	$require_jquery = $conf->{'templaterequire_jquery_' . $player};

    
    //get the bits of the url
	$bits = parse_url($rawurl);
	$scheme = $bits['scheme'] . ':';
	$filename = basename($bits['path']);
	$autopngfilename = str_replace('.' . $ext,'.png',$filename);
	$autojpgfilename = str_replace('.' . $ext,'.png',$filename);
	//print_r($bits);
	//echo $scheme . ":" . $filename . ":" . $autopngfilename ;
    
    /*
	//add scheme to url if there was none
	if(strpos($PAGE->url->out(),'https:')===0){
		$scheme='https:';
	}else{
		$scheme='http:';
	}
	*/
	//fill out require js and require css full urls
	if(strpos($require_js,'//')===0){
		$require_js = $scheme . $require_js;
	}elseif(strpos($require_js,'/')===0){
		$require_js = $CFG->wwwroot . $require_js;
	}
	
	if(strpos($require_css,'//')===0){
		$require_css = $scheme . $require_css;
	}elseif(strpos($require_css,'/')===0){
		$require_css = $CFG->wwwroot . $require_css;
	}
	
	//get presets
	$preset=$conf->{'templatepreset_' . $player};
	$defaults=$conf->{'templatedefaults_' . $player};
	//make sure we have all the keys and defaults in our proparray
	$proparray = filter_videoeasy_fetch_emptyproparray();
	$proparray = array_merge($proparray,filter_videoeasy_parsepropstring($defaults));
	
	
	//use default widths or explicit width/heights if they were passed in ie http://url.to.video.mp4?d=640x480
	if (!empty($link[3])) {
		$proparray['WIDTH'] = $link[3];
	}
	if (!empty($link[4])) {
		$proparray['HEIGHT'] = $link[3];
	}
	
	
	
	//get the url  - extenstion
	$urlstub = substr($rawurl,0,strpos($rawurl,'.' . $ext));
	//$url = $link[5];
	$videourl = $rawurl;
	$autoposterurljpg = $urlstub . '.jpg';
	$autoposterurlpng = $urlstub . '.png';
	$title = $link[5];

	
	//I liked this better, but jquery was odd about it.
	//$autoid = $urlstub . '_' . time() . (string)rand(100,32767) ;
	$autoid = time() . (string)rand(100,32767) ;
	
	//get default splash
	$defaultposterurl = $CFG->wwwroot . '/filter/videoeasy/static.jpg';

	//make up mime type
	switch ($ext){
		case 'mp3': $automime='audio/mpeg';break;
		case 'webm': $automime='video/webm';break;
		case 'ogg': $automime='video/ogg';break;	
		case 'mp4': 
		default:
			$automime='video/mp4';
	}
	
	$proparray['AUTOMIME'] = $automime;
	$proparray['FILENAME'] = $filename;
	$proparray['DEFAULTPOSTERURL'] = $defaultposterurl;
	$proparray['AUTOPNGFILENAME'] = $autopngfilename;
	$proparray['AUTOJPGFILENAME'] = $autojpgfilename;
	$proparray['VIDEOURL'] = $videourl;
	$proparray['AUTOPOSTERURLJPG'] = $autoposterurljpg;
	$proparray['AUTOPOSTERURLPNG'] = $autoposterurlpng;
	$proparray['TITLE'] = $title;
	$proparray['AUTOID'] = $autoid;
	
	//might need this if cant load into header, but need to.
	//$scripttag="<script src='@@REQUIREJS@@'></script>";
	
	/*
	foreach($proparray as $key=>$value){
		echo $key . ': ' . $value . '<br />';
	}
	*/
	//replace the specified names with spec values
	foreach($proparray as $name=>$value){
		$preset = str_replace('@@' . $name .'@@',$value,$preset);
	}
	//error_log($preset);


	//prepare additional params our JS will use
	$proparray['PLAYER'] = $player;
	$proparray['CSSLINK']=false;
	
	//load jquery
	if($require_jquery){
		$PAGE->requires->js(new moodle_url($scheme . '//code.jquery.com/jquery-1.11.0.min.js'));
	}

	//get js
	$PAGE->requires->js(new moodle_url($require_js));
	
	//load css in header if not too late
	//$PAGE->requires->is_head_done()
	if($require_css && !$PAGE->headerprinted){
		$PAGE->requires->css( new moodle_url($require_css));
	}else{
		$proparray['CSSLINK']=$require_css;
	}
	
	
	//initialise our JS and call it to load
	//We need this so that we can require the JSON , for json stringify
		$jsmodule = array(
			'name'     => 'filter_videoeasy',
			'fullpath' => '/filter/videoeasy/module.js',
			'requires' => array('json')
		);
		
		//require any scripts from the template
	$PAGE->requires->js('/filter/videoeasy/videoeasyjs.php?ext=' . $ext);
		
		
	//setup our JS call
	$PAGE->requires->js_init_call('M.filter_videoeasy.loadvideoeasy', array($proparray),false,$jsmodule);
	

	//return our expanded template
	return $preset;
}
