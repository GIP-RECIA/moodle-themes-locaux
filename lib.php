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
define("THEME_ESCO_CACHE_LIFETIME", 86400);

define('BLOCK_MYOVERVIEW_ROLES_VIEW', 'roles');

function theme_esco_etablissement(){
    global $PAGE, $DB;

    switch(get_class($PAGE->context)){
        case "context_course" :
            $course = $DB->get_record('course', array("id" => $PAGE->context->instanceid), '*', MUST_EXIST);
            $category_id = $course->category;
            break;
        case "context_coursecat" :
            $category_id = $PAGE->context->instanceid;
            break;
        default :
            return theme_esco_user_etablissement();
    }

    if($category_id == 0){
        return theme_esco_user_etablissement();
    }

    $category = $DB->get_record('course_categories', array("id" => $category_id), '*', MUST_EXIST);
    $cache_key =  "category_" . $category->id;

    $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'theme_esco', 'etablissements');
    $etablissement_cache = $cache->get($cache_key);


    if(!$etablissement_cache || $etablissement_cache['created'] > time() - THEME_ESCO_CACHE_LIFETIME){
        $ldap_config = theme_esco_ldap_config();
        $ldap_connection = ldap_connect($ldap_config["host_url"]);
        ldap_bind($ldap_connection, $ldap_config["bind_dn"], $ldap_config["bind_pw"]);

        $results = ldap_search($ldap_connection, $ldap_config["branch"], sprintf("(ENTStructureSIREN=%s)",$category->idnumber));
        $results = ldap_get_entries($ldap_connection, $results);

        $etablissement = theme_esco_process_ldap_result($results, array("id" => $category->idnumber,"name" => $category->name));

        ldap_close($ldap_connection);

        $cache->set($cache_key, array("data" => $etablissement, 'created' => time()));
    }else{
        $etablissement = $etablissement_cache["data"];
    }

    return $etablissement;
}


function theme_esco_user_etablissement(){
    global $USER;
    if(empty($USER->profile['etablissementuai'])){
       return null;
    }

    $cache_key = "etablissement_" . $USER->profile['etablissementuai'];

    $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'theme_esco', 'etablissements');
    $etablissement_cache = $cache->get($cache_key);

    if(!$etablissement_cache || $etablissement_cache['created'] > time() - THEME_ESCO_CACHE_LIFETIME){
        $ldap_config = theme_esco_ldap_config();
        $ldap_connection = ldap_connect($ldap_config["host_url"]);
        ldap_bind($ldap_connection, $ldap_config["bind_dn"], $ldap_config["bind_pw"]);

        $results = ldap_search($ldap_connection, $ldap_config["branch"], sprintf("(ENTStructureUAI=%s)",$USER->profile['etablissementuai']));
        $results = ldap_get_entries($ldap_connection, $results);

        $etablissement = theme_esco_process_ldap_result($results, array("id" => $USER->profile['etablissementuai'],"name" => ""));

        ldap_close($ldap_connection);

        $cache->set($cache_key, array("data" => $etablissement, 'created' => time()));
    }else{
        $etablissement = $etablissement_cache["data"];
    }

    return $etablissement;

}

function theme_esco_process_ldap_result($ldap_entries, $default = array()){
    if(isset($ldap_entries[0])){
        $etablissement = new stdClass();
        $etablissement->ou = "";
        $etablissement->entstructurenomcourant = "";
        $etablissement->escostructurenomcourt = "";
        $etablissement->escodomaines = "";
        $etablissement->entstructureuai = "";
        $etablissement->entetablissementministeretutelle = "";

        foreach($ldap_entries[0] as $key => $value){
            if(property_exists($etablissement,$key)){
                if($value["count"] > 1){
                    $etablissement->$key = array();
                    foreach($value as $k => $v){
                        if($k === "count"){
                            continue;
                        }
                        $etablissement->$key[$k] = $v;
                    }
                }else{
                    $etablissement->$key = $value[0];
                }
            }
        }
        if(empty($etablissement->escostructurenomcourt)){
            $etablissement->escostructurenomcourt = $etablissement->entstructurenomcourant;
        }
	$etablissement->escostructurenomcourt = str_replace(‘$’, ‘ ‘, $etablissement->escostructurenomcourt);
        if(!is_array($etablissement->escodomaines)){
            $etablissement->escodomaines = array($etablissement->escodomaines);
        }
    }else{
        $etablissement = new stdClass();
        $etablissement->ou = $default["name"];
        $etablissement->entstructureuai = null;
        $etablissement->entetablissementministeretutelle = null;
        $etablissement->entstructurenomcourant = $default["name"];
        $etablissement->escostructurenomcourt = $default["name"];
        $etablissement->escodomaines = array();
    }

    $etablissement->id = $default["id"];

    return $etablissement;
}

/**
 * Retrieve the color of the theme based on data of the given etablissement
 */
function theme_esco_etablissement_color($etablissement){
    if(is_null($etablissement)){
        return "netocentre";
    }
    if(in_array("www.touraine-eschool.fr", $etablissement->escodomaines)){
        return "touraine";
    }
    if(in_array("colleges41.fr", $etablissement->escodomaines)){
        return "colleges41";
    }
    if(strtolower($etablissement->entetablissementministeretutelle) === "agriculture"){
        return "agricol";
    }
    return "netocentre";
}

function theme_esco_etablissement_logo($etablissement){
    global $OUTPUT;
    $color = theme_esco_etablissement_color($etablissement);
    switch($color){
        default :
            return $OUTPUT->image_url('NOCMoodle_blanc', "theme");
        case "touraine" :
            return $OUTPUT->image_url('TESMoodle_blanc', "theme");
        case "colleges41" :
            return $OUTPUT->image_url('C41Moodle_blanc', "theme");
    }
}

/**
 * Retrieve LDAP Configuration from SimpleSCO configuration
 * @return array
 */
function theme_esco_ldap_config(){
    global $DB;
    $configs = $DB->get_records('config_plugins', array("plugin" => "enrol_simplesco"));
    $config = array(
        "host_url" => "",
        "bind_dn" => "",
        "bind_pw" => "",
        "branch" => "",
    );

    foreach($configs as $tmp){
        if(isset($config[$tmp->name])){
            $config[$tmp->name] = $tmp->value;
        }
    }

    $config["branch"] = str_ireplace("people","structures",$config["branch"]);
    return $config;
}

/**
 * Returns the name of the user preferences as well as the details this plugin uses.
 *
 * @return array
 */
function theme_esco_user_preferences() {
    $preferences = array();
    $preferences['block_myoverview_sort_field'] = array(
        'type' => PARAM_ALPHA,
        'null' => NULL_ALLOWED,
        'choices' => array("fullname","shortname","id","timecreated"),
    );

    $preferences['block_myoverview_sort_order'] = array(
        'type' => PARAM_ALPHA,
        'null' => NULL_NOT_ALLOWED,
        'default' => "asc",
        'choices' => array("asc","desc"),
    );

    $preferences['block_myoverview_display_mode'] = array(
        'type' => PARAM_ALPHA,
        'null' => NULL_NOT_ALLOWED,
        'default' => "card",
        'choices' => array("card","list"),
    );

    return $preferences;
}
