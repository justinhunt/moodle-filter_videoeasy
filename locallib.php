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

class filter_videoeasy_template_script_generator {
	/** @var mixed int index of template*/
    public $templateindex;
    public $ext;
    
	 /**
     * Constructor
     */
    public function __construct($templateindex,$ext) {
        $this->templateindex = $templateindex;
        $this->ext = $ext;
    }//end of constructor
    

    public function get_template_script(){
    	global $CFG;
    	
    	$templateid = $this->templateindex;
    	$ext = $this->ext;
    	$conf = get_config('filter_videoeasy');
    	
		//are we AMD and Moodle 2.9 or more?
		$require_amd = $conf->{'template_amd_' . $templateid} && $CFG->version>=2015051100;

		//get presets
		$thescript=$conf->{'templatescript_' . $templateid};
		$defaults=$conf->{'templatedefaults_' . $templateid};
		//merge defaults with blank proparray  to get all fields
		$defaultsarray = filter_videoeasy_parsepropstring($defaults);
		$proparray=array_merge(filter_videoeasy_fetch_emptyproparray(), $defaultsarray);


		//these props are in the opts array in the allopts[] array on the page
		//since we are writing the JS we write the opts['name'] into the js, but 
		//have to remove quotes from template eg "@@VAR@@" => opts['var'] //NB no quotes.
		//thats worth knowing for the admin who writed the JS load code for the template.
		foreach($proparray as $propname=>$propvalue){
			//case: single quotes
			$thescript = str_replace("'@@" . $propname ."@@'",'opts["' . $propname . '"]',$thescript);
			//case: double quotes
			$thescript = str_replace('"@@' . $propname .'@@"',"opts['" . $propname . "']",$thescript);
			//case: no quotes
			$thescript = str_replace('@@' . $propname .'@@',"opts['" . $propname . "']",$thescript);
		}

		if($require_amd){

			//figure out if this is https or http. We don't want to scare the browser
			$scheme='http:';
			if(strpos(strtolower($CFG->wwwroot),'https')===0){$scheme='https:';}


			//this is for loading as dependencies the uploaded or linked files
			//massage the js URL depending on schemes and rel. links etc. Then insert it
				$requiredjs = $conf->{'templaterequire_js_' . $templateid};
				$requiredjs_shim =trim($conf->{'templaterequire_js_shim_' . $templateid});
				if($requiredjs){
					if(strpos($requiredjs,'//')===0){
						$requiredjs = $scheme . $requiredjs;
					}elseif(strpos($requiredjs,'/')===0){
						$requiredjs = $CFG->wwwroot . $requiredjs;
					}
				}
	
				//if we have an uploaded JS file, then lets include that
				$uploadjsfile = $conf->{'uploadjs_' . $templateid};
				$uploadjs_shim =trim($conf->{'uploadjs_shim_' . $templateid});
				if($uploadjsfile){
					$uploadjs = filter_videoeasy_internal_file_url($uploadjsfile,'uploadjs_' . $templateid);
				}

			//These arrays will be used to build the final amd function dependencies and exports
			$requires = array();
			$params = array();
			
			//these arrays are used for shimming
			$shimkeys= array();
			$shimpaths= array();
			$shimexports= array();
			
			//key of the current video easy template
			$currentkey = $conf->{'templatekey_' . $templateid};
			
			//if we have a url based required js
			//either load it, or shim and load it
			if($requiredjs){
				if($requiredjs_shim!=''){
					$shimkeys[] = $currentkey . '-requiredjs'; 
					
					//remove .js from end of js filepath if its there
					if(strrpos($requiredjs,'.js')==(strlen($requiredjs) -3)){
						$requiredjs = substr($requiredjs, 0, -3);
					}
					
					$shimpaths[]= $requiredjs;
					$shimexports[]=$requiredjs_shim;
					$requires[] = "'" . $currentkey . '-requiredjs' . "'";
					$params[]=$requiredjs_shim;
				}else{
					$requires[] =  "'" . $requiredjs . "'";
					$params[] = "requiredjs_" . $currentkey;
				}
			}
			
			//if we have an uploadedjs library
			//either load it, or shim and load it			
			if($uploadjsfile){
				if($uploadjs_shim!=''){
					$shimkeys[] = $currentkey . '-uploadjs';
					
					//remove .js from end of js filepath if its there
					if(strrpos($uploadjs,'.js')==(strlen($uploadjs) -3)){
						$uploadjs = substr($uploadjs, 0, -3);
					}
					
					$shimpaths[]=$uploadjs;					
					$shimexports[]=$uploadjs_shim;
					$requires[] ="'" .  $currentkey . '-uploadjs' . "'";
					$params[]=$uploadjs_shim;
				}else{
					$requires[] =  "'" . $uploadjs . "'";
					$params[] = "uploadjs_" . $currentkey;
				}
			}
			
			//if we have a shim, lets build the javascript for that
			//actually we build a php object first, and then we will json_encode it
			$theshim = $this->build_shim_function($currentkey, $shimkeys, $shimpaths, $shimexports);
			
			
			//load a different jquery based on path if we are shimming
			//this is because, sigh, Moodle used no conflict for jquery, but
			//shimmed plugins rely on jquery n global scope
			//see: http://www.requirejs.org/docs/jquery.html#noconflictmap
			//so we add a separate load of jquery with name '[currentkey]-jquery' and export it as '$', and don't use the 
			//already set up (by mooodle and AMD) 'jquery' path.
			//we add jquery to beginning of requires and params using unshift. But the end would be find too
			if(!empty($shimkeys)){
				array_unshift($requires,"'" . $currentkey . '-jquery' . "'");
				array_unshift($params,'$');
			}else{
				array_unshift($requires,"'" . 'jquery' . "'");
				array_unshift($params,'$');
			}
			
			//Assemble the final javascript to pass to browser
			$thefunction = "define('filter_videoeasy_d" . $templateid . "',[" . implode(',',$requires) . "], function(" . implode(',',$params) . "){ ";
			$thefunction .= "return function(opts){" . $thescript. " \r\n}; });";
			$return_js = $theshim . $thefunction;
			
		//If not AMD return regular JS
		}else{
		
			$return_js = "if(typeof filter_videoeasy_extfunctions == 'undefined'){filter_videoeasy_extfunctions={};}";
			$return_js .= "filter_videoeasy_extfunctions['" . $ext . "']= function(opts) {" . $thescript. " \r\n};";
		}
    	return $return_js;
    }//end of function
	
	protected function build_shim_function($currentkey, $shimkeys, $shimpaths, $shimexports){
			global $CFG;
			
			$theshim="";
			$theshimtemplate = "requirejs.config(@@THESHIMCONFIG@@);";
			if(!empty($shimkeys)){
				$paths = new stdClass();
				$shim = new stdClass();
				
				//Add a path to  a separetely loaded jquery for shimmed libraries
				$paths->{$currentkey . '-jquery'} = $CFG->wwwroot  . '/filter/videoeasy/jquery/jquery-1.12.4.min'; 
				$jquery_shimconfig = new stdClass();
				$jquery_shimconfig->exports = '$';
				$shim->{$currentkey . '-jquery'}=$jquery_shimconfig;
				
				for($i=0;$i<count($shimkeys);$i++){
					$paths->{$shimkeys[$i]} = $shimpaths[$i];
					$oneshimconfig = new stdClass();
					$oneshimconfig->exports = $shimexports[$i];
					$oneshimconfig->deps = array($currentkey . '-jquery');
					$shim->{$shimkeys[$i]} = $oneshimconfig;
				}
				
				//buuld the actual function that will set up our shim
				//we use php object -> json to kep it simple.
				//But its still not simple
				$theshimobject = new stdClass();
				$theshimobject->paths=$paths;
				$theshimobject->shim =$shim;
				$theshimconfig=json_encode($theshimobject,JSON_UNESCAPED_SLASHES);
				$theshim = str_replace('@@THESHIMCONFIG@@', $theshimconfig,$theshimtemplate);
			}
		return $theshim;
	}
}//end of class


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
		global $PAGE;

		//build our select form
		$keys = array_keys($this->presetdata);
		$usearray = array();

		foreach($keys as $key){
			$usearray[$key]=$this->presetdata[$key]['key'];
		}

		$presetsjson = json_encode($this->presetdata);
		$presetscontrol = html_writer::tag('input', '', array('id' => 'id_s_filter_videoeasy_presetdata_' . $this->templateindex, 'type' => 'hidden', 'value' => $presetsjson));


		//Add javascript handler for presets
		$PAGE->requires->js_call_amd('filter_videoeasy/videoeasy_presets_amd',
			'init',array(array('templateindex'=>$this->templateindex)));

		$select = html_writer::select($usearray,'filter_videoeasy/presets','','--custom--');

		$dragdropsquare = html_writer::tag('div',get_string('bundle','filter_videoeasy'),array('id' => 'id_s_filter_videoeasy_dragdropsquare_' . $this->templateindex,
			'class' => 'filter_videoeasy_dragdropsquare'));

		return format_admin_setting($this, $this->visiblename,
			$dragdropsquare . '<div class="form-text defaultsnext">'. $presetscontrol . $select .  '</div>',
			$this->information, true, '','', $query);

	}

	 protected function parse_preset_template(\SplFileInfo $fileinfo){
		$file=$fileinfo->openFile("r");
		$content = "";
		while(!$file->eof()){
			$content .= $file->fgets();
		}
		$preset_object = json_decode($content);
		if($preset_object && is_object($preset_object)){
			return get_object_vars($preset_object);
		}else{
			return false;
		}
	}//end of parse preset template


	public function fetch_presets(){          
		global $CFG;
		$ret = array();
		$dir = new \DirectoryIterator($CFG->dirroot . '/filter/videoeasy/presets');
		foreach($dir as $fileinfo){
			if(!$fileinfo->isDot()){
			  $preset = $this->parse_preset_template($fileinfo);
			  if($preset){
				$ret[]=$preset;
			  }
			}
		}
	   return $ret;
	}//end of fetch presets function
	
	public static function set_preset_to_config($preset, $templateindex){
		$fields = array();
		$fields['name']='templatename';
		$fields['key']='templatekey';
		$fields['instructions']='templateinstructions';
		$fields['body']='templatepreset';
		$fields['bodyend']='templateend';
		$fields['requirecss']='templaterequire_css';
		$fields['requirejs']='templaterequire_js';
		$fields['shim']='templaterequire_js_shim';
		$fields['defaults']='templatedefaults';
		$fields['amd']='template_amd';
		$fields['script']='templatescript';
		$fields['style']='templatestyle';
		$fields['dataset']='dataset';
		$fields['datavars']='datavars';
		
		foreach($fields as $fieldkey=>$fieldname){
			if(array_key_exists($fieldkey,$preset)){
				$fieldvalue=$preset[$fieldkey];
			}else{
				$fieldvalue='';
			}
			set_config($fieldname . '_' . $templateindex, $fieldvalue, 'filter_videoeasy');
		}
	}//End of set_preset_to_config
}//end of class