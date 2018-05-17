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

namespace filter_videoeasy;

use filter_videoeasy\presets_control;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');


/**
 *
 * This is a class containing static functions for general PoodLL filter things
 * like embedding recorders and managing them
 *
 * @package   filter_poodll
 * @since      Moodle 3.1
 * @copyright  2016 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
class settingstools
{

    //make a readable template name for menus and lists etc
public static function fetch_template_title($conf,$tindex,$typeprefix=true){
    //template display name
    $tname='';
    if($conf && property_exists($conf,'templatename_' . $tindex)){
        $tname = $conf->{'templatename_' . $tindex};
    }
    if(empty($tname) && $conf && property_exists($conf,'templatekey_' . $tindex)){
        $tname = $conf->{'templatekey_' . $tindex};
    }
    if(empty($tname)){$tname=$tindex;}

    if(!$typeprefix){
        return $tname;
    }

    if($conf && property_exists($conf,'templatekey_' . $tindex) && property_exists($conf,'template_showatto_' . $tindex) &&  $conf->{'template_showatto_' . $tindex} > 0){
        $templatetitle = get_string('templatepagewidgetheading', 'filter_poodll',$tname);
    }elseif($conf && property_exists($conf,'templatekey_' . $tindex) && property_exists($conf,'template_showplayers_' . $tindex) &&  $conf->{'template_showplayers_' . $tindex} > 0){
        $templatetitle = get_string('templatepageplayerheading', 'filter_poodll',$tname);
    }else{
        $templatetitle = get_string('templatepageheading', 'filter_poodll',$tname);
    }
    return $templatetitle;
}



public static function fetch_template_pages($conf){
		$pages = array();

    //Add the template pages
    if($conf && property_exists($conf,'templatecount')){
        $templatecount = $conf->templatecount;
    }else{
        $templatecount =  \filter_videoeasy\videoeasy_utils::FILTER_VIDEOEASY_TEMPLATE_COUNT;
    }

    //fetch preset data, just once so we do nto need to repeat the call a zillion times
    $presetdata = presets_control::fetch_presets();

    for($tindex=1;$tindex<=$templatecount;$tindex++){

        //template display name
        if($conf && property_exists($conf,'templatekey_' . $tindex)){
            $tname = $conf->{'templatekey_' . $tindex};
            if(empty($tname)){$tname=$tindex;}
        }else{
            $tname = $tindex;
        }

        //template settings Page Settings 
        $settings_page = new \admin_settingpage('filter_videoeasy_templatepage_' . $tindex,get_string('templatepageheading', 'filter_videoeasy',$tname),'moodle/site:config',true);

        //heading of template
        $settings_page->add(new \admin_setting_heading('filter_videoeasy/templateheading_' . $tindex,
            get_string('templateheading', 'filter_videoeasy', $tname), ''));

        //presets
        //this is a custom control, that allows the user to select a preset from a list.
        $settings_page->add(new \filter_videoeasy\presets_control('filter_videoeasy/templatepresets_' . $tindex,
            get_string('presets', 'filter_videoeasy'), get_string('presets_desc', 'filter_videoeasy'),$tindex,$presetdata));


        //template key
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtext('filter_videoeasy/templatekey_' . $tindex ,
            get_string('templatekey', 'filter_videoeasy',$tindex),
            get_string('templatekey_desc', 'filter_videoeasy'),
            $defvalue, PARAM_TEXT));

        //template name
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtext('filter_videoeasy/templatename_' . $tindex ,
            get_string('templatename', 'filter_videoeasy',$tindex),
            get_string('templatename_desc', 'filter_videoeasy'),
            $defvalue, PARAM_RAW));

        //template version
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtext('filter_videoeasy/templateversion_' . $tindex ,
            get_string('templateversion', 'filter_videoeasy',$tindex),
            get_string('templateversion_desc', 'filter_videoeasy'),
            $defvalue, PARAM_TEXT));

        //template instructions
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtextarea('filter_videoeasy/templateinstructions_' . $tindex,
            get_string('templateinstructions', 'filter_videoeasy',$tindex),
            get_string('templateinstructions_desc', 'filter_videoeasy'),
            $defvalue,PARAM_RAW));

        //template amd
        $defvalue= 0;
        $yesno = array('0'=>get_string('no'),'1'=>get_string('yes'));
        $settings_page->add(new \admin_setting_configselect('filter_videoeasy/template_amd_' . $tindex,
            get_string('templaterequire_amd', 'filter_videoeasy',$tindex),
            get_string('templaterequire_amd_desc', 'filter_videoeasy'),
            $defvalue,$yesno));


        //template JS heading
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtext('filter_videoeasy/templaterequire_js_' . $tindex ,
            $tname  . get_string('templaterequirejs', 'filter_videoeasy') ,
            get_string('templaterequirejs_desc', 'filter_videoeasy'),
            $defvalue), PARAM_RAW,50);

        //template requiredjs_shim
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtext('filter_videoeasy/templaterequire_js_shim_' . $tindex ,
            get_string('templaterequirejsshim', 'filter_videoeasy',$tindex),
            get_string('templaterequirejsshim_desc', 'filter_videoeasy'),
            $defvalue, PARAM_RAW));


        //template css heading
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtext('filter_videoeasy/templaterequire_css_' . $tindex ,
            $tname  . get_string('templaterequirecss', 'filter_videoeasy'),
            get_string('templaterequirecss_desc', 'filter_videoeasy'),
            $defvalue), PARAM_RAW,50);


        //template body
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtextarea('filter_videoeasy/templatepreset_' . $tindex,
            $tname  . get_string('template', 'filter_videoeasy'),
            get_string('template_desc', 'filter_videoeasy'),$defvalue));

        //template body script
        $defvalue= '';
        $setting = new \admin_setting_configtextarea('filter_videoeasy/templatescript_' . $tindex,
            $tname  . get_string('templatescript', 'filter_videoeasy'),
            get_string('templatescript_desc', 'filter_videoeasy'),$defvalue);
        $setting->set_updatedcallback('filter_videoeasy_update_revision');
        $settings_page->add($setting);


        //template defaults	
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtextarea('filter_videoeasy/templatedefaults_' . $tindex,
            $tname  . get_string('templatedefaults', 'filter_videoeasy'),
            get_string('templatedefaults_desc', 'filter_videoeasy'),$defvalue));

        //additional JS (upload)
        //see here: for integrating this https://moodle.org/mod/forum/discuss.php?d=227249
        $name = 'filter_videoeasy/uploadjs_' . $tindex;
        $title = $tname . ' ' .  get_string('uploadjs', 'filter_videoeasy') ;
        $description = get_string('uploadjs_desc', 'filter_videoeasy');
        $settings_page->add(new \admin_setting_configstoredfile($name, $title, $description, 'uploadjs_' . $tindex));

        //template uploadjs_shim
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtext('filter_videoeasy/uploadjs_shim_' . $tindex ,
            get_string('templateuploadjsshim', 'filter_videoeasy',$tindex),
            get_string('templateuploadjsshim_desc', 'filter_videoeasy'),
            $defvalue, PARAM_RAW));


        //template body css
        $defvalue= '';
        $setting=new \admin_setting_configtextarea('filter_videoeasy/templatestyle_' . $tindex,
            get_string('templatestyle', 'filter_videoeasy',$tindex),
            get_string('templatestyle_desc', 'filter_videoeasy'),
            $defvalue,PARAM_RAW);
        $setting->set_updatedcallback('filter_videoeasy_update_revision');
        $settings_page->add($setting);

        //additional CSS (upload)
        $name = 'filter_videoeasy/uploadcss_' . $tindex;
        $title =$tname . ' ' . get_string('uploadcss', 'filter_videoeasy');
        $description = get_string('uploadcss_desc', 'filter_videoeasy');
        $settings_page->add(new \admin_setting_configstoredfile($name, $title, $description, 'uploadcss_' . $tindex));

        //alternative content
        $defvalue= '';
        $settings_page->add(new \admin_setting_configtextarea('filter_videoeasy/templatealternate_' . $tindex,
            get_string('templatealternate', 'filter_videoeasy',$tindex),
            get_string('templatealternate_desc', 'filter_videoeasy'),
            $defvalue,PARAM_RAW));

        $pages[] = $settings_page;
    }


    return $pages;
	}//end of function fetch template pages

}//end of class
