<?php
//#section#[header]
// Module Declaration
$moduleID = 277;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "redbackStatistics", TRUE);

$dbc = new dbConnection();

// Get persons count
$q = module::getQuery($moduleID, "get_persons_count");
$result = $dbc->execute($q);
$row = $dbc->fetch($result);
$personsCount = $row['count'];

// Get total persons in the system
$attr = array();
$attr['count'] = $personsCount;
$title = moduleLiteral::get($moduleID, "lbl_totalPersons", $attr);
$hd = HTML::select("h1.persons")->item(0);
DOM::append($hd, $title);


// Get projects count
$q = module::getQuery($moduleID, "get_projects_count");
$result = $dbc->execute($q);
$row = $dbc->fetch($result);
$projectsCount = $row['count'];

// Get total projects in the system
$attr = array();
$attr['count'] = $projectsCount;
$title = moduleLiteral::get($moduleID, "lbl_totalProjects", $attr);
$hd = HTML::select("h1.projects")->item(0);
DOM::append($hd, $title);


// Return output
return $page->getReport();
//#section_end#
?>