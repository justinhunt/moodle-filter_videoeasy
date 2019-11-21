<?php
/**
 * Created by PhpStorm.
 * User: justin
 * Date: 17/03/24
 * Time: 16:53
 */

namespace filter_videoeasy;


class videoeasy_utils
{
    const FILTER_VIDEOEASY_TEMPLATE_COUNT = 15;

    /**
     * Return an array of template ids. A legacy of when each had a hard coded name really.
     * But we still need an unchanging id for each template, and this is it
     * @return array of template ids
     */
    public static function fetch_players($conf){

        $players = array();

        //Add the template pages
        if($conf && property_exists($conf,'templatecount')){
            $templatecount = $conf->templatecount;
        }else{
            $templatecount =  self::FILTER_VIDEOEASY_TEMPLATE_COUNT;
        }

        for ($i=1;$i<=$templatecount;$i++){
            $players[]=$i;
        }
        return $players;
    }

    /**
     * Return an array of OLD player names. For mapping to template indexes, so early adopters
     * don't get errors and lose templates etc
     * @return array of player names
     */
    public static function fetch_oldplayers(){
        $oldplayers = array('videojs','jplayervideo','jwplayer','flowplayer','mediaelement','playersix','playerseven','playereight','playernine','playerten','playereleven','playertwelve','playerthirteen','playerfourteen','playerfifteen');
        $players = array();
        for ($i=1;$i<=self::FILTER_VIDEOEASY_TEMPLATE_COUNT;$i++){
            $players[$i]=$oldplayers[$i-1];
        }
        return $players;
    }

    /**
     * Fetch the old value of the old style template name, and for use as a default
     * when upgrading. If no old value, just the new defaults
     * @return string default value
     */
    public static function fetch_default($conf, $oldpropname, $newdefault){
        if($conf && property_exists($conf,$oldpropname)){
            $defvalue=$conf->{$oldpropname} ;
        }else{
            $defvalue=$newdefault;
        }
        return $defvalue;
    }

    /**
     * Return an array of extensions we might handle
     * @return array of variable names parsed from template string
     */
    public static function fetch_extensions($adminconfig = false){
        if(!$adminconfig){
            $adminconfig = get_config('filter_videoeasy');
        }
        $default_extensions = self::fetch_default_extensions();
        $have_custom_extensions = $adminconfig && isset($adminconfig->extensions) && !empty($adminconfig->extensions);
        if( $have_custom_extensions){
            $custom_extensions= preg_replace("/[^A-Za-z0-9,]/", '', $adminconfig->extensions);
            return explode(',',$custom_extensions);
        }else{
            return $default_extensions;
        }
    }

    /**
     * Return an array of extensions we might handle
     * @return array of variable names parsed from template string
     */
    public static function fetch_default_extensions(){
        return array('mp4','webm','ogg','ogv','flv','mp3','rss','youtube');
    }

    /**
     * Return an array of properties, empty, to ensure we have all the variables when we need them
     * @return array of properties
     */
    public static function fetch_emptyproparray(){
        $proparray=array();
        $proparray['AUTOMIME'] = '';
        $proparray['FILENAME'] = '';
        $proparray['FILENAMESTUB'] = '';
        $proparray['FILETITLE'] = '';
        $proparray['AUTOPNGFILENAME'] = '';
        $proparray['AUTOJPGFILENAME'] = '';
        $proparray['VIDEOURL'] = '';
        $proparray['RAWVIDEOURL'] = '';
        $proparray['RAWPARAMS'] = '';
        $proparray['URLSTUB'] = '';
        $proparray['AUTOPOSTERURLJPG'] = '';
        $proparray['AUTOPOSTERURLPNG'] = '';
        $proparray['DEFAULTPOSTERURL'] = '';
        $proparray['TITLE'] = '';
        $proparray['AUTOID'] = '';
        $proparray['CSSLINK'] = '';
        $proparray['CSSUPLOAD'] = '';
        $proparray['TEMPLATEID'] = '';
        $proparray['WIDTH'] = '';
        $proparray['HEIGHT'] = '';
        $proparray['FILEEXT'] = '';
        $proparray['COURSEID'] = '';
        $proparray['COURSECONTEXTID'] = '';
        return $proparray;
    }

    /**
     * Takes a string of prop="value",prop="value" and returns a nice array of prop/values
     * @param string a defaults string. prop="value",prop="value"
     * @return array of prop/values
     */
    public static function parsepropstring($rawproperties){
        //Now we just have our properties string
        //Lets run our regular expression over them
        //string should be property=value,property=value
        //got this regexp from http://stackoverflow.com/questions/168171/regular-expression-for-parsing-name-value-pairs
        $regexpression='/([^=,]*)=("[^"]*"|[^,"]*)/';
        $matches;

        //here we match the filter string and split into name array (matches[1]) and value array (matches[2])
        //we then add those to a name value array.
        $itemprops = array();
        if(!$rawproperties ||$rawproperties==''){return $itemprops;}

        if (preg_match_all($regexpression, $rawproperties,$matches,PREG_PATTERN_ORDER)){
            $propscount = count($matches[1]);
            for ($cnt =0; $cnt < $propscount; $cnt++){
                // echo $matches[1][$cnt] . "=" . $matches[2][$cnt] . " ";
                $newvalue = $matches[2][$cnt];
                //this could be done better, I am sure. WE are removing the quotes from start and end
                //this wil however remove multiple quotes id they exist at start and end. NG really
                $newvalue = trim($newvalue,'"');
                $itemprops[$matches[1][$cnt]]=$newvalue;
            }
        }
        return $itemprops;
    }



    /**
     * Returns URL to the videoeasyjs or videoeasycss php files, or the defaulposter image
     *
     *
     * @param string $filepath
     * @param string $filearea
     * @return string protocol relative URL or null if not present
     */
    public static function internal_file_url($filepath, $filearea) {
        global $CFG;


        $component = 'filter_videoeasy';
        $itemid = 0;
        $syscontext = \context_system::instance();

        $url = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/$syscontext->id/$component/$filearea/$itemid".$filepath);
        return $url;
    }

    /**
     * Serves either the videoeasyjs or videoeasycss php files, or the defaulposter image
     *
     * theme revision is used instead of the itemid.
     *
     * @param string $filearea
     * @param array $args the bits that come after the itemid in the url
     * @param boolean $forcedownload passed straight in from above
     * @param array $options passed straight in from above
     * @return string protocol relative URL or null if not present
     */
    public static function internal_file_serve($filearea, $args, $forcedownload, $options) {
        global $CFG;
        require_once("$CFG->libdir/filelib.php");

        $syscontext = \context_system::instance();
        $component = 'filter_videoeasy';

        $revision = array_shift($args);
        if ($revision < 0) {
            $lifetime = 0;
        } else {
            $lifetime = 60*60*24*60;
        }

        $fs = get_file_storage();
        $relativepath = implode('/', $args);

        $fullpath = "/{$syscontext->id}/{$component}/{$filearea}/0/{$relativepath}";
        $fullpath = rtrim($fullpath, '/');
        if ($file = $fs->get_file_by_hash(sha1($fullpath))) {
            send_stored_file($file, $lifetime, 0, $forcedownload, $options);
            return true;
        } else {
            send_file_not_found();
        }
    }
}