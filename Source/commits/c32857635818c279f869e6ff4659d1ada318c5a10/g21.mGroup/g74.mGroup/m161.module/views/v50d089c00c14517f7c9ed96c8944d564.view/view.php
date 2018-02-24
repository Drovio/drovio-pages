<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);

// Build the content
$pageContent->build("", "generalSettingsContainer", TRUE);

$sections = array();
$sections["password"] = "passwordManager";
$sections["managed"] = "accountManager";
$sections["sessions"] = "sessionManager";

foreach ($sections as $section => $viewName)
{
	// Get sbody container
	$sbody = HTML::select(".srow.".$section." .sbody")->item(0);
	
	// Build module container
	$body = $pageContent->getModuleContainer($moduleID, $viewName, $attr = array(), $startup = FALSE, $containerID = "");
	HTML::addClass($body, "sContainer");
	
	// Add to sbody
	DOM::append($sbody, $body);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>