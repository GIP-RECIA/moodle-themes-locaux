<?php

defined('MOODLE_INTERNAL') || die;

class theme_esco_core_renderer extends \theme_boost\output\core_renderer {
    /**
     * Returns HTML attributes to use within the body tag. This includes an ID and classes.
     *
     * On vient ajouter le theme et des domaines de l'utilisateur pour connaitre le ou les domaines a utiliser pour joindre l'api du portail
     *
     * @since Moodle 2.5.1 2.6
     * @param string|array $additionalclasses Any additional classes to give the body tag,
     * @return string
     */
    public function body_attributes($additionalclasses = array()) {
        $additionalclasses[] = "theme-" . theme_esco_etablissement_color(theme_esco_etablissement());
        $res = parent::body_attributes($additionalclasses);
        $res .= ' data-domains="' . implode(',', domains_user()) . '"';

        return  $res;
    }
}