<?php

/*
 * @author    Shaun Daubney
 * @package   theme_netocentre_rwd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Basic Heading
    $name = 'theme_tourainerwd/basicheading';
    $heading = get_string('basicheading', 'theme_tourainerwd');
    $information = get_string('basicheadingdesc', 'theme_tourainerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);
	
	// Logo file setting
	$name = 'theme_tourainerwd/logo';
	$title = get_string('logo','theme_tourainerwd');
	$description = get_string('logodesc', 'theme_tourainerwd');
	$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
	$settings->add($setting);	

	// Hide Menu
	$name = 'theme_tourainerwd/hidemenu';
	$title = get_string('hidemenu','theme_tourainerwd');
	$description = get_string('hidemenudesc', 'theme_tourainerwd');
	$default = 1;
	$choices = array(1=>get_string('yes',''), 0=>get_string('no',''));
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$settings->add($setting);

	// Email url setting
	$name = 'theme_tourainerwd/emailurl';
	$title = get_string('emailurl','theme_tourainerwd');
	$description = get_string('emailurldesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Custom CSS file
	$name = 'theme_tourainerwd/customcss';
	$title = get_string('customcss','theme_tourainerwd');
	$description = get_string('customcssdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtextarea($name, $title, $description, $default);
	$setting->set_updatedcallback('theme_reset_all_caches');
	$settings->add($setting);

	// Frontpage Heading
    $name = 'theme_tourainerwd/frontpageheading';
    $heading = get_string('frontpageheading', 'theme_tourainerwd');
    $information = get_string('frontpageheadingdesc', 'theme_tourainerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);

	// Title Date setting
	$name = 'theme_tourainerwd/titledate';
	$title = get_string('titledate','theme_tourainerwd');
	$description = get_string('titledatedesc', 'theme_tourainerwd');
	$default = 1;
	$choices = array(1=>get_string('yes',''), 0=>get_string('no',''));
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$settings->add($setting);

	// General Alert setting
	$name = 'theme_tourainerwd/generalalert';
	$title = get_string('generalalert','theme_tourainerwd');
	$description = get_string('generalalertdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Snow Alert setting
	$name = 'theme_tourainerwd/snowalert';
	$title = get_string('snowalert','theme_tourainerwd');
	$description = get_string('snowalertdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

    // Colour Heading
    $name = 'theme_tourainerwd/colourheading';
    $heading = get_string('colourheading', 'theme_tourainerwd');
    $information = get_string('colourheadingdesc', 'theme_tourainerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);
	
	// Background colour setting
	$name = 'theme_tourainerwd/backcolor';
	$title = get_string('backcolor','theme_tourainerwd');
	$description = get_string('backcolordesc', 'theme_tourainerwd');
	$default = '#fafafa';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);

	// Graphic Wrap (Background Image)
	$name = 'theme_tourainerwd/backimage';
	$title=get_string('backimage','theme_tourainerwd');
	$description = get_string('backimagedesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
	$settings->add($setting);

	// Graphic Wrap (Background Position)
	$name = 'theme_tourainerwd/backposition';
	$title = get_string('backposition','theme_tourainerwd');
	$description = get_string('backpositiondesc', 'theme_tourainerwd');
	$default = 'no-repeat';
	$choices = array('no-repeat'=>get_string('backpositioncentred','theme_tourainerwd'), 'no-repeat fixed'=>get_string('backpositionfixed','theme_tourainerwd'), 'repeat'=>get_string('backpositiontiled','theme_tourainerwd'), 'repeat-x'=>get_string('backpositionrepeat','theme_tourainerwd'));
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$settings->add($setting);

	// Menu hover background colour setting
	$name = 'theme_tourainerwd/menuhovercolor';
	$title = get_string('menuhovercolor','theme_tourainerwd');
	$description = get_string('menuhovercolordesc', 'theme_tourainerwd');
	$default = '#f42941';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);	
	
	// Footer Options Heading
    $name = 'theme_tourainerwd/footeroptheading';
    $heading = get_string('footeroptheading', 'theme_tourainerwd');
    $information = get_string('footeroptdesc', 'theme_tourainerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);
	
	// Copyright setting
	$name = 'theme_tourainerwd/copyright';
	$title = get_string('copyright','theme_tourainerwd');
	$description = get_string('copyrightdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// CEOP
	$name = 'theme_tourainerwd/ceop';
	$title = get_string('ceop','theme_tourainerwd');
	$description = get_string('ceopdesc', 'theme_tourainerwd');
	$default = '';
	$choices = array(''=>get_string('ceopnone','theme_tourainerwd'), 'http://www.thinkuknow.org.au/site/report.asp'=>get_string('ceopaus','theme_tourainerwd'), 'http://www.ceop.police.uk/report-abuse/'=>get_string('ceopuk','theme_tourainerwd'));
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$settings->add($setting);

	// Disclaimer setting
	$name = 'theme_tourainerwd/disclaimer';
	$title = get_string('disclaimer','theme_tourainerwd');
	$description = get_string('disclaimerdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
	$settings->add($setting);	

	// Social Icons Heading
    $name = 'theme_tourainerwd/socialiconsheading';
    $heading = get_string('socialiconsheading', 'theme_tourainerwd');
    $information = get_string('socialiconsheadingdesc', 'theme_tourainerwd');
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings->add($setting);
	
	// Website url setting
	$name = 'theme_tourainerwd/website';
	$title = get_string('website','theme_tourainerwd');
	$description = get_string('websitedesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Facebook url setting
	$name = 'theme_tourainerwd/facebook';
	$title = get_string('facebook','theme_tourainerwd');
	$description = get_string('facebookdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Twitter url setting
	$name = 'theme_tourainerwd/twitter';
	$title = get_string('twitter','theme_tourainerwd');
	$description = get_string('twitterdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Google+ url setting
	$name = 'theme_tourainerwd/googleplus';
	$title = get_string('googleplus','theme_tourainerwd');
	$description = get_string('googleplusdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Flickr url setting
	$name = 'theme_tourainerwd/flickr';
	$title = get_string('flickr','theme_tourainerwd');
	$description = get_string('flickrdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Pinterest url setting
	$name = 'theme_tourainerwd/pinterest';
	$title = get_string('pinterest','theme_tourainerwd');
	$description = get_string('pinterestdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Instagram url setting
	$name = 'theme_tourainerwd/instagram';
	$title = get_string('instagram','theme_tourainerwd');
	$description = get_string('instagramdesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// LinkedIn url setting
	$name = 'theme_tourainerwd/linkedin';
	$title = get_string('linkedin','theme_tourainerwd');
	$description = get_string('linkedindesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);
	
	// Wikipedia url setting
	$name = 'theme_tourainerwd/wikipedia';
	$title = get_string('wikipedia','theme_tourainerwd');
	$description = get_string('wikipediadesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// YouTube url setting
	$name = 'theme_tourainerwd/youtube';
	$title = get_string('youtube','theme_tourainerwd');
	$description = get_string('youtubedesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Apple url setting
	$name = 'theme_tourainerwd/apple';
	$title = get_string('apple','theme_tourainerwd');
	$description = get_string('appledesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

	// Android url setting
	$name = 'theme_tourainerwd/android';
	$title = get_string('android','theme_tourainerwd');
	$description = get_string('androiddesc', 'theme_tourainerwd');
	$default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$settings->add($setting);

}

