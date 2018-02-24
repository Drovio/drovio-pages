<?php
//#section#[header]
// Module Declaration
$moduleID = 317;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Presentation\popups\popup;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get app id and 
$akey = engine::getVar("akey");

// Build the application view content
$pageContent->build("", "apiKeyDialogContainer", TRUE);

// Load key basic information
$basicInfo = HTML::select(".apiKeyDialog .basicKeyInfo")->item(0);
$attr = array();
$attr['akey'] = $akey;
$keyInfo = $pageContent->getModuleContainer($moduleID, $viewName = "keyInfo", $attr, $startup = TRUE, $containerID = "keyInfoContainer", $loading = FALSE, $preload = TRUE);
DOM::append($basicInfo, $keyInfo);


// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->build($pageContent->get());

return $pp->getReport();
//#section_end#
?>