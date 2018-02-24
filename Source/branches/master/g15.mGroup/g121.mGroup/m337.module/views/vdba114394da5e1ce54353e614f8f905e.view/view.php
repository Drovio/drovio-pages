<?php
//#section#[header]
// Module Declaration
$moduleID = 337;

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
importer::import("SYS", "Resources");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Resources\pages\domain;
use \SYS\Resources\pages\pageFolder;
use \SYS\Resources\pages\page;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "folderViewContainer", TRUE);

// Get folder id and trail
$folderID = engine::getVar("fid");
$folderInfo = pageFolder::info($folderID);
$trail = pageFolder::trail($folderID);

// Get all domains
$domains = domain::getAllDomains();
foreach ($domains as $domain)
	if ($folderInfo['domain'] == $domain['domain_name'])
		$trail = str_replace($domain['path'], "/".$domain['domain_name'], $trail);

// Set header trail
$toolbar = HTML::select(".folderView .toolbar .path")->item(0);
HTML::innerHTML($toolbar, $trail);


// Get folders and pages
$fpool = HTML::select(".folderView .fpool")->item(0);
$folders = pageFolder::getSubFolders($folderID);
foreach ($folders as $folder)
{
	// Set attributes
	$attr = array();
	$attr['fid'] = $folder['id'];
	
	// Create tile
	$tile = getTile($folder['name'], "folder", $actionFactory, $moduleID, "folderView", $attr);
	DOM::append($fpool, $tile);
}

$pages = page::getFolderPages($folderID);
foreach ($pages as $page)
{
	// Set attributes
	$attr = array();
	$attr['pid'] = $page['id'];
	
	// Create tile
	$tile = getTile($page['file'], "page", $actionFactory, $moduleID, "pageEditor", $attr);
	DOM::append($fpool, $tile);
}

$attr = array();
$attr['cfid'] = $folderID;

// Actions
$actionItem = HTML::select(".folderView .toolbar .controls .ctrl.folder")->item(0);
$actionFactory->setModuleAction($actionItem, $moduleID, "createFolder", "", $attr, $loading = TRUE);

$actionItem = HTML::select(".folderView .toolbar .controls .ctrl.dfolder")->item(0);
$actionFactory->setModuleAction($actionItem, $moduleID, "deleteFolder", "", $attr, $loading = TRUE);

$actionItem = HTML::select(".folderView .toolbar .controls .ctrl.page")->item(0);
$actionFactory->setModuleAction($actionItem, $moduleID, "createPage", "", $attr, $loading = TRUE);


// Return output
return $pageContent->getReport();


function getTile($name, $class, $actionFactory, $moduleID, $viewName, $attr)
{
	// Create tile container
	$tileContainer = DOM::create("div", "", "", "tileContainer");
	
	$tile = DOM::create("div", "", "", "tile");
	DOM::append($tileContainer, $tile);
	HTML::addClass($tile, $class);
	
	// Set action
	$actionFactory->setModuleAction($tile, $moduleID, $viewName, ".pageManager .contentEditor", $attr, $loading = TRUE);
	
	// Tile icon
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($tile, $ico);
	
	// Title
	$title = DOM::create("div", $name, "", "title");
	DOM::append($tile, $title);
	
	// Return tile
	return $tileContainer;
}
//#section_end#
?>