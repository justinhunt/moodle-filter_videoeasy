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
 * Filter for expanding videoeasy templates 
 *
 * @package    filter
 * @subpackage videoeasy
 * @copyright  2014 Justin Hunt <poodllsupport@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php'); 

class filter_videoeasy extends moodle_text_filter {
	protected $adminconfig=null;
	protected $courseconfig=null;

    /**
     * Apply the filter to the text
     *
     * @see filter_manager::apply_filter_chain()
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     */
    public function filter($text, array $options = array()) {
			if (!is_string($text)) {
				// non string data can not be filtered anyway
				return $text;
			}
			$newtext = $text;
		
			//No links .. bail
			$havelinks = !(stripos($text, '</a>') ===false);
			if(!$havelinks){return $text;}
			
			 //$conf = get_object_vars(get_config('filter_videoeasy'));
			 //$conf = get_config('filter_videoeasy');
			 $this->adminconfig =get_config('filter_videoeasy');
					
			//get handle extensions
			$exts = filter_videoeasy_fetch_extensions();
			$handleexts = array();
			foreach($exts as $ext){
				if($ext!='youtube' && $this->fetchconf('handle' . $ext)){
					$handleexts[] = $ext;
				}
			}
			//do all the non youtube extensions in one foul swoop
			if(!empty($handleexts)){
				$handleextstring = implode('|',$handleexts);
				//$oldsearch = '/<a\s[^>]*href="([^"#\?]+\.(' .  $handleextstring. '))(\?d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
				$search='/<a\s[^>]*href="([^"#\?]+\.(' .  $handleextstring. '))(.*?)"[^>]*>([^>]*)<\/a>/is';
				$newtext = preg_replace_callback($search, 'self::filter_videoeasy_allexts_callback', $newtext);
			}
			
           //check for youtube
			if ($this->fetchconf('handleyoutube')) {
					 $search = '/<a\s[^>]*href="(?:https?:\/\/)?(?:www\.)?youtu(?:\.be|be\.com)\/(?:watch\?v=|v\/)?([\w-]{10,})(?:.*?)<\/a>/is';
					$newtext = preg_replace_callback($search, 'self::filter_videoeasy_youtube_callback', $newtext);
			}

		
		if (is_null($newtext) or $newtext === $text) {
			// error or not filtered
			return $text;
		}

		return $newtext;
    }
    
    private function fetchconf($prop){
    global $COURSE;

    	//I don't know why we need this whole courseconfig business.
    	//we are supposed to be able to just call $this->localconfig / $this->localconfig[$propertyname]
    	//as per here:https://docs.moodle.org/dev/Filters#Local_configuration , but its always empty
    	//at least at course context, in mod context it works ... 
    	//I just gave up and do it myself and stuff it in $this->courseconfig . bug?? Justin 20150106
    	if($this->localconfig && !empty($this->localconfig)){
    		$this->courseconfig = $this->localconfig;
    	}
    	if(!$this->courseconfig){
    		$this->courseconfig = filter_get_local_config('videoeasy', context_course::instance($COURSE->id)->id);
    	}
    	
		if($this->courseconfig && isset($this->courseconfig[$prop]) && $this->courseconfig[$prop] != 'sitedefault') {
			return $this->courseconfig[$prop];
		}else{
			return isset($this->adminconfig->{$prop}) ? $this->adminconfig->{$prop} : false;
		}
	}
	
	
	/**
	 * Replace youtube links with player
	 *
	 * @param  $link
	 * @return string
	 */
	private function filter_videoeasy_youtube_callback($link) {
		return $this->filter_videoeasy_process($link,'youtube');
	}
	
	
	/**
	 * Replace rss links with player
	 *
	 * @param  $link
	 * @return string
	 */
	private function filter_videoeasy_allexts_callback($link) {
		return $this->filter_videoeasy_process($link,$link[2]);
	}


	/**
	 * Replace mp4 or flv links with player
	 *
	 * @param  $link
	 * @param  $ext
	 * @return string
	 */
	private function filter_videoeasy_process($link, $ext) {
	global $CFG, $PAGE, $COURSE;
		//get template info
		$conf = get_config('filter_videoeasy');
		
		//get our template info
		$playerkey = $this->fetchconf('useplayer' . $ext);
		$templateid=0;
		$templatenumbers = filter_videoeasy_fetch_players();
		foreach($templatenumbers as $templatenumber){
			if($conf->{'templatekey_' . $templatenumber}==$playerkey){
				$templateid=$templatenumber;
				break;
			}
		}
		if(!$templateid){return;}
		
		
		$defaultposterimage =  $conf->{'defaultposterimage'};
		$require_js = $conf->{'templaterequire_js_' . $templateid};
		$require_css = $conf->{'templaterequire_css_' . $templateid};
		$require_jquery = $conf->{'templaterequire_jquery_' . $templateid};
		$uploadcssfile = $conf->{'uploadcss_' . $templateid};
		$uploadjsfile = $conf->{'uploadjs_' . $templateid};
		//are we AMD and Moodle 2.9 or more?
		$require_amd =  $conf->{'template_amd_' . $templateid} && $CFG->version>=2015051100;
		
		/*
		* Old Search Params 1 = url, 2=ext, 3=?d=widthxheight, 4=width,5=height,6=linkedtext
		*  Old Search Params 1 = url, 2=ext, 3=d=widthxheight&prop=value, 4=linkedtext
		*/
		//echo "player:" . $templateid;
//		print_r($link);
		//echo ("player:" . $templateid);
		//echo ("ext:" . $ext);
		//clean up url

		$url = $link[1];
		$url = str_replace('&amp;', '&', $url);
		$rawurl = $url;
		$url = clean_param($url, PARAM_URL);
		$urlstub = substr($rawurl,0,strpos($rawurl,'.' . $ext));
		
		if($ext=="youtube"){
			$filename = $link[1];
			$url="https://www.youtube.com/watch?v=" . $filename;
			$videourl="https://www.youtube.com/watch?v=" . $filename;
			$autojpgfilename ="hqdefault.jpg";
			$autopngfilename ="hqdefault.png";
			$autoposterurljpg  ="http://img.youtube.com/vi/" . $filename ."/hqdefault.jpg";
			$autoposterurlpng  ="http://img.youtube.com/vi/" . $filename ."/hqdefault.png";
			$filetitle="";
			$title="";
			$scheme='https:';
		}else{	
			//get the bits of the url
			$bits = parse_url($rawurl);
			if(!array_key_exists('scheme',$bits)){
				//add scheme to url if there was none
				if(strpos($PAGE->url->out(),'https:')===0){
					$scheme='https:';
				}else{
					$scheme='http:';
				}
			}else{
				$scheme = $bits['scheme'] . ':';
			}
	
			$filename = basename($bits['path']);
			$filetitle = str_replace('.' . $ext,'',$filename);
			$autopngfilename = str_replace('.' . $ext,'.png',$filename);
			$autojpgfilename = str_replace('.' . $ext,'.jpg',$filename);

			$videourl = $rawurl;
			$autoposterurljpg = $urlstub . '.jpg';
			$autoposterurlpng = $urlstub . '.png';
			$title = $link[4];
		}
		
		
		//fill out require js and require css full urls
		if(strpos($require_js,'//')===0){
			$require_js = $scheme . $require_js;
		}elseif(strpos($require_js,'/')===0){
			$require_js = $CFG->wwwroot . $require_js;
		}
	
		if(strpos($require_css,'//')===0){
			$require_css = $scheme . $require_css;
		}elseif(strpos($require_css,'/')===0){
			$require_css = $CFG->wwwroot . $require_css;
		}
	
		//get template body and defaults 
		//at some point template body was called "templatepreset_", and it has never changed. sorry :(
		$templatebody=$conf->{'templatepreset_' . $templateid};
		$defaults=$conf->{'templatedefaults_' . $templateid};
		//make sure we have all the keys and defaults in our proparray
		$proparray = filter_videoeasy_fetch_emptyproparray();
		$proparray = array_merge($proparray,filter_videoeasy_parsepropstring($defaults));
	
		//Add any params from url
		if(!empty($link[3])){
			//drop the first char if it is ?, whch it probably is
			$paramstring =  ltrim ($link[3], '?');
			$paramstring = str_replace('&amp;', '&', $paramstring);
			$params = array();
			parse_str($paramstring, $params);
			$proparray = array_merge($proparray,$params);
		}else{
			$paramstring="";
		}
	
		//use default widths or explicit width/heights if they were passed in ie http://url.to.video.mp4?d=640x480
		if(isset($proparray['d'])){
			$dimensions = explode('x',$proparray['d']);
			if(count($dimensions)==2){
				list($proparray['WIDTH'],$proparray['HEIGHT'])=$dimensions;
			}
		} 

	
		//I liked this better, but jquery was odd about it.
		//$autoid = $urlstub . '_' . time() . (string)rand(100,32767) ;
		$autoid = 'fv_' . time() . (string)mt_rand() ;
	
		//get default poster
		//get uploaded js
		if($defaultposterimage){
			$defaultposterurl = filter_videoeasy_internal_file_url($defaultposterimage,'defaultposterimage');
		}else{
			$defaultposterurl = $CFG->wwwroot . '/filter/videoeasy/defaultposter.jpg';
		}
		

		//make up mime type
		switch ($ext){
			case 'mp3': $automime='audio/mpeg';break;
			case 'webm': $automime='video/webm';break;
			case 'ogg': $automime='video/ogg';break;	
			case 'mp4': 
			case 'youtube': 
			default:
				$automime='video/mp4';
		}
	
		$proparray['AUTOMIME'] = $automime;
		$proparray['URLSTUB'] = $urlstub;
		$proparray['FILENAME'] = $filename;
		$proparray['FILETITLE'] = $filetitle;
		$proparray['DEFAULTPOSTERURL'] = $defaultposterurl;
		$proparray['AUTOPNGFILENAME'] = $autopngfilename;
		$proparray['AUTOJPGFILENAME'] = $autojpgfilename;
		$proparray['VIDEOURL'] = $videourl;
		$proparray['RAWVIDEOURL'] =  !empty($paramstring) ? $videourl + '?' + $paramstring : $videourl;
		$proparray['RAWURLPARAMS'] = $paramstring;
		$proparray['AUTOPOSTERURLJPG'] = $autoposterurljpg;
		$proparray['AUTOPOSTERURLPNG'] = $autoposterurlpng;
		$proparray['TITLE'] = $title;
		$proparray['AUTOID'] = $autoid;
		$proparray['FILEEXT'] = $ext;
		//Add courseid and CourseContextId if necessary
		if($COURSE && isset($COURSE->id)){
			$proparray['COURSEID'] = $COURSE->id;
			$context = context_course::instance($COURSE->id);
			$proparray['COURSECONTEXTID'] = $context->id;
		}
		
		
		
		
	
		//might need this if cant load into header, but need to.
		//$scripttag="<script src='@@REQUIREJS@@'></script>";
	
		/*
		foreach($proparray as $key=>$value){
			echo $key . ': ' . $value . '<br />';
		}
		*/
		//replace the specified names with spec values
		foreach($proparray as $name=>$value){
			$templatebody = str_replace('@@' . $name .'@@',$value,$templatebody);
		}
		//error_log($templatebody);

		//load jquery
		if(!$require_amd){
			if($require_jquery){
				if(!$PAGE->headerprinted && !$PAGE->requires->is_head_done()){
				//if(false){
					$PAGE->requires->jquery();
				}else{
					$PAGE->requires->js(new moodle_url($scheme . $conf->{'jqueryurl'}));
				}
			}

			//get any required js
			if($require_js){
				$PAGE->requires->js(new moodle_url($require_js));
			}
		
			//get uploaded js
			if($uploadjsfile){
				$uploadjsurl = filter_videoeasy_internal_file_url($uploadjsfile,'uploadjs_' . $templateid);
				$PAGE->requires->js($uploadjsurl);
			}
		}
		
		//load css in header if not too late
		//if not too late: load css in header
		// if too late: inject it there via JS
		if($uploadcssfile){
			$uploadcssurl = filter_videoeasy_internal_file_url($uploadcssfile,'uploadcss_' . $templateid);
		}
	
	
		//prepare additional params our JS will use
		$proparray['TEMPLATEID'] = $templateid;
		$proparray['CSSLINK']=false;
		$proparray['CSSUPLOAD']=false;
		$proparray['CSSCUSTOM']=false;
	
		//require any styles from the template
		$customcssurl=false;
		$customstyle=$conf->{'templatestyle_' . $templateid};
		if($customstyle){
			$customcssurl =new moodle_url( '/filter/videoeasy/videoeasycss.php?t=' . $templateid);

		}

			
		if(!$PAGE->headerprinted && !$PAGE->requires->is_head_done()){
			if($require_css){
				$PAGE->requires->css( new moodle_url($require_css));
			}
			if($uploadcssfile){
				$PAGE->requires->css($uploadcssurl);
			}
			if($customcssurl){
				$PAGE->requires->css($customcssurl);
			}
		}else{
			if($require_css){
				$proparray['CSSLINK']=$require_css;
			}
			if($uploadcssfile){
				$proparray['CSSUPLOAD']=$uploadcssurl->out();
			}
			if($customcssurl){
				$proparray['CSSCUSTOM']=$customcssurl->out();
			}
		
		}

	
		//initialise our JS and call it to load
		//We need this so that we can require the JSON , for json stringify
			$jsmodule = array(
				'name'     => 'filter_videoeasy',
				'fullpath' => '/filter/videoeasy/module.js',
				'requires' => array('json')
			);
		
		
		
		//AMD or not, and then load our js for this template on the page
		if($require_amd){
			$generator = new filter_videoeasy_template_script_generator($templateid,$ext);
			$template_amd_script = $generator->get_template_script();

			//props can't be passed in at much length , Moodle complains about too many
			//so we do this ... lets hope it don't break things
			$jsonstring = json_encode($proparray);
			$props_html = \html_writer::tag('input', '', array('id' => 'filter_videoeasy_amdopts_' . $proparray['AUTOID'], 'type' => 'hidden', 'value' => $jsonstring));
			$templatebody = $props_html . $templatebody;

			//load define for this template. Later it will be called from loadgenerico
			$PAGE->requires->js_amd_inline($template_amd_script);
			
			//for AMD
			$PAGE->requires->js_call_amd('filter_videoeasy/videoeasy_amd','loadvideoeasy', array(array('AUTOID'=>$proparray['AUTOID'])));


		}else{		
			//require any scripts from the template
			$PAGE->requires->js('/filter/videoeasy/videoeasyjs.php?ext=' . $ext . '&t=' . $templateid);
		
			//for no AMD
			$PAGE->requires->js_init_call('M.filter_videoeasy.loadvideoeasy', array($proparray),false,$jsmodule);
		}

		//return our expanded template

		return $templatebody;
	}

   
}//end of class