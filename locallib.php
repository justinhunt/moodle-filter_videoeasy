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

require_once($CFG->libdir . '/adminlib.php');

require_once($CFG->dirroot . '/filter/videoeasy/lib.php');

/**
 * This is a custom admin setting for a dropdown list of presets
 * that fills other input areas on the settings form
 * The fact that some templates are defined here, and others elsewhere(lib.php) 
 * is a bad legacy of the changes the plugin has gone through to date. 
 * So the way the defaults/presets are stored in code needs a refactor. 
 * But its just a housekeeping issue.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_videoeasypresets extends admin_setting {

	  /** @var mixed int index of template*/
    public $templateindex;
    /** @var array template data for spec index */
    public $presetdata;
    public $visiblename;
    public $information;

    /**
     * not a setting, just text
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $heading heading
     * @param string $information text in box
     */
    public function __construct($name, $visiblename, $information,$templateindex) {
        $this->nosave = true;
        $this->templateindex = $templateindex;
        $this->presetdata = $this->fetch_presets();
        $this->visiblename=$visiblename;
        $this->information=$information;
        parent::__construct($name, $visiblename, $information, '',$templateindex);
    }

    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Returns an HTML string
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
     
        //make our js
        $jscallback = 'filter_videoeasy_fillfields_' . $this->templateindex ;
		$js ="<script>";
		$js .="function $jscallback(presetindex){";
		$js .="if(!presetindex){return;}";
		$js .="var presets = " . json_encode($this->presetdata) .";";
		$js .="var key = document.getElementById('id_s_filter_videoeasy_templatekey_' + $this->templateindex);";
		$js .="var name = document.getElementById('id_s_filter_videoeasy_templatename_' + $this->templateindex);";
		$js .="var requirecss = document.getElementById('id_s_filter_videoeasy_templaterequire_css_' + $this->templateindex);";
		$js .="var requirejs = document.getElementById('id_s_filter_videoeasy_templaterequire_js_' + $this->templateindex);";
		$js .="var defaults = document.getElementById('id_s_filter_videoeasy_templatedefaults_' + $this->templateindex);";
		$js .="var jquery = document.getElementById('id_s_filter_videoeasy_templaterequire_jquery_' + $this->templateindex);";
		$js .="var amd = document.getElementById('id_s_filter_videoeasy_template_amd_' + $this->templateindex);";
		$js .="var body = document.getElementById('id_s_filter_videoeasy_templatepreset_' + $this->templateindex);";
		$js .="var script = document.getElementById('id_s_filter_videoeasy_templatescript_' + $this->templateindex);";
		$js .="var style = document.getElementById('id_s_filter_videoeasy_templatestyle_' + $this->templateindex);";


		$js .="key.value=presets[presetindex]['key'];";
		$js .="name.value=presets[presetindex]['name'];";
		$js .="requirecss.value=presets[presetindex]['requirecss'];";
		$js .="requirejs.value=presets[presetindex]['requirejs'];";
		$js .="defaults.value=presets[presetindex]['defaults'];";
		$js .="amd.value=presets[presetindex]['amd'];";
		$js .="jquery.value=presets[presetindex]['jquery'];";
		$js .="jquery.checked=presets[presetindex]['jquery'] ? true : false;";
		$js .="body.value=presets[presetindex]['body'];";
		$js .="script.value=presets[presetindex]['script'];";
		$js .="style.value=presets[presetindex]['style'];";
		$js .="}";
		$js .="</script>";
		
        //build our select form
        $keys = array_keys($this->presetdata);
        $usearray = array();
        
        foreach($keys as $key){
        	$usearray[$key]=$this->presetdata[$key]['name'];
        }
        $select = html_writer::select($usearray,'filter_videoeasy/presets','','--custom--', array('onchange'=>$jscallback . '(this.value)'));
	
		return format_admin_setting($this, $this->visiblename,
        '<div class="form-text defaultsnext">'. $js . $select . '</div>',
        $this->information, true, '','', $query);

    }
	
	protected function fetch_presets(){

	$ret = array();
	$defaultpresets = array(1,2,3,4,5,6);//filter_videoeasy_fetch_players();
	$additionalpresets = array(7,8,9,10,11,12);
	
	//prepare template info
	$templaterequires=filter_videoeasy_fetch_template_requires($defaultpresets);
	$templatebodys=filter_videoeasy_fetch_template_bodys($defaultpresets);
	$templatescripts=filter_videoeasy_fetch_template_scripts($defaultpresets);
	$templatestyles=filter_videoeasy_fetch_template_styles($defaultpresets);
	$templatedefaults=filter_videoeasy_fetch_template_defaults($defaultpresets);
	$templatekeys=filter_videoeasy_fetch_template_keys($defaultpresets);
	$templatenames=filter_videoeasy_fetch_template_names($defaultpresets);
	
	foreach($defaultpresets as $preset){
		$presets = array();
		$presets['key'] =$templatekeys[$preset];
		if(!$presets['key']){continue;}
		$presets['name'] =$templatenames[$preset];
		$presets['requirecss'] =$templaterequires[$preset]['css'];
		$presets['requirejs'] =  $templaterequires[$preset]['js'];
		$presets['amd'] = $templaterequires[$preset]['amd'];
		$presets['jquery'] = $templaterequires[$preset]['jquery'];
		$presets['defaults'] = $templatedefaults[$preset];
		$presets['body'] =$templatebodys[$preset];
		$presets['script'] = $templatescripts[$preset];
		$presets['style'] = $templatestyles[$preset];		
	  //update our return value
	    $ret[$preset] = $presets;
	}//end of for each
	foreach($additionalpresets as $preset){
		$presets = array();
		switch ($preset){
			case 7:
				$presets['key'] ='youtubestandard';
				$presets['name'] ='YouTube(standard)';
				$presets['requirecss'] ='';
				$presets['requirejs'] =  '';
				$presets['amd'] = 1;
				$presets['jquery'] = 0;
				$presets['defaults'] = 'WIDTH=600,HEIGHT=400';
				$presets['body'] ='<iframe width="@@WIDTH@@" height="@@HEIGHT@@" src="//www.youtube.com/embed/@@FILENAME@@" frameborder="0" allowfullscreen></iframe>';
				$presets['script'] = '';
				$presets['style'] = '';
				break;
		/*
			case 8:
				$presets['key'] ='YouTube(Mediaelement.js)';
				$presets['requirecss'] ='https://cdnjs.cloudflare.com/ajax/libs/mediaelement/2.13.2/css/mediaelementplayer.min.css';
				$presets['requirejs'] ='https://cdnjs.cloudflare.com/ajax/libs/mediaelement/2.13.2/js/mediaelement-and-player.min.js';
				$presets['jquery'] = 1;
				$presets['defaults'] = 'WIDTH=640,HEIGHT=480';
				$presets['body'] ='<video width="@@WIDTH@@" height="@@HEIGHT@@" id="@@AUTOID@@" preload="none">
    <source type="video/youtube" src="http://www.youtube.com/watch?v=@@FILENAME@@" />
</video>';
				$presets['script'] = '';
				$presets['style'] = '';
				break;
		*/
			case 8:
				$presets['key'] ='multisourcevideo';
				$presets['name'] ='Multi Source Video';
				$presets['requirecss'] ='';
				$presets['requirejs'] =  '';
				$presets['amd'] = 1;
				$presets['jquery'] = 0;
				$presets['defaults'] = 'WIDTH=640,HEIGHT=480';
				$presets['body'] ='<video width="@@WIDTH@@" height="@@HEIGHT@@" controls>
  <source src="@@VIDEOURL@@" type="video/mp4">
  <source src="@@URLSTUB@@.ogg" type="video/ogg">
Your browser does not support the video tag.
</video>';
				$presets['script'] = '';
				$presets['style'] = '';
				break;
			case 9:
				$presets['key'] ='multisourceaudio';
				$presets['name'] ='Multi Source Audio';
				$presets['requirecss'] ='';
				$presets['requirejs'] =  '';
				$presets['amd'] = 1;
				$presets['jquery'] = 0;
				$presets['defaults'] = '';
				$presets['body'] ='<audio controls>
  <source src="@@VIDEOURL@@" type="audio/mpeg">
  <source src="@@URLSTUB@@.ogg" type="audio/ogg">
Your browser does not support the audio element.
</audio>';
				$presets['script'] = '';
				$presets['style'] = '';
				break;
			case 10:
				$presets['key'] ='jwplayerrss';
				$presets['name'] ='JW Player RSS';
				$presets['requirecss'] ='';
				$presets['requirejs'] =  'http://jwpsrv.com/library/PERSONALCODE.js';
				$presets['amd'] = 0;
				$presets['jquery'] = 0;
				$presets['defaults'] = 'WIDTH=640,HEIGHT=360';
				$presets['body'] ='<div id="@@AUTOID@@"></div>';
				$presets['script'] = 'jwplayer("@@AUTOID@@").setup({
playlist: "@@videourl@@"",
width: "@@WIDTH@@",
height: "@@HEIGHT@@",
listbar: {
        position: "right",
        size: 240,
        layout: "basic"
      }
});';
				$presets['style'] = '';
				break;
			case 11:
				$presets['key'] ='soundmanager2';
				$presets['name'] ='SoundManager2';
				$presets['requirecss'] ='';
				$presets['requirejs'] =  '//cdn.jsdelivr.net/soundmanager2/2.97a.20130512/soundmanager2.js';
				$presets['jquery'] = 0;
				$presets['amd'] = 0;
				$presets['defaults'] = '';
				$presets['body'] ='<a onClick="soundManager.play(\'@@AUTOID@@\')" >@@FILENAME@@</a>';
				$presets['script'] = 'soundManager.setup({
  url: "//cdn.jsdelivr.net/soundmanager2/2.97a.20130512/soundmanager2_flash9.swf",
  flashVersion: 9, // optional: shiny features (default = 8)
  // preferFlash: true;
  preferFlash: false,
  onready: function() {
   var mySound = soundManager.createSound({
      id: @@AUTOID@@, // optional: provide your own unique id
      url: @@VIDEOURL@@,
       autoPlay: false
    });
  }
});';
				$presets['style'] = '';
				break;
			case 12:
			default:
				$presets['key'] ='';
				$presets['name'] ='None';
				$presets['requirecss'] ='';
				$presets['requirejs'] =  '';
				$presets['jquery'] = 0;
				$presets['amd'] = 1;
				$presets['defaults'] = '';
				$presets['body'] ='';
				$presets['script'] = '';
				$presets['style'] = '';
			
		}//end of switch		
	  //update our return value
	    $ret[$preset] = $presets;
	
	}//end of for each
	return $ret;
	
}
}//end of class