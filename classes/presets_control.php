<?php
/**
 * Created by PhpStorm.
 * User: justin
 * Date: 17/03/24
 * Time: 16:44
 */

namespace filter_videoeasy;

require_once($CFG->libdir . '/adminlib.php');

class presets_control extends \admin_setting
{

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
        parent::__construct($name, $visiblename, $information,$templateindex);
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
            $name = $this->presetdata[$key]['name'];
            if(empty($name)){$name=$key;}
            $usearray[$key]=$name;
        }

        $presetsjson = json_encode($this->presetdata);
        $presetscontrol = \html_writer::tag('input', '', array('id' => 'id_s_filter_videoeasy_presetdata_' . $this->templateindex, 'type' => 'hidden', 'value' => $presetsjson));


        //Add javascript handler for presets
        $PAGE->requires->js_call_amd('filter_videoeasy/videoeasy_presets_amd',
            'init',array(array('templateindex'=>$this->templateindex)));

        $select = \html_writer::select($usearray,'filter_videoeasy/presets','','--custom--');

        $dragdropsquare = \html_writer::tag('div',get_string('bundle','filter_videoeasy'),array('id' => 'id_s_filter_videoeasy_dragdropsquare_' . $this->templateindex,
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
        $fields['version']='templateversion';


        foreach($fields as $fieldkey=>$fieldname){
            if(array_key_exists($fieldkey,$preset)){
                $fieldvalue=$preset[$fieldkey];
            }else{
                $fieldvalue='';
            }
            set_config($fieldname . '_' . $templateindex, $fieldvalue, 'filter_videoeasy');
        }
    }//End of set_preset_to_config
}