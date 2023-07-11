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

define("THEME_ESCO_CACHE_LIFETIME", 86400);

/**
 * Retourne la liste des domaines de l'utilisateur sous forme de tableau.
 * Cette méthode est nécessaire temporairement pour faire fonctionner le système de refresh de session.
 * En effet, pour faire appel à ce système, il est nécessaire de connaître le domaine de l'api de l'utilisateur.
 * La plupart des utilisateurs n'auront qu'un domaine retourné.
 *
 * @return array<string> La liste des domaines de l'utilisateur, par exemple : ["ent.recia.fr", "www.chercan.fr"]
 */
function domains_user() {
    global $USER, $DB;

    // FIXME: a supprimer
    return ["lycees.netocentre.fr", "ent.recia.fr"];

    // On essaye d'aller récupérer l'information dans le cache
    $cache_key = "user_" . $USER->username;
    $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'theme_esco', 'users_domaines');
    $user_domains_cache = $cache->get($cache_key);

    // Si l'information est présente on la retourne
    if ($user_domains_cache && $user_domains_cache['created'] < time() + THEME_ESCO_CACHE_LIFETIME){
        return $user_domains_cache["data"];
    }

    // Sinon on va la chercher dans le ldap
    $ldap_config = theme_esco_ldap_config();
    $branch = explode(',', $ldap_config["branch"]);
    $branch[0] = "ou=people";
    $ldap_connection = ldap_connect($ldap_config["host_url"]);
    ldap_bind($ldap_connection, $ldap_config["bind_dn"], $ldap_config["bind_pw"]);
    $results = ldap_search($ldap_connection, implode(',', $branch), sprintf("(uid=%s)",$USER->username));
    $results = ldap_get_entries($ldap_connection, $results);
    $domains = [];

    // On récupère tous les domaines
    if(isset($results[0])){
        foreach($results[0]['escodomaines'] as $key => $value){
            if ($key !== "count") {
                $domains[] = $value;
            }
        }
    }

    // On remplit le cache avec les domaines
    ldap_close($ldap_connection);
    $cache->set($cache_key, array("data" => $domains, 'created' => time()));

    // Et on retourne la liste des domaines
    return $domains;
}

/**
 * Retourne l'établissement à utiliser pour le thème en fonction du contexte
 *
 * @return stdClass|null L'établissement sous forme d'un objet stdClass ou null
 */
function theme_esco_etablissement() {
    global $PAGE, $DB;

    // On regarde le contexte
    switch(get_class($PAGE->context)){
        // Dans le contexte d'un cours, on récupère la catégorie du cours
        case "context_course" :
            $course = $DB->get_record('course', array("id" => $PAGE->context->instanceid), '*', MUST_EXIST);
            $category_id = $course->category;
            break;
        // Dans le contexte d'une catégorie, on récupère la catégorie
        case "context_coursecat" :
            $category_id = $PAGE->context->instanceid;
            break;
        // Dans les autres contextes, on retourne directement le thème de l'utilisateur
        default :
            return theme_esco_user_etablissement();
    }

    // Si la catégorie est 0, on retourne le thème de l'utilisateur
    if($category_id == 0){
        return theme_esco_user_etablissement();
    }

    // On essaye de récupérer le cache lié à cette catégorie
    $category = $DB->get_record('course_categories', array("id" => $category_id), '*', MUST_EXIST);
    $cache_key =  "category_" . $category->id;
    $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'theme_esco', 'etablissements');
    $etablissement_cache = $cache->get($cache_key);

    // Si le cache est absent ou invalide
    if(!$etablissement_cache || $etablissement_cache['created'] > time() - THEME_ESCO_CACHE_LIFETIME) {
        // FIXME: a supprimer
        $name = $category->name;
        $etablissement = new stdClass();
        $etablissement->ou = $name;
        $etablissement->entstructureuai = null;
        $etablissement->entetablissementministeretutelle = null;
        $etablissement->entstructurenomcourant = $name;
        $etablissement->escostructurenomcourt = $name;
        $etablissement->escodomaines = ["lycees.netocentre.fr"];
        $etablissement->id = $category->idnumber;

        // FIXME: a décommenter
        /*// Connexion au ldap
        $ldap_config = theme_esco_ldap_config();
        $ldap_connection = ldap_connect($ldap_config["host_url"]);
        ldap_bind($ldap_connection, $ldap_config["bind_dn"], $ldap_config["bind_pw"]);

        // Récupération de la structure ayant un ENTStructureSIREN qui correspond au category id
        $results = ldap_search($ldap_connection, $ldap_config["branch"], sprintf("(ENTStructureSIREN=%s)", $category->idnumber));
        $results = ldap_get_entries($ldap_connection, $results);

        // On récupère les données du ldap et on les traite pour en faire un objet établissement
        $etablissement = theme_esco_process_ldap_result($results, $category->idnumber, $category->name);

        ldap_close($ldap_connection);*/

        // On remplit le cache pour les appels ultérieurs
        $cache->set($cache_key, array("data" => $etablissement, 'created' => time()));
    // Sinon, le cache est valide, on l'affecte a la valeur de retour
    }else{
        $etablissement = $etablissement_cache["data"];
    }

    // On retourne l'objet établissement
    return $etablissement;
}

/**
 * Retourne l'établissement à utiliser pour le thème en fonction du contexte
 *
 * @return stdClass|null L'établissement à utiliser sous forme d'un objet stdClass ou null
 */
function theme_esco_user_etablissement() {
    global $USER;

    // Si l'utilisateur n'a pas d'etablissementuai on retourne null
    if (empty($USER->profile['etablissementuai'])) {
       return null;
    }

    // On essaye de récupérer le cache lié à cette etablissementuai
    $cache_key = "etablissement_" . $USER->profile['etablissementuai'];
    $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'theme_esco', 'etablissements');
    $etablissement_cache = $cache->get($cache_key);

    // Si le cache est absent ou invalide
    if (!$etablissement_cache || $etablissement_cache['created'] > time() - THEME_ESCO_CACHE_LIFETIME) {
        // FIXME: a supprimer
        $name = "";
        $etablissement = new stdClass();
        $etablissement->ou = $name;
        $etablissement->entstructureuai = null;
        $etablissement->entetablissementministeretutelle = null;
        $etablissement->entstructurenomcourant = $name;
        $etablissement->escostructurenomcourt = $name;
        $etablissement->escodomaines = ["lycees.netocentre.fr"];
        $etablissement->id = $USER->profile['etablissementuai'];

        // FIXME: a décommenter
        /*// Connexion au ldap
        $ldap_config = theme_esco_ldap_config();
        $ldap_connection = ldap_connect($ldap_config["host_url"]);
        ldap_bind($ldap_connection, $ldap_config["bind_dn"], $ldap_config["bind_pw"]);

        // Récupération de la structure ayant un ENTStructureUAI qui correspond au etablissementuai de l'utilisateur
        $results = ldap_search($ldap_connection, $ldap_config["branch"], sprintf("(ENTStructureUAI=%s)",$USER->profile['etablissementuai']));
        $results = ldap_get_entries($ldap_connection, $results);

        // On récupère les données du ldap et on les traite pour en faire un objet établissement
        $etablissement = theme_esco_process_ldap_result($results, $USER->profile['etablissementuai']);

        ldap_close($ldap_connection);*/

        // On remplit le cache pour les appels ultérieurs
        $cache->set($cache_key, array("data" => $etablissement, 'created' => time()));
    // Sinon, le cache est valide, on l'affecte a la valeur de retour
    }else{
        $etablissement = $etablissement_cache["data"];
    }

    // On retourne l'objet établissement
    return $etablissement;

}

/**
 * Créé un objet établissement avec le résultat de la requéte ldap et en le complétant avec les champs name et id
 * 
 * @param mixed[]   $ldap_entries   Les résultats de la requête ldap
 * @param string    $id             L'identifiant de l'établissement
 * @param string    $name           Le nom de l'établissement si l'on n'a pas eu de résultat par ldap
 */
function theme_esco_process_ldap_result($ldap_entries, $id, $name = "") { 
    // Si la requête a au moins un résultat, on créé l'objet en fonction des données récupérées
    if(isset($ldap_entries[0])) {
        $etablissement = new stdClass();
        $etablissement->ou = "";
        $etablissement->entstructurenomcourant = "";
        $etablissement->escostructurenomcourt = "";
        $etablissement->escodomaines = "";
        $etablissement->entstructureuai = "";
        $etablissement->entetablissementministeretutelle = "";

        foreach ($ldap_entries[0] as $key => $value) {
            if (property_exists($etablissement,$key)) {
                if ($value["count"] > 1) { 
                    $etablissement->$key = array();

                    foreach ($value as $k => $v) {
                        if ($k === "count") {
                            continue;
                        }

                        $etablissement->$key[$k] = $v;
                    }
                } else {
                    $etablissement->$key = $value[0];
                }
            }
        }

        if (empty($etablissement->escostructurenomcourt)) {
            $etablissement->escostructurenomcourt = $etablissement->entstructurenomcourant;
        }

        if( !is_array($etablissement->escodomaines)) {
            $etablissement->escodomaines = array($etablissement->escodomaines);
        }
    // Sinon on créé l'objet avec des données vides
    }else{
        $etablissement = new stdClass();
        $etablissement->ou = $name;
        $etablissement->entstructureuai = null;
        $etablissement->entetablissementministeretutelle = null;
        $etablissement->entstructurenomcourant = $name;
        $etablissement->escostructurenomcourt = $name;
        $etablissement->escodomaines = array();
    }

    // On affecte son id
    $etablissement->id = $id;

    return $etablissement;
}

/**
 * Retourne le nom du thème à utiliser en fonction du ou des domaines de l'établissement
 * C'est grâce a ce nom de thème que l'on connaîtra les couleurs a utiliser
 *
 * @param stdClass|null Les données de l'établissement ou null
 *
 * @return string Le nom du thème à utiliser
 */
function theme_esco_etablissement_color($etablissement) {
    // Si l'établissement n'est pas null, on trouve le thème qui correspond a son domaine et on le retourne
    if (!is_null($etablissement)) {
        if (in_array("www.touraine-eschool.fr", $etablissement->escodomaines)) {
            return "touraine";
        }
    
        if (in_array("colleges41.fr", $etablissement->escodomaines) || in_array("ent.colleges41.fr", $etablissement->escodomaines)) {
            return "colleges41";
        }
    
        if (in_array("www.chercan.fr", $etablissement->escodomaines)) {
            return "chercan";
        }
    
        if (in_array("e-college.indre.fr", $etablissement->escodomaines)) {
            return "colleges36";
        }
    
        if (in_array("mon-e-college.loiret.fr", $etablissement->escodomaines)) {
            return "colleges45";
        }
    
        if (in_array("www.colleges-eureliens.fr", $etablissement->escodomaines)) {
            return "colleges28";
        }
    
        if (strtolower($etablissement->entetablissementministeretutelle) === "agriculture") {
            return "agricol";
        }
    }

    // Sinon, ou si on a un domaine qui correspond a netocentre, on retourne le thème par défaut qui est netocentre
    return "netocentre";
}

/**
 * Charge le config ldap a partir des données de configuration du plugin enrol_simplesco
 *
 * @return array<string> La config du ldap
 */
function theme_esco_ldap_config() {
    global $DB;

    $configs = $DB->get_records('config_plugins', array("plugin" => "enrol_simplesco"));
    $config = array(
        "host_url" => "",
        "bind_dn" => "",
        "bind_pw" => "",
        "branch" => "",
    );

    foreach ($configs as $tmp) {
        if (isset($config[$tmp->name])) {
            $config[$tmp->name] = $tmp->value;
        }
    }

    $config["branch"] = str_ireplace("people","structures",$config["branch"]);

    return $config;
}

/**
 * Ajoute le scss supplémentaire du thème à la fin du scss du thème boost
 */
function theme_esco_get_extra_scss($theme) { 
    return file_get_contents(__DIR__ . '/scss/extra.scss');                                                           
}