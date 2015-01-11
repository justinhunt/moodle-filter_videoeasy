/**
 * Javascript for loading swf widgets , espec flowplayer for PoodLL
 *
 * @copyright &copy; 2012 Justin Hunt
 * @author poodllsupport@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package filter_poodll
 */

M.filter_videoeasy = {

	allopts: {},
	
	extscripts: {},
	
	csslinks: Array(),
	
	gyui: null,
	
	injectcss: function(csslink){
		var link = document.createElement("link");
		link.href = csslink;
		if(csslink.toLowerCase().lastIndexOf('.html')==csslink.length-5){
			link.rel = 'import';
		}else{
			link.type = "text/css";
			link.rel = "stylesheet";	
		}
		document.getElementsByTagName("head")[0].appendChild(link);	
	},
	
	// Replace poodll_flowplayer divs with flowplayers
	loadvideoeasy: function(Y,opts) {
		//stash our Y and opts for later use
		this.gyui = Y;
		
		//load our css in head if required
		//only do it once per file though
		if(opts['CSSLINK']){
			if (this.csslinks.indexOf(opts['CSSLINK'])<0){
				this.csslinks.push(opts['CSSLINK']);
				this.injectcss(opts['CSSLINK']);
			}
		}
		//load our css in head if required
		//only do it once per extension though
		if(opts['CSSUPLOAD']){
			if (this.csslinks.indexOf(opts['CSSUPLOAD'])<0){
				this.csslinks.push(opts['CSSUPLOAD']);
				this.injectcss(opts['CSSUPLOAD']);
			}
		}
		
		//load our css in head if required
		//only do it once per extension though
		if(opts['CSSCUSTOM']){
			if (this.csslinks.indexOf(opts['CSSCUSTOM'])<0){
				this.csslinks.push(opts['CSSCUSTOM']);
				this.injectcss(opts['CSSCUSTOM']);
			}
		}
		
		
		/*
		if(!this.allopts[opts['FILEEXT']]){this.allopts[opts['FILEEXT']]=Array();}		
		this.allopts[opts['FILEEXT']].push(opts);
		console.log(this.allopts);
		filter_videoeasy_doscripts(opts);
		*/
		
		if(typeof filter_videoeasy_extfunctions != 'undefined'){ 
			if(typeof filter_videoeasy_extfunctions[opts['FILEEXT']] == 'function'){ 
				filter_videoeasy_extfunctions[opts['FILEEXT']](opts);
			}
		}
	}//end of function
}//end of class