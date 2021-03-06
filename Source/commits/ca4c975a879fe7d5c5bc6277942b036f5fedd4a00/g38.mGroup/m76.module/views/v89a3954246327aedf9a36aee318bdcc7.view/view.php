<?php
//#section#[header]
// Module Declaration
$moduleID = 76;

// Inner Module Codes
$innerModules = array();
$innerModules['frontend'] = 70;

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
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Resources\url;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Modules\MPage;

// Build Module Page
$page = new MPage($moduleID);

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "helpCenterPage", TRUE);

$urls = array();
$urls["starting"] = url::resolve("www", "/help/trending/starting/");
$urls["security"] = url::resolve("www", "/help/trending/security/");
$urls["social"] = url::resolve("www", "/help/trending/social/");
$urls["international"] = url::resolve("www", "/help/trending/international/");
$urls["new"] = url::resolve("www", "/help/trending/new/");
$urls["business"] = url::resolve("www", "/help/trending/business/");
$urls["apps"] = url::resolve("www", "/help/trending/apps/");
$urls["issues"] = url::resolve("www", "/help/trending/issues/");
foreach ($urls as $class => $url)
{
	$urlA = HTML::select("a.".$class)->item(0);
	//DOM::attr($urlA, "href", $url);
}


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['frontend'], "navigationBar");
DOM::append($navBar, $navigationBar);

return $page->getReport();
//#section_end#
?>