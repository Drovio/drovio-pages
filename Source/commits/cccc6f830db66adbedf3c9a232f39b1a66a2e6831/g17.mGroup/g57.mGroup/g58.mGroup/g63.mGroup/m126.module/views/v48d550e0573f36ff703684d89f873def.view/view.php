<?php
//#section#[header]
// Module Declaration
$moduleID = 126;

// Inner Module Codes
$innerModules = array();
$innerModules['templateObject'] = 117;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\templateManager;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;



$templateID = $_GET['templateId'];
$themeName = $_GET['theme'];
$holder = $_GET['holder'];

// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$ModuleHTMLContent = $HTMLContent->build()->get();
DOM::attr($ModuleHTMLContent, "style", "height: 100%");


$refreshControl = DOM::create('div', '', '', 'refreshControl');		
$visibilitySwitch = DOM::create('div', '', '', 'visibilitySwitch');
DOM::append($refreshControl, $visibilitySwitch);
DOM::appendAttr($visibilitySwitch, 'data-pageSelector', 'hide');
$refresh = DOM::create('span', 'Hide');
DOM::append($visibilitySwitch, $refresh);
DOM::append($ModuleHTMLContent, $refreshControl);

//Tree View
$navTree = new treeView();
$navTreeElement = $navTree->build()->get();
DOM::append($ModuleHTMLContent, $navTreeElement);

// Try to load Object
$templateManager = new templateManager();
$templateObject = $templateManager->getTemplate($templateID);
	
$pageStructureArray = $templateObject->getAllStructures();
if(empty($pageStructureArray))
{
	$item = DOM::create("div");
	$content = DOM::create('span', "Nothing Available");
	DOM::append($item, $content);
	DOM::prepend($navTreeElement, $item);	
}
else
{
	$attr = array('templateId' => $templateID, 'theme' => $themeName);
	foreach($pageStructureArray as $pageStructureObject)
	{
		$item = DOM::create("div");
		$content = DOM::create('span', $pageStructureObject);
		DOM::append($item, $content);
		$attr['pageStructure'] = $pageStructureObject;
		$actionFactory->setModuleAction($item, $moduleID, "thObjectEditor", "", $attr);
		$treeItem = $navTree->insertTreeItem('', $item);			
	}
}

return $HTMLContent->getReport($holder);
//#section_end#
?>