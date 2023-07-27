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

    /**
     * Implémentation pour remplacer l'avatar par celui de l'ent
     *
     * @param user_picture $userpicture
     * @return string
     */
    protected function render_user_picture(\user_picture $userpicture) {
        $user = $userpicture->user;

        if ($userpicture->alttext) {
            if (!empty($user->imagealt)) {
                $alt = $user->imagealt;
            } else {
                $alt = get_string('pictureof', '', fullname($user));
            }
        } else {
            $alt = '';
        }

        if (empty($userpicture->size)) {
            $size = 35;
        } else if ($userpicture->size === true or $userpicture->size == 1) {
            $size = 100;
        } else {
            $size = $userpicture->size;
        }

        $class = $userpicture->class;

        if ($user->picture == 0) {
            $class .= ' defaultuserpic';
        }
        profile_load_custom_fields($userpicture->user);
        $src = $userpicture->get_url($this->page, $this);
        if(!empty($userpicture->user->profile["avatar"])){
            $src = $userpicture->user->profile["avatar"];
        }

        $attributes = array('src'=>$src, 'alt'=>$alt, 'title'=>$alt, 'class'=>$class, 'width'=>$size, 'height'=>$size);
        if (!$userpicture->visibletoscreenreaders) {
            $attributes['role'] = 'presentation';
        }

        // get the image html output fisrt
        $output = html_writer::empty_tag('img', $attributes);

        // Show fullname together with the picture when desired.
        if ($userpicture->includefullname) {
            $output .= fullname($userpicture->user);
        }

        // then wrap it in link if needed
        if (!$userpicture->link) {
            return $output;
        }

        if (empty($userpicture->courseid)) {
            $courseid = $this->page->course->id;
        } else {
            $courseid = $userpicture->courseid;
        }

        if ($courseid == SITEID) {
            $url = new moodle_url('/user/profile.php', array('id' => $user->id));
        } else {
            $url = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $courseid));
        }

        $attributes = array('href'=>$url);
        if (!$userpicture->visibletoscreenreaders) {
            $attributes['tabindex'] = '-1';
            $attributes['aria-hidden'] = 'true';
        }

        if ($userpicture->popup) {
            $id = html_writer::random_id('userpicture');
            $attributes['id'] = $id;
            $this->add_action_handler(new popup_action('click', $url), $id);
        }

        return html_writer::tag('a', $output, $attributes);
    }
}