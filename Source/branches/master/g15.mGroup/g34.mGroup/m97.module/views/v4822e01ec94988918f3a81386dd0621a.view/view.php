<?php
//#section#[header]
// Module Declaration
$moduleID = 97;

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
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Navigation\treeView;
use \UI\Presentation\togglers\accordion;

// Create Module Content
$content = new MContent($moduleID);

// Build the content
$content->build("", "moduleViewerContent");


$mdlAcc = new accordion();
$moduleAccordion = $mdlAcc->build()->get();
$content->append($moduleAccordion);

// Get Modules
$groupID = engine::getVar("gid");
$allModules = module::getAllModules($groupID);
foreach ($allModules as $module)
{
	// Set slice head
	$head = DOM::create("div", "", "", "moduleHeader");
	$mdlTitle = DOM::create("b", $module['title'], "", "moduleTitle");
	DOM::append($head, $mdlTitle);
	$mdlScope = DOM::create("span", " [".$module['scope_desc']."] ", "", "moduleScope");
	DOM::append($head, $mdlScope);
	$mdlStatus = DOM::create("span", " [".$module['status_desc']."] ", "", "moduleStatus");
	DOM::append($head, $mdlStatus);
	
	// Set slice content
	$sliceContent = DOM::create("span", "empty content for now");
	$attr = array();
	$attr['id'] = $module['id'];
	$sliceContent = $content->getModuleContainer($moduleID, $action = "moduleEditor", $attr, $startup = FALSE, $containerID = "mdl_".$module['id']);
	
	DOM::attr($head, "data-ref", $module['id']);
	
	// Add slice
	$mdlAcc->addSlice("sl_".$module['id'], $head, $sliceContent, $selected = FALSE);
}


// Return output
return $content->getReport();
//#section_end#
?>