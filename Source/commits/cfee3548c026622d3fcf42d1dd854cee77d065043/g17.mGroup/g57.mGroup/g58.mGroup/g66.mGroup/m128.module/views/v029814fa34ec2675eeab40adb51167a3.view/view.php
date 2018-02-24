<?php
//#section#[header]
// Module Declaration
$moduleID = 128;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
//#section_end#
//#section#[code]
// Usage
use \UI\Html\HTMLContent;
use \API\Developer\ebuilder\template;

use \ESS\Protocol\server\ModuleProtocol;
use \UI\Navigation\treeView;

$templateID = $_GET['templateId'];


// Create Module Page
$HTMLContentBuilder = new HTMLContent();

//Tree View
$navTree = new treeView();
$navTreeElement = $navTree->get_view('', '', TRUE);

$globalObjectWhapper = $HTMLContentBuilder->buildElement($navTreeElement)->get();

$templateManager = new template();
$templateManager->load($templateID);
	
$themesArray = $templateManager->getAllThemes();
if(empty($pageStructureArray))
{
	
}
else
{
	foreach($themeseArray as $themeObject)
	{
		$item = DOM::create("div");
		$content = DOM::create('span', $pageStructureObject);
		DOM::append($item, $content);
		$attr = array('templateId' => $templateID, 'theme' => $themeObject);
		ModuleProtocol::addActionGET($item, $moduleID, "pageSelector", "", $attr);
		$treeItem = $navTree->insert_treeItem($navTreeElement, '', $item);				
	}
}	


return $HTMLContentBuilder->getReport();
//#section_end#
?>