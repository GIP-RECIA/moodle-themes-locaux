<?php
//  This file is part of Moodle - http://moodle.org
//
//  Moodle is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  Moodle is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// This file is part of The Bootstrap Moodle theme
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

namespace theme_esco\output\core_course\management;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/classes/management_renderer.php");

use html_writer;
use core_course_category;

/**
 * Main renderer for the course management pages.
 *
 * @package theme_boost
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \core_course_management_renderer {

    /**
     * Presents a course category listing.
     *
     * @param core_course_category $category The currently selected category. Also the category to highlight in the listing.
     * @return string
     */
    public function category_listing(core_course_category $category = null) {
        if ($category === null) {
            $selectedparents = array();
            $selectedcategory = null;
        } else {
            $selectedparents = $category->get_parents();
            $selectedparents[] = $category->id;
            $selectedcategory = $category->id;
        }
        $catatlevel = \core_course\management\helper::get_expanded_categories('');
        $catatlevel[] = array_shift($selectedparents);
        $catatlevel = array_unique($catatlevel);

        $listing = core_course_category::get(0)->get_children();

        $attributes = array(
            'class' => 'ml-1 list-unstyled',
            'role' => 'tree',
            'aria-labelledby' => 'category-listing-title'
        );

        $html  = html_writer::start_div('category-listing card w-100');
        $html .= html_writer::tag('h3', get_string('categories'),
            array('class' => 'card-header', 'id' => 'category-listing-title'));
        $html .= html_writer::start_div('card-body');
        $html .= $this->category_listing_actions($category);
        $html .= html_writer::start_tag('ul', $attributes);
        foreach ($listing as $listitem) {
            if(!is_siteadmin() && !$listitem->can_create_course() && !$listitem->has_manage_capability()) continue;
            // Render each category in the listing.
            $subcategories = array();
            if (in_array($listitem->id, $catatlevel)) {
                $subcategories = $listitem->get_children();
            }
            $html .= $this->category_listitem(
                $listitem,
                $subcategories,
                $listitem->get_children_count(),
                $selectedcategory,
                $selectedparents
            );
        }
        $html .= html_writer::end_tag('ul');
        $html .= $this->category_bulk_actions($category);
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        return $html;
    }
}
