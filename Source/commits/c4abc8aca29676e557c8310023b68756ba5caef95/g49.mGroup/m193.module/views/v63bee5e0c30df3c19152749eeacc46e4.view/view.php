<?php
//#section#[header]
// Module Declaration
$moduleID = 193;

// Inner Module Codes
$innerModules = array();
$innerModules['statusPage'] = 192;
$innerModules['developerHome'] = 100;

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
importer::import("DEV", "Profiler");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \DEV\Profiler\status;


// Build Module Page
$page = new MPage($moduleID);

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "devSupportPage", TRUE);


// Status
$pStatus = new status();
$status = $pStatus->getStatus();
$statusBar = HTML::select(".statusBar")->item(0);
$statusDesc = HTML::select(".statusBar h3.statusDesc a")->item(0);
if ($status['code'] == status::STATUS_OK)
{
	HTML::addClass($statusBar, "healthy");
	$desc = moduleLiteral::get($innerModules['statusPage'], "lbl_healthyPlatform");
}
else
{
	HTML::addClass($statusBar, "sick");
	$desc = $status['description'];
}
DOM::append($statusDesc, $desc);


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['developerHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

return $page->getReport();
//#section_end#
?>