<?php
//#section#[header]
// Module Declaration
$moduleID = 271;

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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("BSS", "Dashboard");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \BSS\Dashboard\appLibrary;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Validate form
	if (!simpleForm::validate())
		return FALSE;
	
	// Get application and version to buy
	$appID = $_POST['id'];
	$appVersion = $_POST['version'];
	
	// Update team application
	$status = appLibrary::setTeamAppVersion($appID, $appVersion, $teamID = "");
	
	// Build content
	$pageContent->build("", "appUpdaterContainer", TRUE);
	$appGetter = HTML::select(".appUpdater")->item(0);
	
	if ($status)
	{
		// Add success class
		HTML::addClass($appGetter, "success");
		
		// Set literal
		$appContext = HTML::select(".appUpdater .context.error")->item(0);
		HTML::replace($appContext, NULL);
	}
	else
	{
		// Add success class
		HTML::addClass($appGetter, "success");
		
		// Set literal
		$appContext = HTML::select(".appUpdater .context.error")->item(0);
		HTML::replace($appContext, NULL);
	}
	
	return $pageContent->getReport(".appStatus");
}

return FALSE;
//#section_end#
?>