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
 * Theme ESCO
 *
 * @package    theme_esco
 * @copyright  GIP Récia - Pierre LEJEUNE
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'esco';
$THEME->doctype = 'html5';
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->parents = ['boost'];

$THEME->scss = function() {
    $parentconfig = theme_config::load('boost');
    $scss = theme_boost_get_main_scss_content($parentconfig);
    $scss .= file_get_contents(__DIR__ . '/scss/default.scss');
    return $scss;
};

$THEME->enable_dock = false;
$THEME->usefallback = true;

$THEME->yuicssmodules = array();

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->requiredblocks = 'navigation,settings';
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;

if (core_useragent::is_ie() && !core_useragent::check_ie_version('9.0')) {
    $THEME->javascripts[] = 'html5shiv';
}
//$THEME->javascripts_footer[] = 'table';

$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_DEFAULT;

$THEME->javascripts_footer = array(
    'moodlebootstrap',
);

if($CFG->allowuserthemes = 1){
    set_config('allowuserthemes',0);
}

if($CFG->allowcategorythemes = 1){
    set_config('allowcategorythemes',0);
}