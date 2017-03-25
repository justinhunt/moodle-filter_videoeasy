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
 * Returns the JS for a specified player
 * Its php but looks to browser like js file, cos that is what it returns.
 *
 * @package    filter_videoeasy
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//define('AJAX_SCRIPT', true);
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$ext = required_param('ext',PARAM_TEXT);
$templateid = optional_param('t','',PARAM_TEXT);

$conf = get_config('filter_videoeasy');
if(empty($templateid)){
	$templateid=$conf->{'useplayer' . $ext};
}

$generator = new \filter_videoeasy\template_script_generator($templateid,$ext);
$template_script = $generator->get_template_script();
header('Content-Type: application/javascript');
echo $template_script;
