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
			if(empty(trim($playername))){$playername = $playerkey;}
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
	
	//add jquery path 
	$settings_page->add(new admin_setting_configtext('filter_videoeasy/jqueryurl', 
				get_string('jqueryurl', 'filter_videoeasy'),
				get_string('jqueryurl_desc', 'filter_videoeasy'), 
				 '//code.jquery.com/jquery-1.11.2.min.js', PARAM_RAW,50));
				 
	//add upload area for default poster image
	$name = 'filter_videoeasy/defaultposterimage';
	$title = get_string('defaultposterimage', 'filter_videoeasy') ;
	$description = get_string('defaultposterimage_desc', 'filter_videoeasy');
	$settings_page->add(new admin_setting_configstoredfile($name, $title, $description, 'defaultposterimage'));
	
	//add page to category
	$ADMIN->add('filter_videoeasy_category', $settings_page);
	
	//prepare template info
	//this is mainly just for the default templates. Once the user has saved these will not be used.
	//however these functions are also called to get the values and data for the presets dropdown.
	$templaterequires=filter_videoeasy_fetch_template_requires($players);
	$templatebodys=filter_videoeasy_fetch_template_bodys($players);
	$templatescripts=filter_videoeasy_fetch_template_scripts($players);
	$templatestyles=filter_videoeasy_fetch_template_styles($players);
	$templatedefaults=filter_videoeasy_fetch_template_defaults($players);
	$templatekeys=filter_videoeasy_fetch_template_keys($players);
	$templatenames=filter_videoeasy_fetch_template_names($players);
	
	
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
		$defvalue= $templatekeys[$templateid];
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templatekey_' . $templateid , 
				get_string('templatekey', 'filter_videoeasy',$templateid),
				get_string('templatekey_desc', 'filter_videoeasy'), 
				$defvalue, PARAM_TEXT));
				
		//template name
		$defvalue=  $templatenames[$templateid];
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templatename_' . $templateid , 
				get_string('templatename', 'filter_videoeasy',$templateid),
				get_string('templatename_desc', 'filter_videoeasy'), 
				$defvalue, PARAM_RAW));
				
		//template JS heading
		//$defvalue= filter_videoeasy_fetch_default($conf,'templaterequire_js_' . $oldplayers[$templateid], $templaterequires[$templateid]['js']);
		$defvalue= $templaterequires[$templateid]['js'];
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templaterequire_js_' . $templateid , 
				$playername  . get_string('templaterequirejs', 'filter_videoeasy') ,
				get_string('templaterequirejs_desc', 'filter_videoeasy'), 
				 $defvalue), PARAM_RAW,50);		
				
		//template css heading
		$defvalue= $templaterequires[$templateid]['css'];
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templaterequire_css_' . $templateid , 
				$playername  . get_string('templaterequirecss', 'filter_videoeasy'),
				get_string('templaterequirecss_desc', 'filter_videoeasy'), 
				 $defvalue), PARAM_RAW,50);
		
		//template jquery heading
		//$defvalue= filter_videoeasy_fetch_default($conf,'templaterequire_jquery_' . $oldplayers[$templateid], $templaterequires[$templateid]['jquery']);
		$defvalue=  $templaterequires[$templateid]['jquery'];
		 $settings_page->add(new admin_setting_configcheckbox('filter_videoeasy/templaterequire_jquery_' . $templateid, 
				$playername  . get_string('templaterequirejquery', 'filter_videoeasy'),
				get_string('templaterequirejquery_desc', 'filter_videoeasy'), 
				 $defvalue));		 
				 
		//template body
		//$defvalue= filter_videoeasy_fetch_default($conf,'templatepreset_' .$oldplayers[$templateid], $templatebodys[$templateid]);
		$defvalue= $templatebodys[$templateid];
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatepreset_' . $templateid,
					$playername  . get_string('template', 'filter_videoeasy'),
					get_string('template_desc', 'filter_videoeasy'),$defvalue));

		//template body script
		//$defvalue= filter_videoeasy_fetch_default($conf,'templatescript_' . $oldplayers[$templateid],$templatescripts[$templateid]);
		$defvalue= $templatescripts[$templateid];
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatescript_' . $templateid,
					$playername  . get_string('templatescript', 'filter_videoeasy'),
					get_string('templatescript_desc', 'filter_videoeasy'),$defvalue));


		//template defaults	
		// $defvalue= filter_videoeasy_fetch_default($conf,'templatedefaults_' . $oldplayers[$templateid],$templatedefaults[$templateid]);	
		$defvalue= $templatedefaults[$templateid];			
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatedefaults_' . $templateid,
					$playername  . get_string('templatedefaults', 'filter_videoeasy'),
					get_string('templatedefaults_desc', 'filter_videoeasy'),$defvalue));

		//additional JS (upload)
		//see here: for integrating this https://moodle.org/mod/forum/discuss.php?d=227249
		$name = 'filter_videoeasy/uploadjs_' . $templateid;
		$title = $playername . ' ' .  get_string('uploadjs', 'filter_videoeasy') ;
		$description = get_string('uploadjs_desc', 'filter_videoeasy');
		$settings_page->add(new admin_setting_configstoredfile($name, $title, $description, 'uploadjs_' . $templateid));
		
		//template body css
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatestyle_' . $templateid,
					get_string('templatestyle', 'filter_videoeasy',$templateid),
					get_string('templatestyle_desc', 'filter_videoeasy'),
					$templatestyles[$templateid],PARAM_RAW));
		
		//additional CSS (upload)
		$name = 'filter_videoeasy/uploadcss_' . $templateid;
		$title =$playername . ' ' . get_string('uploadcss', 'filter_videoeasy');
		$description = get_string('uploadcss_desc', 'filter_videoeasy');
		$settings_page->add(new admin_setting_configstoredfile($name, $title, $description, 'uploadcss_' . $templateid));

					
		//add page to category
		$ADMIN->add('filter_videoeasy_category', $settings_page);
	}
}