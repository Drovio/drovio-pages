<?php
//#section#[header]
// Module Declaration
$moduleID = 146;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;


// Set Active Locale
$dbc = new interDbConnection();

if ($_GET['type'] == "on")
	$q = new dbQuery("603059917", "resources.geoloc.locale");
else
	$q = new dbQuery("996151671", "resources.geoloc.locale");

$attr = array();
$attr['locale'] = $_GET['lid'];
$result = $dbc->execute_query($q, $attr);
//#section_end#
?>