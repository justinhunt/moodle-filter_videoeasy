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
    $videoeasy_category_name='videoeasy_category';
    $videoeasy_category = new admin_category($videoeasy_category_name, 'Video Easy');
    $ADMIN->add('filtersettings', $videoeasy_category);
	 $conf = get_config('filter_videoeasy');
	 
	 //template settings Page Settings 
	 // we changed this to use the default settings id for the top page. This way in the settings link on the manage filters
	 //page, we will arrive here. Else the link will show there, but it will error out if clicked.
   //	$settings_page = new admin_settingpage('filter_videoeasy_templatepage_handlers',get_string('templatepageheading_handlers', 'filter_videoeasy'));
	$settings_page = new admin_settingpage('filtersettingvideoeasy',get_string('templatepageheading_handlers', 'filter_videoeasy'));

    $settings_page->add(new admin_setting_configtext('filter_videoeasy/templatecount',
        get_string('templatecount', 'filter_videoeasy'),
        get_string('templatecount_desc', 'filter_videoeasy'),
        \filter_videoeasy\videoeasy_utils::FILTER_VIDEOEASY_TEMPLATE_COUNT, PARAM_INT,20));
	
	//heading of template
	$settings_page->add(new admin_setting_heading('filter_videoeasy/extensionheading', 
			get_string('extensionheading', 'filter_videoeasy'), ''));
	
	//get the players we use and the extensions we handle
	$players = \filter_videoeasy\videoeasy_utils::fetch_players($conf);
	$extensions = \filter_videoeasy\videoeasy_utils::fetch_extensions();
	
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
	$defaultexts = implode(',',\filter_videoeasy\videoeasy_utils::fetch_default_extensions());
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
	$ADMIN->add($videoeasy_category_name, $settings_page);


    $videoeasytemplatesadmin_settings = new admin_externalpage('videoeasytemplatesadmin', get_string('templates', 'filter_videoeasy'),
        $CFG->wwwroot . '/filter/videoeasy/videoeasytemplatesadmin.php' );

    $ADMIN->add($videoeasy_category_name, $videoeasytemplatesadmin_settings);

    //Templates
    $template_pages = \filter_videoeasy\settingstools::fetch_template_pages($conf);
    foreach ($template_pages as $template_page) {
        $ADMIN->add($videoeasy_category_name, $template_page);
    }

}
