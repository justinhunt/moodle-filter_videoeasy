<?php
/*
 * Video Easy Moodle filter
* Copyright (C) 2014 Justin hunt
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * Video Easu filter local settings
 *
 * @package    filter
 * @subpackage videoeasy
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class videoeasy_filter_local_settings_form extends filter_local_settings_form {
	protected function definition_inner($mform) {
		global $CFG;
		require_once($CFG->dirroot.'/filter/videoeasy/lib.php');
		
		//get the players we use and the extensions we handle
        $siteconf = get_config('filter_videoeasy');
		$players = \filter_videoeasy\videoeasy_utils::fetch_players($siteconf);
		$extensions = \filter_videoeasy\videoeasy_utils::fetch_extensions();

	
		//create player select list
		//complex because of old playername mapping. Can be removed soon.(01/2015)
		$playeroptions=array();
		$oldplayers = \filter_videoeasy\videoeasy_utils::fetch_oldplayers();
		$playeroptions['sitedefault'] = get_string('sitedefault','filter_videoeasy');
		/*
		foreach($players as $keyvalue){
			//player name
			 if($siteconf && property_exists($siteconf,'templatekey_' . $keyvalue)){
				$playername = $siteconf->{'templatekey_' . $keyvalue};
				if(!$playername || empty($playername)){$playername='Player ??';}
			 }else{
			 	if($siteconf && property_exists($siteconf,'templatekey_' . $oldplayers[$keyvalue])){
		 			$playername = get_string('player_' . $oldplayers[$keyvalue],'filter_videoeasy');
				}else{
					$playername = get_string('player','filter_videoeasy', $keyvalue);
				}
			 }
			$playeroptions[$keyvalue] = $playername;
		}
		*/
		
		foreach($players as $templateid){
			//player name
			$playername='Player ??';
			 if($siteconf && property_exists($siteconf,'templatename_' . $templateid)){
				$playername = $siteconf->{'templatename_' . $templateid};
				$playerkey = $siteconf->{'templatekey_' . $templateid};
			 }elseif($conf && property_exists($siteconf,'templatekey_' . $templateid)){
				$playername = $siteconf->{'templatekey_' . $templateid};
				$playerkey = $templateid;
			 }
			$playeroptions[$playerkey] = $playername;
		}
		
		
		//add extensions checkbox and dropdown list
		foreach($extensions as $ext){
            $ext = trim($ext);
			//extension checkbox	
			$elname = 'handle' . $ext;	
			$mform->addElement('advcheckbox', $elname, 
					get_string('handle', 'filter_videoeasy', $ext),
					'', 
					array('group'=>1), array(0, 1));
			$mform->setType($elname, PARAM_INT);
			$mform->setDefault($elname, ($siteconf && property_exists($siteconf,'handle' . $ext)) ? $siteconf->{'handle' . $ext} : 0);
			
			//player dropdown list
			$elname = 'useplayer' . $ext;	
			$mform->addElement('select', $elname, get_string('useplayer', 'filter_videoeasy', strtoupper($ext)),$playeroptions);
	  		$mform->setDefault($elname, 'sitedefault');

		
		}
	
		/*
		foreach($players as $keyvalue){
			$playeroptions[$keyvalue] = get_string('player_' .$keyvalue,'filter_videoeasy');
		}
		 $settings->add(new admin_setting_configselect('filter_videoeasy/useplayer', get_string('useplayer', 'filter_videoeasy'), get_string('useplayerdesc', 'filter_videoeasy'), 'flowplayer', $playeroptions));
	
		//add extensions checkbox
		foreach($extensions as $ext){
			$settings->add(new admin_setting_configcheckbox('filter_videoeasy/handle' . $ext, get_string('handle', 'filter_videoeasy') . ' ' . $ext, '', 0));
		}
	*/
	/*
		//prepare template info
		$templaterequires=filter_videoeasy_fetch_template_requires($players);
		$templatepresets=filter_videoeasy_fetch_template_presets($players);
		$templatescripts=filter_videoeasy_fetch_template_scripts($players);
		$templatedefaults=filter_videoeasy_fetch_template_defaults($players);
		$textareaoptions = 'wrap="virtual" class="filter_videoeasy-textarea row-fluid" rows="3" cols="9"';
		$textatts = array();

		//add 10 templates
		foreach($players as $player){
			$playername = get_string('player_' .$player,'filter_videoeasy');
			
			
			$elname = 'templateheading_' . $player;
			$mform->addElement('header',$elname,get_string('templateheading', 'filter_videoeasy') . ' ' . $playername);
				
			//template JS heading
			$elname = 'templaterequire_js_' . $player;
			 $mform->addElement('text',$elname, 
					$playername  . get_string('templaterequirejs', 'filter_videoeasy') ,
					$textatts);
			$mform->setType($elname, PARAM_RAW);
			$mform->setDefault($elname, $templaterequires[$player]['js']);
					//get_string('templaterequirejs_desc', 'filter_videoeasy'), 
		
				
			//template css heading
			$elname = 'templaterequire_css_' . $player;
			$mform->addElement('text',$elname, 
					$playername  . get_string('templaterequirecss', 'filter_videoeasy'),
					$textatts);
			$mform->setType($elname, PARAM_RAW);
			$mform->setDefault($elname, $templaterequires[$player]['css']);
					
					//get_string('templaterequirecss_desc', 'filter_videoeasy'), 

		
			//template jquery heading	
			$elname = 'templaterequire_jquery_' . $player;	
			$mform->addElement('advcheckbox', $elname, 
					$playername  . get_string('templaterequirejquery', 'filter_videoeasy'),
					get_string('templaterequirejquery_desc', 'filter_videoeasy'), 
					array('group'=>1), array(0, 1));
			$mform->setType($elname, PARAM_INT);
			$mform->setDefault($elname, $templaterequires[$player]['jquery']);
		 
				 
			//template body
			$elname =  'templatepreset_' . $player;
			$mform->addElement('textarea',$elname,
						$playername  . get_string('template', 'filter_videoeasy'),
						$textareaoptions);
			$mform->setType($elname, PARAM_RAW);
			$mform->setDefault($elname, $templatepresets[$player]);
						
						//get_string('template_desc', 'filter_videoeasy'),$templatepresets[$player]));

			//template body script
			$elname =  'templatescript_' . $player;
			$mform->addElement('textarea',$elname,
						$playername  . get_string('templatescript', 'filter_videoeasy'),
						$textareaoptions);
			$mform->setType($elname, PARAM_RAW);
			$mform->setDefault($elname, $templatescripts[$player]);
						
						//get_string('templatescript_desc', 'filter_videoeasy'),$templatescripts[$player]));


			//template defaults			
			$elname = 'templatedefaults_' . $player;
			$mform->addElement('textarea', $elname,
						$playername  . get_string('templatedefaults', 'filter_videoeasy'),
						$textareaoptions);
			$mform->setType($elname, PARAM_RAW);
			$mform->setDefault($elname, $templatedefaults[$player]);
						
						//get_string('templatedefaults_desc', 'filter_videoeasy'),$templatedefaults[$player]));
						
			
		}
		*/


	}
}