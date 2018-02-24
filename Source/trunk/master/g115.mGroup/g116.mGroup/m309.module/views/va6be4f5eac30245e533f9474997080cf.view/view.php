<?php
//#section#[header]
// Module Declaration
$moduleID = 309;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("API", "Profile");
importer::import("ESS", "Environment");
importer::import("UI", "Login");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Profile\account;
use \UI\Login\loginDialog;
use \UI\Modules\MContent;

// Form dashboard url
$return_url = url::resolve("developer", "/dashboard/");

// Validate account
if (!account::validate())
{
	// Create and return login dialog
	$ld = new loginDialog();
	return $ld->build($username = "", $logintype = loginDialog::LGN_TYPE_PAGE, $return_url)->getReport($background = TRUE, $fade = TRUE);
}

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Redirect to developer's dashboard
return $actionFactory->getReportRedirect($return_url);
//#section_end#
?>