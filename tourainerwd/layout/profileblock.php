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

/*
 * @author    Shaun Daubney
 * @package   theme_aardvark
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

	function get_content () {
	global $USER, $CFG, $SESSION, $COURSE;
	$wwwroot = '';
	$signup = '';}

	if (empty($CFG->loginhttps)) {
		$wwwroot = $CFG->wwwroot;
	} else {
		$wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
	}

	if (!isloggedin() or isguestuser()) {
	   echo '<form class="navbar-form pull-left" method="post" action="'.$wwwroot.'/login/index.php?authldap_skipntlmsso=1">';
	   echo '<input class="span2" type="text" name="username" placeholder="'.get_string('username').'">';
	   echo '<input class="span2" type="password" name="password" placeholder="'.get_string('password').'">';
	   echo '<button class="btn" type="submit"> '.get_string('login').'</button>';
	   echo '</form>';
	} else { 

	echo '<ul class="nav">

<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="#cm_submenu_5"><img class="profilepic" src="'.$CFG->wwwroot.'/user/pix.php?file=/'.$USER->id.'/f1.jpg" width="80px" height="80px" title="'.$USER->firstname.' '.$USER->lastname.'" alt="'.$USER->firstname.' '.$USER->lastname.'" />
'.$USER->firstname.'
<b class="caret"></b>
</a>
<ul class="dropdown-menu profiledrop">';
/* Modification Recia - Ce menu disparait dans les versions plus récentes d'aardvark et est remplacé par
la fonction de création de menu prévue par moodle.

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/my">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/course', 'theme').'" />';
echo get_string('mycourses');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/user/profile.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/profile', 'theme').'" />';
echo get_string('viewprofile');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/user/edit.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/edit', 'theme').'" />';
echo get_string('editmyprofile');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/user/files.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/files', 'theme').'" />';
echo get_string('myfiles');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/calendar/view.php?view=month">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/calendar', 'theme').'" />';
echo get_string('calendar','calendar');
echo '</a>';
echo '</li>';

if ($hasemailurl) {
echo '<li>';
echo '<a href="'.$PAGE->theme->settings->emailurl.'">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/email', 'theme').'" />';
echo get_string('email','theme_aardvark');
echo '</a>';
echo '</li>';
}

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/login/logout.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/logout', 'theme').'" />';
echo get_string('logout');
echo '</a>';
echo '</li>';
*/

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/my">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/course', 'theme').'" />';
echo get_string('myhome');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<hr class="hr_menu"/>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/calendar/view.php?view=month">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/calendar', 'theme').'" />';
echo get_string('calendar','calendar');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/user/profile.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/profile', 'theme').'" />';
echo get_string('viewprofile');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/user/preferences.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/profile', 'theme').'" />';
echo get_string('preferences');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/message/index.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/email', 'theme').'" />';
echo get_string('messages','message');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/badges/mybadges.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('t/award', 'core').'" />';
echo get_string('mybadges','badges');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<a href="'.$CFG->wwwroot.'/user/files.php">';
echo '<img class="profileicon" src="'.$OUTPUT->pix_url('profile/files', 'theme').'" />';
echo get_string('myfiles');
echo '</a>';
echo '</li>';

echo '<li>';
echo '<hr class="hr_menu"/>';
echo '</li>';

$addlangmenu = true;
$langs = get_string_manager()->get_list_of_translations();
if (count($langs) < 2
	/*or empty($CFG->langmenu) */
   or ($this->page->course != SITEID and !empty($this->page->course->lang))) {
   $addlangmenu = false;
}
		        
/*		        if (!$menu->has_children() && $addlangmenu === false) {
					            return '';
}    */
		            
if ($addlangmenu) {
   $strlang =  get_string('language');
   $currentlang = current_language();
   if (isset($langs[$currentlang])) {
      $currentlang = $langs[$currentlang];
   } else {
      $currentlang = $strlang;
   }   
   echo '<li class="dropdown langmenu">';
   echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="' . $currentlang . '">' . $currentlang . '<b class="caret"></b></a>';
   echo '<ul class="dropdown-menu">';
   foreach ($langs as $langtype => $langname) {
	   echo '<li>';
	   $url_lang = new moodle_url('', array('lang' => $langtype));
       echo '<a title="'. $langname . '" href="'. $url_lang . '">' . $langname . '</a>';
	   echo '</li>';
  }
   echo '</ul>';
   echo '</li>';
}   

/* Fin de modifications Recia */

echo '</ul></li></ul>';

}?>
