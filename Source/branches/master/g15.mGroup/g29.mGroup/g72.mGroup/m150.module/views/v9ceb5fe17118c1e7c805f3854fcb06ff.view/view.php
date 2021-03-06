<?php
//#section#[header]
// Module Declaration
$moduleID = 150;

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


// Get All Languages
$dbc = new interDbConnection();
$q = new dbQuery("988046250", "resources.geoloc.languages");
$attr = array();
$attr['id'] = $_GET['lid'];
$attr['basic'] = ($_GET['type'] == "off" ? 0 : 1);
$result = $dbc->execute_query($q, $attr);
//#section_end#
?>