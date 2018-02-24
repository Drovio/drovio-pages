<?php
//#section#[header]
// Module Declaration
$moduleID = 184;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\JSONServerReport;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\DOMParser;

$dbc = new interDbConnection();
$dbq = new dbQuery("1469641106", "security.privileges.user");
$attr = array();
$attr['gid'] = $_GET['userGroup'];
$result = $dbc->execute($dbq, $attr);

$userGroupModules = array();
$userGroupModules['gid'] = $_GET['userGroup'];
while ($gModule = $dbc->fetch($result))
	$userGroupModules[$gModule['id']] = $gModule['id'];
	
JSONServerReport::clear();
JSONServerReport::addContent($userGroupModules);
return JSONServerReport::get();
//#section_end#
?>