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
 * A drawer based layout for the Eskada theme.
 *
 * @package    theme_moove
 * @copyright  2025 Willian Mano - willianmanoaraujo@gmail.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}

$themesettings = new \theme_moove52\util\settings();
if (!$themesettings->enablecourseindex) {
    $courseindex = '';
} else {
    $courseindex = core_course_drawer();
}

if (!$courseindex) {
    $courseindexopen = false;
}

$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $secondary = $PAGE->secondarynav;

    if ($secondary->get_children_key_list()) {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        $secondarynavigation = $moremenu->export_for_template($OUTPUT);
        $extraclasses[] = 'has-secondarynavigation';
    }

    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);

$iscoursedetailpage = !empty($PAGE->course) && !empty($PAGE->course->id) &&
    $PAGE->course->id != SITEID &&
    ($PAGE->context->contextlevel === CONTEXT_COURSE ||
    str_starts_with($PAGE->pagetype, 'course-view-') ||
    $PAGE->pagetype === 'course-view');

$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions();
// If the settings menu will be included in the header then don't add it here.
if ($iscoursedetailpage) {
    $regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
} else {
    $regionmainsettingsmenu = $buildregionmainsettings && !$PAGE->has_secondary_navigation() ?
        $OUTPUT->region_main_settings_menu() : false;
}

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$courseheading = '';
if (!empty($PAGE->course->fullname)) {
    $courseheading = format_string($PAGE->course->fullname, true, ['context' => $PAGE->context]);
}

$courseediturl = '';
if ($iscoursedetailpage && !empty($PAGE->course->id)) {
    $courseediturl = (new moodle_url('/course/edit.php', ['id' => $PAGE->course->id]))->out(false);
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$breadcrumbitems = [];
if ($PAGE->navbar && $PAGE->navbar->get_items()) {
    foreach ($PAGE->navbar->get_items() as $item) {
        $text = $item->text instanceof \lang_string ? $item->text->out() : $item->text;
        $url = null;

        if ($item->has_action()) {
            if ($item->action instanceof \moodle_url) {
                $url = $item->action->out();
            } else if ($item->action instanceof \action_link) {
                $url = $item->action->url->out();
            }
        }

        $breadcrumbitems[] = [
            'text' => $text,
            'url' => $url,
            'islast' => $item->is_last(),
        ];
    }
}

$hasbreadcrumb = !empty($breadcrumbitems);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => \core\context\course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu) ? true : false,
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'courseheading' => $courseheading,
    'courseediturl' => $courseediturl,
    'breadcrumbitems' => $breadcrumbitems,
    'hasbreadcrumb' => $hasbreadcrumb,
    'iscoursedetailpage' => $iscoursedetailpage,
    'addblockbutton' => $addblockbutton,
    'is_site_admin_search_page' => $PAGE->pagetype === 'admin-search' ? true: false,
    'is_breadcrumb' => $hasbreadcrumb,
    'is_site_admin_page' => str_starts_with($PAGE->pagetype, 'admin-'),
    'pagetype' => $PAGE->pagetype,
];

$themesettings = new \theme_moove52\util\settings();

$templatecontext = array_merge($templatecontext, $themesettings->footer());

echo $OUTPUT->render_from_template('theme_moove52/drawers', $templatecontext);
