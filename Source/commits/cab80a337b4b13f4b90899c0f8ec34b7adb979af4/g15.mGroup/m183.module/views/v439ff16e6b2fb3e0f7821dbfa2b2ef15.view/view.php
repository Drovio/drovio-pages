<?php
//#section#[header]
// Module Declaration
$moduleID = 183;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("UI", "Html");
importer::import("UI", "Html");
importer::import("INU", "Developer");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;

use \UI\Html\HTMLModulePage;
use \INU\Developer\vcs\repositoryOverviewer;

// Create page
$page = new HTMLModulePage();
$page->build("Repository Overview");

// Get project information
$dbc = new interDbConnection();
$dbq = new dbQuery("587007210", "developer");
$attr = array();
$attr['id'] = $_GET['id'];
$result = $dbc->execute($dbq, $attr);
if (!$result)
{
	$error = DOM::create("h2", "There is an error getting project information");
	$page->append($error);
	
	return $page->getReport();
}

$project = $dbc->fetch($result);
$repViewer = new repositoryOverviewer("developerProjectOverview", $project['repository']);
$control = $repViewer->build($project['title'])->get();
$page->append($control);

return $page->getReport();
//#section_end#
?>