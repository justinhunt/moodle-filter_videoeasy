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
	$playeroptions=array();
	foreach($players as $keyvalue){
		//player name
		 if($conf && property_exists($conf,'templatekey_' . $keyvalue)){
		 	$playername = $conf->{'templatekey_' . $keyvalue};
		 	if(!$playername || empty($playername)){$playername='Player ??';}
		 }else{
		 	$playername = get_string('player_' .$keyvalue,'filter_videoeasy');
		 }
		$playeroptions[$keyvalue] = $playername;
	}
	
	//add extensions checkbox
	foreach($extensions as $ext){
		$settings_page->add(new admin_setting_configcheckbox('filter_videoeasy/handle' . $ext, get_string('handle', 'filter_videoeasy', strtoupper($ext)), '', 0));
		$settings_page->add(new admin_setting_configselect('filter_videoeasy/useplayer' . $ext, get_string('useplayer', 'filter_videoeasy', strtoupper($ext)),  get_string('useplayerdesc', 'filter_videoeasy'), 'flowplayer', $playeroptions));
	}
	
	//add jquery path 
	$settings_page->add(new admin_setting_configtext('filter_videoeasy/jqueryurl', 
				get_string('jqueryurl', 'filter_videoeasy'),
				get_string('jqueryurl_desc', 'filter_videoeasy'), 
				 '//code.jquery.com/jquery-1.11.2.min.js', PARAM_RAW,50));
	
	//add page to category
	$ADMIN->add('filter_videoeasy_category', $settings_page);
	
	//prepare template info
	$templaterequires=filter_videoeasy_fetch_template_requires($players);
	$templatepresets=filter_videoeasy_fetch_template_presets($players);
	$templatescripts=filter_videoeasy_fetch_template_scripts($players);
	$templatedefaults=filter_videoeasy_fetch_template_defaults($players);
	
	//add 10 templates
	foreach($players as $player){
		
		//player name
		$playername=$playeroptions[$player];
		
		 //template settings Page Settings 
		$settings_page = new admin_settingpage('filter_videoeasy_templatepage_' . $player,get_string('templatepageheading', 'filter_videoeasy',$playername));
		
		//heading of template
		$settings_page->add(new admin_setting_heading('filter_videoeasy/templateheading_' . $player, 
				get_string('templateheading', 'filter_videoeasy', $playername), ''));
				
		//template key
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templatekey_' . $player , 
				get_string('templatekey', 'filter_videoeasy',$player),
				get_string('templatekey_desc', 'filter_videoeasy'), 
				 get_string('player_' .$player,'filter_videoeasy'), PARAM_RAW));
				
		//template JS heading
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templaterequire_js_' . $player , 
				$playername  . get_string('templaterequirejs', 'filter_videoeasy') ,
				get_string('templaterequirejs_desc', 'filter_videoeasy'), 
				 $templaterequires[$player]['js']), PARAM_RAW,50);		
				
		//template css heading
		 $settings_page->add(new admin_setting_configtext('filter_videoeasy/templaterequire_css_' . $player , 
				$playername  . get_string('templaterequirecss', 'filter_videoeasy'),
				get_string('templaterequirecss_desc', 'filter_videoeasy'), 
				 $templaterequires[$player]['css']), PARAM_RAW,50);
		
		//template jquery heading		
		 $settings_page->add(new admin_setting_configcheckbox('filter_videoeasy/templaterequire_jquery_' . $player, 
				$playername  . get_string('templaterequirejquery', 'filter_videoeasy'),
				get_string('templaterequirejquery_desc', 'filter_videoeasy'), 
				 $templaterequires[$player]['jquery']));		 
				 
		//template body
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatepreset_' . $player,
					$playername  . get_string('template', 'filter_videoeasy'),
					get_string('template_desc', 'filter_videoeasy'),$templatepresets[$player]));

		//template body script
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatescript_' . $player,
					$playername  . get_string('templatescript', 'filter_videoeasy'),
					get_string('templatescript_desc', 'filter_videoeasy'),$templatescripts[$player]));


		//template defaults			
		 $settings_page->add(new admin_setting_configtextarea('filter_videoeasy/templatedefaults_' . $player,
					$playername  . get_string('templatedefaults', 'filter_videoeasy'),
					get_string('templatedefaults_desc', 'filter_videoeasy'),$templatedefaults[$player]));
					
		//additional JS (upload)
		//see here: for integrating this https://moodle.org/mod/forum/discuss.php?d=227249
		$name = 'filter_videoeasy/uploadjs_' . $player;
		$title = $playername . ' ' .  get_string('uploadjs', 'filter_videoeasy') ;
		$description = get_string('uploadjs_desc', 'filter_videoeasy');
		$settings_page->add(new admin_setting_configstoredfile($name, $title, $description, 'uploadjs_' . $player));
		
		//additional CSS (upload)
		$name = 'filter_videoeasy/uploadcss_' . $player;
		$title =$playername . ' ' . get_string('uploadcss', 'filter_videoeasy');
		$description = get_string('uploadcss_desc', 'filter_videoeasy');
		$settings_page->add(new admin_setting_configstoredfile($name, $title, $description, 'uploadcss_' . $player));

					
		//add page to category
		$ADMIN->add('filter_videoeasy_category', $settings_page);
	}
}