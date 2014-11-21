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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/filter/videoeasy/lib.php');

if ($ADMIN->fulltree) {
	
	
	//heading of template
	$settings->add(new admin_setting_heading('filter_videoeasy/extensionheading', 
			get_string('extensionheading', 'filter_videoeasy'), ''));
	
	//get the players we use and the extensions we handle
	$players = filter_videoeasy_fetch_players();
	$extensions = filter_videoeasy_fetch_extensions();
	
	//create player select list
	$playeroptions=array();
	foreach($players as $keyvalue){
		$playeroptions[$keyvalue] = get_string('player_' .$keyvalue,'filter_videoeasy');
	}
	
	//add extensions checkbox
	foreach($extensions as $ext){
		$settings->add(new admin_setting_configcheckbox('filter_videoeasy/handle' . $ext, get_string('handle', 'filter_videoeasy', strtoupper($ext)), '', 0));
		$settings->add(new admin_setting_configselect('filter_videoeasy/useplayer' . $ext, get_string('useplayer', 'filter_videoeasy', strtoupper($ext)),  get_string('useplayerdesc', 'filter_videoeasy'), 'flowplayer', $playeroptions));
	}
	
	//prepare template info
	$templaterequires=filter_videoeasy_fetch_template_requires($players);
	$templatepresets=filter_videoeasy_fetch_template_presets($players);
	$templatescripts=filter_videoeasy_fetch_template_scripts($players);
	$templatedefaults=filter_videoeasy_fetch_template_defaults($players);
	
	//add 10 templates
	foreach($players as $player){
		$playername = get_string('player_' .$player,'filter_videoeasy');
		
		//heading of template
		$settings->add(new admin_setting_heading('filter_videoeasy/templateheading_' . $player, 
				get_string('templateheading', 'filter_videoeasy') . ' ' . $playername , ''));
				
		//template JS heading
		 $settings->add(new admin_setting_configtext('filter_videoeasy/templaterequire_js_' . $player , 
				$playername  . get_string('templaterequirejs', 'filter_videoeasy') ,
				get_string('templaterequirejs_desc', 'filter_videoeasy'), 
				 $templaterequires[$player]['js']), PARAM_RAW);		
				
		//template css heading
		 $settings->add(new admin_setting_configtext('filter_videoeasy/templaterequire_css_' . $player , 
				$playername  . get_string('templaterequirecss', 'filter_videoeasy'),
				get_string('templaterequirecss_desc', 'filter_videoeasy'), 
				 $templaterequires[$player]['css']), PARAM_RAW);
		
		//template jquery heading		
		 $settings->add(new admin_setting_configcheckbox('filter_videoeasy/templaterequire_jquery_' . $player, 
				$playername  . get_string('templaterequirejquery', 'filter_videoeasy'),
				get_string('templaterequirejquery_desc', 'filter_videoeasy'), 
				 $templaterequires[$player]['jquery']));		 
				 
		//template body
		 $settings->add(new admin_setting_configtextarea('filter_videoeasy/templatepreset_' . $player,
					$playername  . get_string('template', 'filter_videoeasy'),
					get_string('template_desc', 'filter_videoeasy'),$templatepresets[$player]));

		//template body script
		 $settings->add(new admin_setting_configtextarea('filter_videoeasy/templatescript_' . $player,
					$playername  . get_string('templatescript', 'filter_videoeasy'),
					get_string('templatescript_desc', 'filter_videoeasy'),$templatescripts[$player]));


		//template defaults			
		 $settings->add(new admin_setting_configtextarea('filter_videoeasy/templatedefaults_' . $player,
					$playername  . get_string('templatedefaults', 'filter_videoeasy'),
					get_string('templatedefaults_desc', 'filter_videoeasy'),$templatedefaults[$player]));
	}
}