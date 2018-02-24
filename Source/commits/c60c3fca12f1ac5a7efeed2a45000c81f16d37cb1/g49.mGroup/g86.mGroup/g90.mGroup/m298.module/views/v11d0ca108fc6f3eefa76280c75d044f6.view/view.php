<?php
//#section#[header]
// Module Declaration
$moduleID = 298;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "WebEngine");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;
use \UI\Presentation\frames\windowFrame;
use \DEV\WebEngine\distroManager;

// Create page content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "webCoreSettingsDialog", TRUE);

// Load all distros
$distroList = HTML::select(".distroList")->item(0);
$scopeContainer = $pageContent->getModuleContainer($moduleID, $action = "distroList", $attr = array(), $startup = TRUE, $containerID = "", $loading = FALSE, $preload = TRUE);
DOM::append($distroList, $scopeContainer);


// Build the frame
$frame = new windowFrame();
$title = moduleLiteral::get($moduleID, "lbl_webCoreSettings");
$frame->build($title);

// Append content
$frame->append($pageContent->get());

// Return the report
return $frame->getFrame();
//#section_end#
?>