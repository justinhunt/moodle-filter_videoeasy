/**
 * Javascript for loading swf widgets , espec flowplayer for PoodLL
 *
 * @copyright &copy; 2012 Justin Hunt
 * @author poodllsupport@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package filter_poodll
 */

M.filter_videoeasy = {

	allopts: Array(),
	
	csslink: false,
	
	gyui: null,
	
	injectcss: function(csslink){
		var link = document.createElement("link");
		link.href = csslink;
		link.type = "text/css";
		link.rel = "stylesheet";
		document.getElementsByTagName("head")[0].appendChild(link);	
	},
	
	// Replace poodll_flowplayer divs with flowplayers
	loadvideoeasy: function(Y,opts) {
		//stash our Y and opts for later use
		this.gyui = Y;
		this.allopts.push(opts);
			
		//load our css in head
		if(opts['CSSLINK']){
			if (!this.csslink){
				this.csslink=true;
				this.injectcss(opts['CSSLINK']);
			}
		}
		filter_videoeasy_doscripts();		
	}//end of function
}//end of class