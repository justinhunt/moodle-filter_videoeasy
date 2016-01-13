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
	//$players = array('videojs','jplayervideo','jwplayer','flowplayer','mediaelement','playersix','playerseven','playereight','playernine','playerten','playereleven','playertwelve','playerthirteen','playerfourteen','playerfifteen');
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
 * Return an array of arrays. Each containing info about the required CSS, JS and JQuery
 * @param array a list of players to fetch the requires for. 
 * @return array of array of CSS/JS/JQuery requires values.
 */
function filter_videoeasy_fetch_template_requires($players){
	$ret = array();
	
	foreach($players as $player){
		$requires = array();
		switch($player){
			case '1':
			case 'videojs':
			// '<link href="//vjs.zencdn.net/4.10/video-js.css" rel="stylesheet">';
				$requires['css'] ='//vjs.zencdn.net/4.10/video-js.css';
				$requires['js'] = '//vjs.zencdn.net/4.10/video.js';
				$requires['amd'] = 1;
				$requires['jquery'] = 0;
				break;
			
			case '2':	
			case 'jplayervideo':
				$requires['css'] ='//cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/skin/pink.flag/css/jplayer.pink.flag.min.css';
				$requires['js'] = '//cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js';
				$requires['amd'] = 1;
				$requires['jquery'] = 0;				
				break;
			
			case '3':	
			case 'jwplayer':
				$requires['css'] ='';
				$requires['js'] = 'http://jwpsrv.com/library/PERSONALJSCODE.js';
				$requires['amd'] = 0;
				$requires['jquery'] = 0;
				break;
			
			case '4':	
			case 'flowplayer':
				//<script src="//releases.flowplayer.org/5.5.0/flowplayer.min.js"></script>';
				//$requires .= '<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>';
				$requires['css'] ='//releases.flowplayer.org/6.0.4/skin/functional.css';
				$requires['js'] = '//releases.flowplayer.org/6.0.4/flowplayer.min.js';
				$requires['jquery'] = 0;
				$requires['amd'] = 1;
				break;
			
			case '5':	
			case 'mediaelement':
				$requires['css'] ='https://cdnjs.cloudflare.com/ajax/libs/mediaelement/2.13.2/css/mediaelementplayer.min.css';
				$requires['js'] ='https://cdnjs.cloudflare.com/ajax/libs/mediaelement/2.13.2/js/mediaelement-and-player.min.js';
				$requires['jquery'] = 0;
				$requires['amd'] = 0;
				break;
			
			case '6':	
			case 'playersix':
				$requires['css'] ='//cdn.rawgit.com/noelboss/featherlight/1.0.3/release/featherlight.min.css';
				$requires['js'] ='//cdn.rawgit.com/noelboss/featherlight/1.0.3/release/featherlight.min.js';
				$requires['jquery'] =0;
				$requires['amd'] = 0;
				break;

			default:
				$requires['css'] ='';
				$requires['js'] ='';
				$requires['jquery'] =0;
				$requires['amd'] = 0;
		}
			//update our return value
			$ret[$player] = $requires;
	}
	return $ret;
}

/**
 * Return an array of the body text of the template for each template index(player)
 * @param array a list of players/templates to fetch the data for. 
 * @return array of array of body text for each template/player
 */
function filter_videoeasy_fetch_template_bodys($players){
	$ret = array();
	
	foreach($players as $player){
		$presets = false;
		switch($player){
			case '1':
			case 'videojs':
				$presets = '<video id="@@AUTOID@@" class="video-js vjs-default-skin" controls preload="auto" width="@@WIDTH@@" height="@@HEIGHT@@"   data-setup=\'{"example_option":true}\'>';
 				$presets .='<source src="@@VIDEOURL@@" type="@@AUTOMIME@@" />';
				$presets .='</video>';
				break;
			
			case '2':
			case 'jplayervideo':
				$presets = '<div id="@@AUTOID@@_container" class="jp-video " role="application" aria-label="media player">
  <div class="jp-type-single">
    <div id="@@AUTOID@@" class="jp-jplayer"></div>
    <div class="jp-gui">
      <div class="jp-video-play">
        <button class="jp-video-play-icon" role="button" tabindex="0">play</button>
      </div>
      <div class="jp-interface">
        <div class="jp-progress">
          <div class="jp-seek-bar">
            <div class="jp-play-bar"></div>
          </div>
        </div>
        <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
        <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
        <div class="jp-details">
          <div class="jp-title" aria-label="title">&nbsp;</div>
        </div>
        <div class="jp-controls-holder">
          <div class="jp-volume-controls">
            <button class="jp-mute" role="button" tabindex="0">mute</button>
            <button class="jp-volume-max" role="button" tabindex="0">max volume</button>
            <div class="jp-volume-bar">
              <div class="jp-volume-bar-value"></div>
            </div>
          </div>
          <div class="jp-controls">
            <button class="jp-play" role="button" tabindex="0">play</button>
            <button class="jp-stop" role="button" tabindex="0">stop</button>
          </div>
          <div class="jp-toggles">
            <button class="jp-repeat" role="button" tabindex="0">repeat</button>
            <button class="jp-full-screen" role="button" tabindex="0">full screen</button>
          </div>
        </div>
      </div>
    </div>
    <div class="jp-no-solution">
      <span>Update Required</span>
      To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
    </div>
  </div>
</div>';	
				break;
			
			case '3':	
			case 'jwplayer':
				$presets = '<div id="@@AUTOID@@"></div>';
				break;
			
			case '4':	
			case 'flowplayer':
				$presets='<div><div id="@@AUTOID@@" style="max-width: @@WIDTH@@px; background-color:#777;';
				$presets .=' background-image:url(@@DEFAULTPOSTERURL@@);"class="flowplayer videoeasy-flowplayer"></div></div>';
				break;
			
			case '5':	
			case 'mediaelement':
				$presets = '<video id="@@AUTOID@@" src="@@VIDEOURL@@" width="@@WIDTH@@" height="@@HEIGHT@@"></video>';
				break;
			
			case '6':
			case 'playersix':
				$presets='<a href="#" data-featherlight="#@@AUTOID@@">';
				$presets.='<div class="filter_videoeasy_ytl">';
				$presets.='<img src="@@AUTOPOSTERURLJPG@@" width="@@WIDTH@@" height="@@HEIGHT@@"/></div></a>';				
				$presets.='<div style="display: none;"><div  id="@@AUTOID@@">';
				$presets.='<iframe width="@@videowidth@@" height="@@videoheight@@" src="//www.youtube.com/embed/@@FILENAME@@?rel=0" frameborder="0" allowfullscreen>';
				$presets.='</iframe></div></div>';
				break;
	
			default:
				$presets='';
		}
		if($presets!==false){
			$ret[$player] = $presets;
		}	
	}
	return $ret;
}

/**
 * Return an array of the custom css styles for each template index(player)
 * @param array a list of players/templates to fetch the data for. 
 * @return array of array of inline style for each template/player
 */
function filter_videoeasy_fetch_template_styles($players){
	$ret = array();
	
	foreach($players as $player){
		$styles = false;
		switch($player){
			case '1':
			case 'videojs':
				$styles = '';
				break;
			
			case '2':	
			case 'jplayervideo':
				$styles = '';	
				break;
				
			case '6':
			case 'playersix':
				$styles = '.filter_videoeasy_ytl img{display: block;}
					.filter_videoeasy_ytl { 
					position: relative; 
					display: inline-block;
					}
					.filter_videoeasy_ytl:after {
					content: ">";
					  font-size: 20px;
					  line-height: 30px;
					  color: #FFFFFF;
					  text-align: center;
					  position: absolute;
					  top: 40%;
					  left: 40%;
					  width: 20%;
					  height: 32px;
					  z-index: 2;
					  background: #FF0000;
					  border-radius: 8px;
					  pointer-events: none;
					}';
					break;
				
			default: $styles='';
		}
		if($styles!==false){
			$ret[$player] = $styles;
		}	
	}
	return $ret;
}
		
/**
 * Return an array of the custom js scripts for each template index(player)
 * @param array a list of players/templates to fetch the data for. 
 * @return array of array of inline scripts for each template/player
 */
function filter_videoeasy_fetch_template_scripts($players){
	$ret = array();
	
	foreach($players as $player){
		$scripts = false;
		switch($player){
			case '1':
			case 'videojs':
				$scripts = '';
				break;
			
			case '2':	
			case 'jplayervideo':
				$scripts = '$("#" + @@AUTOID@@).jPlayer(
   {
        ready: function () 
         {
          $(this).jPlayer("setMedia", 
             {
               title: @@TITLE@@,
               m4v: @@VIDEOURL@@,
               poster: @@DEFAULTPOSTERURL@@
              }
           );
        },
        cssSelectorAncestor: "#" + @@AUTOID@@ + "_container",
        swfPath: "https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.swf",
        supplied: "m4v",
        useStateClassSkin: true,
        autoBlur: false,
        smoothPlayBar: true,
        keyEnabled: true,
        remainingDuration: true,
        toggleDuration: true
 });';	
				break;
			
			case '3':	
			case 'jwplayer':
				$scripts = 'jwplayer("@@AUTOID@@").setup({
file: "@@VIDEOURL@@",
image: "@@DEFAULTPOSTERURL@@",
title: "@@TITLE@@",
width: "400",
aspectratio: "4:3"
});';
				break;
			
			case '4':	
			case 'flowplayer':
				$scripts='var container = $("#" + @@AUTOID@@);
if(container.length<1){return;}
requiredjs_flowplayer(container[0],{
playlist: [
 [
  { mp4:     "@@VIDEOURL@@" }
 ]
],
ratio: 3/4,
splash: true
}); ';
				break;
			
			case '5':	
			case 'mediaelement':
				$scripts = '$("#" + "@@AUTOID@@").mediaelementplayer(/* Options */);';				
				break;
								
			default:
				$scripts='';
		}
		if($scripts!==false){
			$ret[$player] = $scripts;
		}	
	}
	return $ret;
}

/**
 * Return an array of template keys for each template index(ie player)
 * 
 * @param array a list of players/templates to fetch the data for. 
 * @return array of array of keys for each template/player
 */
/**
 * Return an array of template keys(visible names) for each template index(player)
 * 
 * @param array a list of players/templates to fetch the data for. 
 * @return array of array of keys for each template/player
 */
function filter_videoeasy_fetch_template_keys($players){
	$ret = array();
	
	foreach($players as $player){
		$key = false;
		switch($player){
			case 1:
				$key='videojs';
				break;
			
			case 2:		
				$key='jplayervideo';
				break;
			
			case 3:	
				$key='jwplayer';
				break;
			
			case 4:	
				$key='flowplayer';
				break;
			
			case 5:	
				$key='mediaelementjs';
				break;
			
			case 6:	
				$key='youtubelightbox';
				break;

			default:
				$key='';
		}
		if($key!==false){
			$ret[$player] = $key;
		}	
	}
	return $ret;
}


/**
 * Return an array of templateie  names for each template index(player)
 * 
 * @param array a list of players/templates to fetch the data for. 
 * @return array of array of keys for each template/player
 */
function filter_videoeasy_fetch_template_names($players){
	$ret = array();
	
	foreach($players as $player){
		$key = false;
		switch($player){
			case '1':
			case 'videojs':
				$key='Video JS';
				break;
			
			case '2':	
			case 'jplayervideo':	
				$key='JPlayer (Video)';
				break;
			
			case '3':	
			case 'jwplayer':
				$key='JW Player';
				break;
			
			case '4':	
			case 'flowplayer':
				$key='Flowplayer';
				break;
			
			case '5':	
			case 'mediaelement':
				$key='Mediaelement.js';
				break;
			
			case '6':	
			case 'playersix':
				$key='YouTube(Lightbox)';
				break;

			default:
				$key='';
		}
		if($key!==false){
			$ret[$player] = $key;
		}	
	}
	return $ret;
}

/**
 * Return an array of template variable default values for each template index(player)
 * In the format of prop=value,prop=value
 * @param array a list of players/templates to fetch the data for. 
 * @return array of array of defaults for each template/player
 */
function filter_videoeasy_fetch_template_defaults($players){
	$ret = array();
	
	foreach($players as $player){
		$defaults = false;
		switch($player){
			case "1":
			case 'videojs':
				$defaults='WIDTH=640,HEIGHT=480';
				break;
				
			case "2":
			case 'jplayervideo':	
				$defaults='';
				break;
			
			case "3":	
			case 'jwplayer':
				$defaults='';
				break;
			
			case "4":	
			case 'flowplayer':
				$defaults='WIDTH=640,HEIGHT=480';
				break;
			
			case "5":	
			case 'mediaelement':
				$defaults='WIDTH=640,HEIGHT=480';
				break;
			
			case "6":	
			case 'playersix':
				$defaults='WIDTH=240,HEIGHT=160,videowidth=600,videoheight=400';
				break;

			default:
				$defaults='';
		}
		if($defaults!==false){
			$ret[$player] = $defaults;
		}	
	}
	return $ret;
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
