<?php

/*
 * @author    Shaun Daubney
 * @package   theme_netocentre_rwd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Basic Heading
    $name = 'theme_netocentrerwd/basicheading';
    $heading = get_string('basicheading', 'theme_netocentrerwd');
    $information = get_string('basicheadingdesc', 'theme_netocentrerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);
	
	// Logo file setting
	$name = 'theme_netocentrerwd/logo';
	$title = get_string('logo','theme_netocentrerwd');
	$description = get_string('logodesc', 'theme_netocentrerwd');
	$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
	$settings->add($setting);	

	// Hide Menu
	$name = 'theme_netocentrerwd/hidemenu';
	$title = get_string('hidemenu','theme_netocentrerwd');
	$description = get_string('hidemenudesc', 'theme_netocentrerwd');
	$default = 1;
	$choices = array(1=>get_string('yes',''), 0=>get_string('no',''));
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$settings->add($setting);

	// Email url setting
	$name = 'theme_netocentrerwd/emailurl';
	$title = get_string('emailurl','theme_netocentrerwd');
	$description = get_string('emailurldesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Custom CSS file
	$name = 'theme_netocentrerwd/customcss';
	$title = get_string('customcss','theme_netocentrerwd');
	$description = get_string('customcssdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtextarea($name, $title, $description, $default);
	$setting->set_updatedcallback('theme_reset_all_caches');
	$settings->add($setting);

	// Frontpage Heading
    $name = 'theme_netocentrerwd/frontpageheading';
    $heading = get_string('frontpageheading', 'theme_netocentrerwd');
    $information = get_string('frontpageheadingdesc', 'theme_netocentrerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);

	// Title Date setting
	$name = 'theme_netocentrerwd/titledate';
	$title = get_string('titledate','theme_netocentrerwd');
	$description = get_string('titledatedesc', 'theme_netocentrerwd');
	$default = 1;
	$choices = array(1=>get_string('yes',''), 0=>get_string('no',''));
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$settings->add($setting);

	// General Alert setting
	$name = 'theme_netocentrerwd/generalalert';
	$title = get_string('generalalert','theme_netocentrerwd');
	$description = get_string('generalalertdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Snow Alert setting
	$name = 'theme_netocentrerwd/snowalert';
	$title = get_string('snowalert','theme_netocentrerwd');
	$description = get_string('snowalertdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

    // Colour Heading
    $name = 'theme_netocentrerwd/colourheading';
    $heading = get_string('colourheading', 'theme_netocentrerwd');
    $information = get_string('colourheadingdesc', 'theme_netocentrerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);
	
	// Background colour setting
	$name = 'theme_netocentrerwd/backcolor';
	$title = get_string('backcolor','theme_netocentrerwd');
	$description = get_string('backcolordesc', 'theme_netocentrerwd');
	$default = '#fafafa';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);

	// Graphic Wrap (Background Image)
	$name = 'theme_netocentrerwd/backimage';
	$title=get_string('backimage','theme_netocentrerwd');
	$description = get_string('backimagedesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
	$settings->add($setting);

	// Graphic Wrap (Background Position)
	$name = 'theme_netocentrerwd/backposition';
	$title = get_string('backposition','theme_netocentrerwd');
	$description = get_string('backpositiondesc', 'theme_netocentrerwd');
	$default = 'no-repeat';
	$choices = array('no-repeat'=>get_string('backpositioncentred','theme_netocentrerwd'), 'no-repeat fixed'=>get_string('backpositionfixed','theme_netocentrerwd'), 'repeat'=>get_string('backpositiontiled','theme_netocentrerwd'), 'repeat-x'=>get_string('backpositionrepeat','theme_netocentrerwd'));
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$settings->add($setting);

	// Menu hover background colour setting
	$name = 'theme_netocentrerwd/menuhovercolor';
	$title = get_string('menuhovercolor','theme_netocentrerwd');
	$description = get_string('menuhovercolordesc', 'theme_netocentrerwd');
	$default = '#f42941';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);	
	
	// Footer Options Heading
    $name = 'theme_netocentrerwd/footeroptheading';
    $heading = get_string('footeroptheading', 'theme_netocentrerwd');
    $information = get_string('footeroptdesc', 'theme_netocentrerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);
	
	// Copyright setting
	$name = 'theme_netocentrerwd/copyright';
	$title = get_string('copyright','theme_netocentrerwd');
	$description = get_string('copyrightdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// CEOP
	$name = 'theme_netocentrerwd/ceop';
	$title = get_string('ceop','theme_netocentrerwd');
	$description = get_string('ceopdesc', 'theme_netocentrerwd');
	$default = '';
	$choices = array(''=>get_string('ceopnone','theme_netocentrerwd'), 'http://www.thinkuknow.org.au/site/report.asp'=>get_string('ceopaus','theme_netocentrerwd'), 'http://www.ceop.police.uk/report-abuse/'=>get_string('ceopuk','theme_netocentrerwd'));
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$settings->add($setting);

	// Disclaimer setting
	$name = 'theme_netocentrerwd/disclaimer';
	$title = get_string('disclaimer','theme_netocentrerwd');
	$description = get_string('disclaimerdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
	$settings->add($setting);	

	// Social Icons Heading
    $name = 'theme_netocentrerwd/socialiconsheading';
    $heading = get_string('socialiconsheading', 'theme_netocentrerwd');
    $information = get_string('socialiconsheadingdesc', 'theme_netocentrerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);
	
	// Website url setting
	$name = 'theme_netocentrerwd/website';
	$title = get_string('website','theme_netocentrerwd');
	$description = get_string('websitedesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Facebook url setting
	$name = 'theme_netocentrerwd/facebook';
	$title = get_string('facebook','theme_netocentrerwd');
	$description = get_string('facebookdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Twitter url setting
	$name = 'theme_netocentrerwd/twitter';
	$title = get_string('twitter','theme_netocentrerwd');
	$description = get_string('twitterdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Google+ url setting
	$name = 'theme_netocentrerwd/googleplus';
	$title = get_string('googleplus','theme_netocentrerwd');
	$description = get_string('googleplusdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Flickr url setting
	$name = 'theme_netocentrerwd/flickr';
	$title = get_string('flickr','theme_netocentrerwd');
	$description = get_string('flickrdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Pinterest url setting
	$name = 'theme_netocentrerwd/pinterest';
	$title = get_string('pinterest','theme_netocentrerwd');
	$description = get_string('pinterestdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Instagram url setting
	$name = 'theme_netocentrerwd/instagram';
	$title = get_string('instagram','theme_netocentrerwd');
	$description = get_string('instagramdesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// LinkedIn url setting
	$name = 'theme_netocentrerwd/linkedin';
	$title = get_string('linkedin','theme_netocentrerwd');
	$description = get_string('linkedindesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);
	
	// Wikipedia url setting
	$name = 'theme_netocentrerwd/wikipedia';
	$title = get_string('wikipedia','theme_netocentrerwd');
	$description = get_string('wikipediadesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// YouTube url setting
	$name = 'theme_netocentrerwd/youtube';
	$title = get_string('youtube','theme_netocentrerwd');
	$description = get_string('youtubedesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Apple url setting
	$name = 'theme_netocentrerwd/apple';
	$title = get_string('apple','theme_netocentrerwd');
	$description = get_string('appledesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Android url setting
	$name = 'theme_netocentrerwd/android';
	$title = get_string('android','theme_netocentrerwd');
	$description = get_string('androiddesc', 'theme_netocentrerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

}

