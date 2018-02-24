<?php
//#section#[header]
// Module Declaration
$moduleID = 272;

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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Projects\projectLibrary;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Check and update application
	$appID = $_POST['id'];
	$lastAppVersion = projectLibrary::getLastProjectVersion($appID);
	$result = projectLibrary::setTeamProjectVersion($appID, $lastAppVersion);
	
	// Build content
	$pageContent = new MContent();
	
	// Return reload action
	$pageContent->addReportAction("application.update", $appID);
	return $pageContent->getReport();
}
//#section_end#
?>