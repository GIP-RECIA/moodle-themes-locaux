<?php
/**
 * Created by PhpStorm.
 * User: pierrelejeune
 * Date: 18/06/18
 * Time: 08:54
 */

namespace theme_esco\output;

use core_course\external\course_summary_exporter;

class block_myoverview_renderer extends \block_myoverview\output\renderer
{

    /**
     * @var array
     */
    private $coursesprogress;

    public function render_main(\block_myoverview\output\main $main)
    {

        global $USER;
        $view_data = $main->export_for_template($this);

        if ($main->tab === "roles") {
            $view_data["viewingtimeline"] = false;
            $view_data["viewingcourses"] = false;
            $view_data["viewingroles"] = true;
        } else {
            $view_data["viewingroles"] = false;
        }

        $courses = enrol_get_my_courses('*'); //'timecreated' necessaire pour pouvoir trier les cours par date
        $courses = $this->addRoles($USER->id, $courses);

        $view_data["rolesview"]["roles"] = $this->getDistinctRoles($courses);
        $this->retrieveCourseProgress($courses);

        $this->updateViewData($view_data, $courses);

        return $this->render_from_template('block_myoverview/main', $view_data);
    }

    /**
     * Retrieve for each $courses the role in the DB
     * @param $uid
     * @param $courses
     * @return mixed
     */
    private function addRoles($uid, $courses)
    {
        global $DB;

        if (!empty ($courses)) {
            $sql = "SELECT c.instanceid AS courseid, GROUP_CONCAT(r.shortname SEPARATOR ',') AS roles
	    		FROM {role} r
	    		JOIN {role_assignments} ra ON ra.roleid = r.id
	    		JOIN {context} c ON c.id = ra.contextid
	    		WHERE ra.userid = ?
	    		AND c.instanceid in (";

            $params = array(
                $uid
            );
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
        global $CFG;
        $courseid = $course->id;
        $context = \context_course::instance($courseid);
        $exporter = new course_summary_exporter($course, [
            'context' => $context
        ]);
        $exportedcourse = $exporter->export($this);
        $exportedcourse->summary = content_to_text($exportedcourse->summary, $exportedcourse->summaryformat);

        $course = new \course_in_list($course);
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                    $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                $exportedcourse->courseimage = $url;
                $exportedcourse->classes = 'courseimage';
                break;
            }
        }

        $exportedcourse->color = $this->coursecolor($course->id);

        if (!isset($exportedcourse->courseimage)) {
            $pattern = new \core_geopattern();
            $pattern->setColor($exportedcourse->color);
            $pattern->patternbyid($courseid);
            $exportedcourse->classes = 'coursepattern';
            $exportedcourse->courseimage = $pattern->datauri();
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

            $percentage = \core_completion\progress::progress::get_course_progress_percentage($course);
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