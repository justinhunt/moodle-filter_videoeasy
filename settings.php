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
 * @package    filter
 * @subpackage videoeasy
 * @copyright  2014 Justin Hunt <poodllsupport@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$settings = null;
defined('MOODLE_INTERNAL') || die;
if (is_siteadmin()) {
	require_once($CFG->dirroot . '/filter/videoeasy/lib.php');
	require_once($CFG->dirroot . '/filter/videoeasy/locallib.php');

	//add folder in property tree for settings pages
	 $ADMIN->add('filtersettings', new admin_category('filter_videoeasy_category', 'Video Easy'));
	 $conf = get_config('filter_videoeasy');
	 
	 //template settings Page Settings 
	 // we changed this to use the default settings id for the top page. This way in the settings link on the manage filters
	 //page, we will arrive here. Else the link will show there, but it will error out if clicked.
   //	$settings_page = new admin_settingpage('filter_videoeasy_templatepage_handlers',get_string('templatepageheading_handlers', 'filter_videoeasy'));
	$settings_page = new admin_settingpage('filtersettingvideoeasy',get_string('templatepageheading_handlers', 'filter_videoeasy'));
	
	//heading of template
	$settings_page->add(new admin_setting_heading('filter_videoeasy/extensionheading', 
			get_string('extensionheading', 'filter_videoeasy'), ''));
	
	//get the players we use and the extensions we handle
	$players = filter_videoeasy_fetch_players();
	$extensions = filter_videoeasy_fetch_extensions();
	
	//create player select list
	//this looks complicated, because we made a big shift in the way we key templates
	//we are trying to map old numeric keys to new user assigned ones here
	//also it will look v. confusing on initial install, so we do a bit of work here
	//to get the playername and key right
	$playeroptions=array();
	$playernames=array();
	foreach($players as $templateid){
		//player name
		$defplayername = 'Player: ';
		$playername=$defplayername;
		$playerkey = false;
		 if($conf && property_exists($conf,'templatename_' . $templateid)){
		 	$playername = $conf->{'templatename_' . $templateid};
			$playerkey = $conf->{'templatekey_' . $templateid};
			$playername = trim($playername);
			if(empty($playername)){$playername = $playerkey;}
		 }elseif($conf && property_exists($conf,'templatekey_' . $templateid)){
		 	$playername = $conf->{'templatekey_' . $templateid};
			$playerkey = $templateid;
		 }
		 if($playername == $defplayername){$playername .= $templateid;}
		 if($playerkey){
			$playeroptions[$playerkey] = $playername;
		 }
		$playernames[$templateid] = $playername;
	}
	
	//add extensions checkbox
	if(count($playeroptions) < 1){
		$playeroptions['']=get_string('none');
	}
	foreach($extensions as $ext){
		switch($ext){
			case 'youtube': $def_player='playersix';break;
			case 'rss': $def_player='jwplayer';break;
			default:
				$def_player = '1';
		}
		$settings_page->add(new admin_setting_configcheckbox('filter_videoeasy/handle' . $ext, get_string('handle', 'filter_videoeasy', strtoupper($ext)), '', 0));
		$settings_page->add(new admin_setting_configselect('filter_videoeasy/useplayer' . $ext, get_string('useplayer', 'filter_videoeasy', strtoupper($ext)),  get_string('useplayerdesc', 'filter_videoeasy'), $def_player, $playeroptions));
	}
	
	//add extensions csv
	$defaultexts = implode(',',filter_videoeasy_fetch_default_extensions()); 
	$settings_page->add(new admin_setting_configtext('filter_videoeasy/extensions', 
				get_string('extensions', 'filter_videoeasy'),
				get_string('extensions_desc', 'filter_videoeasy'), 
				 $defaultexts, PARAM_RAW,70));
				 
	//add upload area for default poster image
	$name = 'filter_videoeasy/defaultposterimage';
	$title = get_string('defaultposterimage', 'filter_videoeasy') ;
	$description = get_string('defaultposterimage_desc', 'filter_videoeasy');
	$settings_page->add(new admin_setting_configstoredfile($name, $title, $description, 'defaultposterimage'));
	
	//add page to category
	$ADMIN->add('filter_videoeasy_category', $settings_page);
	
	
	//add 10 templates
	foreach($players as $templateid){

		//player name
		$playername=$playernames[$templateid];
		
		 //template settings Page Settings 
		$settings_page = new admin_settingpage('filter_videoeasy_templatepage_' . $templateid,get_string('templatepageheading', 'filter_videoeasy',$playername));
		
		//heading of template
		$settings_page->add(new admin_setting_heading('filter_videoeasy/templateheading_' . $templateid, 
				get_string('templateheading', 'filter_videoeasy', $playername), ''));
				
		//presets
		//this is a custom control, that allows the user to select a preset from a list.
		$settings_page->add(new admin_setting_videoeasypresets('filter_videoeasy/templatepresets_' . $templateid, 
				get_string('presets', 'filter_videoeasy'), get_string('presets_desc', 'filter_videoeasy'),$templateid));
			
				
		//template key
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templatekey_' . $templateid , 
				get_string('templatekey', 'filter_videoeasy',$templateid),
				get_string('templatekey_desc', 'filter_videoeasy'), 
				$defvalue, PARAM_TEXT));
				
		//template name
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templatename_' . $templateid , 
				get_string('templatename', 'filter_videoeasy',$templateid),
				get_string('templatename_desc', 'filter_videoeasy'), 
				$defvalue, PARAM_RAW));
				
		//template amd
		$defvalue= 0;
		$yesno = array('0'=>get_string('no'),'1'=>get_string('yes'));
		$settings_page->add(new admin_setting_configselect('filter_videoeasy/template_amd_' . $templateid,
			get_string('templaterequire_amd', 'filter_videoeasy',$templateid),
			get_string('templaterequire_amd_desc', 'filter_videoeasy'),
			$defvalue,$yesno));
		

		//template JS heading
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templaterequire_js_' . $templateid , 
				$playername  . get_string('templaterequirejs', 'filter_videoeasy') ,
				get_string('templaterequirejs_desc', 'filter_videoeasy'), 
				 $defvalue), PARAM_RAW,50);		

		//template requiredjs_shim
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templaterequire_js_shim_' . $templateid , 
				get_string('templaterequirejsshim', 'filter_videoeasy',$templateid),
				get_string('templaterequirejsshim_desc', 'filter_videoeasy'), 
				$defvalue, PARAM_RAW));

				 
		//template css heading
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templaterequire_css_' . $templateid , 
				$playername  . get_string('templaterequirecss', 'filter_videoeasy'),
				get_string('templaterequirecss_desc', 'filter_videoeasy'), 
				 $defvalue), PARAM_RAW,50);


		//template body
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatepreset_' . $templateid,
					$playername  . get_string('template', 'filter_videoeasy'),
					get_string('template_desc', 'filter_videoeasy'),$defvalue));

		//template body script
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatescript_' . $templateid,
					$playername  . get_string('templatescript', 'filter_videoeasy'),
					get_string('templatescript_desc', 'filter_videoeasy'),$defvalue));


		//template defaults	
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatedefaults_' . $templateid,
					$playername  . get_string('templatedefaults', 'filter_videoeasy'),
					get_string('templatedefaults_desc', 'filter_videoeasy'),$defvalue));

		//additional JS (upload)
		//see here: for integrating this https://moodle.org/mod/forum/discuss.php?d=227249
		$name = 'filter_videoeasy/uploadjs_' . $templateid;
		$title = $playername . ' ' .  get_string('uploadjs', 'filter_videoeasy') ;
		$description = get_string('uploadjs_desc', 'filter_videoeasy');
		$settings_page->add(new admin_setting_configstoredfile($name, $title, $description, 'uploadjs_' . $templateid));
		
		//template uploadjs_shim
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/uploadjs_shim_' . $templateid , 
				get_string('templateuploadjsshim', 'filter_videoeasy',$templateid),
				get_string('templateuploadjsshim_desc', 'filter_videoeasy'), 
				$defvalue, PARAM_RAW));
		
		
		//template body css
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatestyle_' . $templateid,
					get_string('templatestyle', 'filter_videoeasy',$templateid),
					get_string('templatestyle_desc', 'filter_videoeasy'),
					$defvalue,PARAM_RAW));
		
		//additional CSS (upload)
		$name = 'filter_videoeasy/uploadcss_' . $templateid;
		$title =$playername . ' ' . get_string('uploadcss', 'filter_videoeasy');
		$description = get_string('uploadcss_desc', 'filter_videoeasy');
		$settings_page->add(new admin_setting_configstoredfile($name, $title, $description, 'uploadcss_' . $templateid));
		
		//alternative content
		$defvalue= '';
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatealternate_' . $templateid,
					get_string('templatealternate', 'filter_videoeasy',$templateid),
					get_string('templatealternate_desc', 'filter_videoeasy'),
					$defvalue,PARAM_RAW));
		

					
		//add page to category
		$ADMIN->add('filter_videoeasy_category', $settings_page);
	}
}