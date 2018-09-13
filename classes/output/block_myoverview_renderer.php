<?php
/**
 * Created by PhpStorm.
 * User: pierrelejeune
 * Date: 18/06/18
 * Time: 08:54
 */

namespace theme_esco\output;

use block_myoverview\output\courses_view;
use core_course\external\course_summary_exporter;
use core_completion\progress;

require_once($CFG->dirroot . '/blocks/myoverview/lib.php');
require_once($CFG->libdir . '/completionlib.php');

class block_myoverview_renderer extends \block_myoverview\output\renderer
{

    const HIDDEN_ROLES = array("extendedteacher");

    /**
     * @var array
     */
    private $coursesprogress;

    public function render_main(\block_myoverview\output\main $main)
    {
        global $USER;
        $view_data = $this->export_for_template();
        $this->handleDisplay();

        $view_data["viewingtimeline"] = false;
        $view_data["viewingcourses"] = false;
        $view_data["viewingroles"] = false;

        $view_data["display_card_mode"] = get_user_preferences("block_myoverview_display_mode", "card") == "card";
        $view_data["display_list_mode"] = get_user_preferences("block_myoverview_display_mode", "card") == "list";

        $view_data["sort"] = "default";

        switch ($main->tab) {
            case BLOCK_MYOVERVIEW_TIMELINE_VIEW:
                $view_data["viewingtimeline"] = true;
                break;
            case BLOCK_MYOVERVIEW_COURSES_VIEW:
                $view_data["viewingcourses"] = true;
                break;
            case BLOCK_MYOVERVIEW_ROLES_VIEW:
                $view_data["viewingroles"] = true;
                set_user_preference('block_myoverview_last_tab', BLOCK_MYOVERVIEW_ROLES_VIEW);
                break;
        }

        $this->updateImages($view_data);

        $courses = enrol_get_my_courses('*', $this->getSort()); //'timecreated' necessaire pour pouvoir trier les cours par date
        $courses = $this->addRoles($USER->id, $courses);

        $view_data["rolesview"]["roles"] = $this->getDistinctRoles($courses);
        $this->retrieveCourseProgress($courses);

        $this->updateViewData($view_data, $courses);
        $this->getSortViewData($view_data);

        return $this->render_from_template('block_myoverview/main', $view_data);
    }

    private function export_for_template()
    {
        global $USER;

        $courses = enrol_get_my_courses('*', $this->getSort());
        $coursesprogress = [];

        foreach ($courses as $course) {

            $completion = new \completion_info($course);

            // First, let's make sure completion is enabled.
            if (!$completion->is_enabled()) {
                continue;
            }

            $percentage = progress::get_course_progress_percentage($course);
            if (!is_null($percentage)) {
                $percentage = floor($percentage);
            }

            $coursesprogress[$course->id]['completed'] = $completion->is_course_complete($USER->id);
            $coursesprogress[$course->id]['progress'] = $percentage;
        }

        $coursesview = new courses_view($courses, $coursesprogress);
        $nocoursesurl = $this->image_url('courses', 'block_myoverview')->out();
        $noeventsurl = $this->image_url('activities', 'block_myoverview')->out();

        return [
            'midnight' => usergetmidnight(time()),
            'coursesview' => $coursesview->export_for_template($this),
            'urls' => [
                'nocourses' => $nocoursesurl,
                'noevents' => $noeventsurl
            ]
        ];
    }

    private function handleDisplay(){

        $display_mode = optional_param('display_mode', null, PARAM_ALPHA);
        if (!is_null($display_mode)) {
            set_user_preference('block_myoverview_display_mode', $display_mode);
        }
    }

    private function getSort()
    {
        $sort = null;
        $sort_field = optional_param('sort_field', null, PARAM_ALPHA);
        if (is_null($sort_field)) {
            $sort_field = get_user_preferences('block_myoverview_sort_field');
        } else {
            set_user_preference('block_myoverview_sort_field', $sort_field);
        }
        if (is_null($sort_field)) return null;

        $sort_order = optional_param('sort_order', null, PARAM_ALPHA);
        if (is_null($sort_order)) {
            $sort_order = get_user_preferences('block_myoverview_sort_order');
        } else {
            set_user_preference('block_myoverview_sort_order', $sort_order);
        }
        if (is_null($sort_order)) {
            $sort_order = "ASC";
        }

        $sort = "$sort_field $sort_order";

        return $sort;
    }

    private function getSortViewData(&$viewdata)
    {
        $sort = $this->getSort();
        if (is_null($sort)) {
            $viewdata["sort_label"] = get_string("resortcourses");
            return;
        }
        list($field, $order) = explode(" ", $sort);
        $viewdata["sort_label"] = get_string(($order == "ASC" ? 'sortbyx' : 'sortbyxreverse'), 'moodle', get_string($field . "course"));
    }

    private function updateImages(array &$view_data)
    {
        global $OUTPUT;
        $tabs = array("past", "futur", "inprogress");
//        var_dump($view_data);
        foreach ($tabs as $tab) {
            if (!isset($view_data["coursesview"][$tab]) || !isset($view_data["coursesview"][$tab]['pages'])) continue;
            foreach ($view_data["coursesview"][$tab]['pages'] as $page => $d) {
                if (!isset($view_data["coursesview"][$tab]['pages'][$page]['courses'])) continue;
                foreach ($view_data["coursesview"][$tab]['pages'][$page]['courses'] as $key => $course) {
                    if (stripos($course->courseimage, "data:") === 0) {
                        $course->courseimage = $OUTPUT->image_url('Toque', 'theme');
                        $view_data["coursesview"][$tab]['pages'][$page]['courses'][$key] = $course;
                    }
                }
            }
        }
    }

    /**
     * Retrieve for each $courses the role in the DB
     * @param $uid
     * @param $courses
     * @return mixed
     * @throws \dml_exception A DML specific exception is thrown for any errors.
     */
    private function addRoles($uid, $courses)
    {
        global $DB;

        if (!empty ($courses)) {
            $params = array(
                $uid
            );

            $sql = "SELECT c.instanceid AS courseid, GROUP_CONCAT(r.shortname SEPARATOR ',') AS roles
	    		FROM {role} r
	    		JOIN {role_assignments} ra ON ra.roleid = r.id
	    		JOIN {context} c ON c.id = ra.contextid
	    		WHERE ra.userid = ? ";

            if (!empty(self::HIDDEN_ROLES)) {
                $sql .= "AND r.shortname NOT IN (";
                $first = true;
                foreach (self::HIDDEN_ROLES as $role) {
                    $sql .= ($first ? "" : ",") . "'$role'";
                    $first = false;
                }
                $sql .= ") ";
            }

            $sql .= "AND c.instanceid in (";

            foreach ($courses as $course) {
                $sql .= ",?";
                $params [] = $course->id;
            }

            // Un expression reguliere pour enlever une virgule (:/)
            $sql = preg_replace('/\(,/', '(', $sql, 1) . ") GROUP BY c.instanceid";

            $roleassignments = $DB->get_records_sql($sql, $params);
            foreach ($courses as $course) {
                $course->roles_esco = $roleassignments[$course->id]->roles;
            }
        }

        return $courses;
    }


    /**
     * Retrieve distinct roles for each $courses the role
     * @param $courses
     * @return array
     * @throws \dml_exception A DML specific exception is thrown for any errors.
     */
    private function getDistinctRoles($courses)
    {
        global $DB;
        $roles = array();

        foreach ($courses as $course) {
            if (property_exists($course, "roles_esco")) {
                $course_roles = explode(",", $course->roles_esco);
                foreach ($course_roles as $role) {
                    if (!isset($roles[$role])) {
                        if (is_null($data = $DB->get_record('role', array('shortname' => $role), 'name'))) {
                            $name = "";
                        } else {
                            $name = $data->name;
                        }
                        $tmp = new \stdClass();
                        $tmp->key = $role;
                        $tmp->value = $name;
                        $tmp->courses = array();
                        $roles[$role] = $tmp;
                    }
                }
            }
        }

        return $roles;
    }

    /**
     * Update the view data in order to filter courses
     * @param array $view_data
     * @param array $courses
     */
    private function updateViewData(array &$view_data, array $courses)
    {
        foreach ($courses as $course) {
            $view_data["rolesview"]["courses_all"][] = $this->convertToFrontView($course);
            $roles = explode(",", $course->roles_esco);
            foreach ($roles as $role) {
                $view_data["rolesview"]["roles"][$role]->courses[] = $this->convertToFrontView($course);
            }
        }
        sort($view_data["rolesview"]["roles"]);
    }

    /**
     * Convert course extract from DB into course used by vue
     * @param $course
     * @return mixed
     */
    private function convertToFrontView($course)
    {
        global $CFG, $OUTPUT;
        $courseid = $course->id;
        $context = \context_course::instance($courseid);
        $exporter = new course_summary_exporter($course, [
            'context' => $context
        ]);
        $exportedcourse = $exporter->export($this);
        $course_object = new \course_in_list($course);
        $exportedcourse->summary = content_to_text($exportedcourse->summary, $exportedcourse->summaryformat);

        $course = new \course_in_list($course);
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                    $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                $exportedcourse->courseimage = $url;
                $exportedcourse->classes = 'courseimage';
                break;
            }
        }

        $exportedcourse->enseignants = array();
        foreach ($course_object->get_course_contacts() as $uid => $course_contact) {
            if ($course_contact["role"]->shortname !== "editingteacher") {
                continue;
            }
            $exportedcourse->enseignants[] = $course_contact["user"];
        }
        $exportedcourse->hasenseignants = (count($exportedcourse->enseignants) > 0);

        $exportedcourse->color = $this->coursecolor($course->id);

        if (!isset($exportedcourse->courseimage)) {
            $exportedcourse->classes = 'coursepattern';
            $exportedcourse->courseimage = $OUTPUT->image_url('Toque', 'theme');
        }

        // Include course visibility.
        $exportedcourse->visible = (bool)$course->visible;

        $courseprogress = null;

        if (isset($this->coursesprogress[$courseid])) {
            $courseprogress = $this->coursesprogress[$courseid]['progress'];
            $exportedcourse->hasprogress = !is_null($courseprogress);
            $exportedcourse->progress = $courseprogress;
        }

        return $exportedcourse;
    }

    /**
     * Retrieve course progress
     * @param $courses
     */
    private function retrieveCourseProgress($courses)
    {
        global $USER;
        $coursesprogress = [];

        foreach ($courses as $course) {

            $completion = new \completion_info($course);

            // First, let's make sure completion is enabled.
            if (!$completion->is_enabled()) {
                continue;
            }

            $percentage = \core_completion\progress::get_course_progress_percentage($course);
            if (!is_null($percentage)) {
                $percentage = floor($percentage);
            }

            $coursesprogress[$course->id]['completed'] = $completion->is_course_complete($USER->id);
            $coursesprogress[$course->id]['progress'] = $percentage;
        }

        $this->coursesprogress = $coursesprogress;
    }

    /**
     * Generate a semi-random color based on the courseid number (so it will always return
     * the same color for a course)
     *
     * @param int $courseid
     * @return string $color, hexvalue color code.
     */
    protected function coursecolor($courseid)
    {
        $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];
        $color = $basecolors[$courseid % 10];
        return $color;
    }
}