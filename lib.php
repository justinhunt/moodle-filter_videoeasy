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


function filter_videoeasy_fetch_emptyproparray(){
	$proparray=array();
	$proparray['AUTOMIME'] = '';
	$proparray['FILENAME'] = '';
	$proparray['AUTOPNGFILENAME'] = '';
	$proparray['AUTOJPGFILENAME'] = '';
	$proparray['VIDEOURL'] = '';
	$proparray['AUTOPOSTERURLJPG'] = '';
	$proparray['AUTOPOSTERURLPNG'] = '';
	$proparray['DEFAULTPOSTERURL'] = '';
	$proparray['TITLE'] = '';
	$proparray['AUTOID'] = '';
	$proparray['CSSLINK'] = '';
	$proparray['PLAYER'] = '';
	$proparray['WIDTH'] = '';
	$proparray['HEIGHT'] = '';
	return $proparray;
}

function filter_videoeasy_fetch_template_requires($players){
	$ret = array();
	
	foreach($players as $player){
		$requires = array();
		switch($player){
			case 'videojs':
			// '<link href="//vjs.zencdn.net/4.10/video-js.css" rel="stylesheet">';
				$requires['css'] ='//vjs.zencdn.net/4.10/video-js.css';
				$requires['js'] = '//vjs.zencdn.net/4.10/video.js';
				$requires['jquery'] = 0;
				break;
				
			case 'sublimevideo':
				$requires['css'] ='';
				$requires['js'] = '//cdn.sublimevideo.net/js/@@PERSONALJSCODE@@.js';
				$requires['jquery'] = 0;				
				break;
				
			case 'jwplayer':
				$requires['css'] ='';
				$requires['js'] = 'http://jwpsrv.com/library/@@PERSONALJSCODE@@.js';
				$requires['jquery'] = 0;
				break;
				
			case 'flowplayer':
				//<script src="//releases.flowplayer.org/5.5.0/flowplayer.min.js"></script>';
				//$requires .= '<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>';
				$requires['css'] ='//releases.flowplayer.org/5.5.0/skin/minimalist.css';
				$requires['js'] = '//releases.flowplayer.org/5.5.0/flowplayer.min.js';
				$requires['jquery'] = 1;
				
				break;
				
			case 'mediaelement':
				$requires['css'] ='/filter/videoeasy/players/mediaelementjs/mediaelementplayer.css';
				$requires['js'] ='/filter/videoeasy/players/mediaelementjs/mediaelement-and-player.min.js';
				$requires['jquery'] = 1;
				break;
				
			case 'playersix':
				$requires['css'] ='';
				$requires['js'] ='';
				$requires['jquery'] =0;
				break;
			case 'playerseven':
				$requires['css'] ='';
				$requires['js'] ='';
				$requires['jquery'] =0;
				break;
			case 'playereight':
				$requires['css'] ='';
				$requires['js'] ='';
				$requires['jquery'] =0;
				break;
			case 'playernine':
				$requires['css'] ='';
				$requires['js'] ='';
				$requires['jquery'] =0;
				break;
			case 'playerten':
				$requires['css'] ='';
				$requires['js'] ='';
				$requires['jquery'] =0;
				break;
			default:
				$requires['css'] ='';
				$requires['js'] ='';
				$requires['jquery'] =0;
		}
			//update our return value
			$ret[$player] = $requires;
	}
	return $ret;
}

function filter_videoeasy_fetch_template_presets($players){
	$ret = array();
	
	foreach($players as $player){
		$presets = false;
		switch($player){
			case 'videojs':
				$presets = '<video id="@@AUTOID@@" class="video-js vjs-default-skin" controls preload="auto" width="@@WIDTH@@" height="@@HEIGHT@@" poster="@@AUTOPOSTERURLPNG@@" data-setup=\'{"example_option":true}\'>';
 				$presets .='<source src="@@VIDEOURL@@" type="@@AUTOMIME@@" />';
				$presets .='</video>';
				break;
				
			case 'sublimevideo':
				$presets = '<video id="@@AUTOID@@" class="sublime" width="@@WIDTH@@" height="@@HEIGHT@@" poster="@@AUTOPOSTERURLJPG@@" data-uid="@@TITLE@@" title="@@TITLE@@" preload="none">';
  				$presets .= '<source src="@@VIDEOURL@@" type="@@AUTOMIME@@"/>';
				$presets .='</video>';	
				break;
				
			case 'jwplayer':
				$presets = '<div id="@@AUTOID@@"></div>';
				break;
				
			case 'flowplayer':
				$presets='<div id="@@AUTOID@@" class="flowplayer video-flowplayer"></div>';
				break;
				
			case 'mediaelement':
				$presets = '<video id="@@AUTOID@@" src="@@VIDEOURL@@" width="@@WIDTH@@" height="@@HEIGHT@@"></video>';
				break;
			case 'playersix':
				$presets='';
				break;
			case 'playerseven':
				$presets='';
				break;
				
			case 'playereight':
				$presets='';
				break;
				
			case 'playernine':
				$presets='';
				break;
				
			case 'playerten':
				$presets='';
				break;
		}
		if($presets!==false){
			$ret[$player] = $presets;
		}	
	}
	return $ret;
}

function filter_videoeasy_fetch_template_scripts($players){
	$ret = array();
	
	foreach($players as $player){
		$scripts = false;
		switch($player){
			case 'videojs':
				$scripts = '';
				break;
				
			case 'sublimevideo':
				$scripts = '';	
				break;
				
			case 'jwplayer':
				$scripts = 'jwplayer("@@AUTOID@@").setup({
file: "@@VIDEOURL@@",
image: "@@AUTOPOSTERURLPNG@@",
title: "@@TITLE@@",
width: "100%",
aspectratio: "4:3"
});';
				break;
				
			case 'flowplayer':
				$scripts='$(function () {$("#" + "@@AUTOID@@").flowplayer({
playlist: [
 [
  { mp4:     "@@VIDEOURL@@" }
 ]
],
ratio: 3/4,  
splash: true  
}); 
});';
				break;
				
			case 'mediaelement':
				$scripts = '$("#" + "@@AUTOID@@").mediaelementplayer(/* Options */);';
				
				break;
			case 'playersix':
				$scripts='';
				break;
			case 'playerseven':
				$scripts='';
				break;
				
			case 'playereight':
				$scripts='';
				break;
				
			case 'playernine':
				$scripts='';
				break;
				
			case 'playerten':
				$scripts='';
				break;
		}
		if($scripts!==false){
			$ret[$player] = $scripts;
		}	
	}
	return $ret;
}



function filter_videoeasy_fetch_template_defaults($players){
	$ret = array();
	
	foreach($players as $player){
		$defaults = false;
		switch($player){
			case 'videojs':
				$defaults='WIDTH=640,HEIGHT=480';
				break;
				
			case 'sublimevideo':	
				$defaults='WIDTH=640,HEIGHT=480';
				break;
				
			case 'jwplayer':
				$defaults='';
				break;
				
			case 'flowplayer':
				$defaults='';
				break;
				
			case 'mediaelement':
				$defaults='WIDTH=640,HEIGHT=480';
				break;
				
			case 'playersix':
				$defaults='';
				break;
				
			case 'playerseven':
				$defaults='';
				break;
				
			case 'playereight':
				$defaults='';
				break;
				
			case 'playernine':
				$defaults='';
				break;
				
			case 'playerten':
				$defaults='';
				break;
		}
		if($defaults!==false){
			$ret[$player] = $defaults;
		}	
	}
	return $ret;
}

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
