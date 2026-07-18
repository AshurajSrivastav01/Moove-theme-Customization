<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'kspc_Kenelearn';
$CFG->dbuser    = 'kspc_kspcken';
$CFG->dbpass    = '8f5-zZg-TP4-3NJ';
$CFG->prefix    = 'mdl_';
$CFG->contactemail = 'support@example.com';
$CFG->contactphone = '+91 9876543210';


$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
//   'dbcollation' => 'utf8_general_ci',
  'dbcollation' => 'utf8mb4_unicode_ci',
);

// $CFG->wwwroot   = 'https://consistent-turquoise-porpoise.91-204-209-39.cpanel.site';
$CFG->wwwroot   = 'https://consistent-turquoise-porpoise.91-204-209-39.cpanel.site';
$CFG->dataroot  = '/home/kspc/moodledata';
// $CFG->dirroot   = '/home/kspc/public_html/public';
$CFG->admin     = 'admin';
$CFG->directorypermissions = 0777;

// $CFG->debug=true;
// @error_reporting(E_ALL | E_STRICT);
// @ini_set('display_errors', '1');
// @ini_set('log_errors', '1');
// $CFG->debug = (E_ALL | E_STRICT);
// $CFG->debugdisplay = 1;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!