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
importer::import("API", "Developer");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\template;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;

$templateID = $_GET['id'];
$holder = (isset($_GET['holder']) ? '#'.$_GET['holder'] : '');

// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$ModuleHTMLContent = $HTMLContent->build()->get();
DOM::attr($ModuleHTMLContent, "style", "height: 100%");

// Build layout
$listViewerHolder = DOM::create('div','','','sidebar');
DOM::append($ModuleHTMLContent, $listViewerHolder);

$editorWrapper = DOM::create('div','','editorWrapper ','rightContent');
DOM::append($ModuleHTMLContent, $editorWrapper);


// Sidebar LayoutLIst Viewer
DOM::attr($listViewerHolder, "style", "height: 100%");

$userPrompt = DOM::create('div', '', '', 'userPrompt');	
	$content = DOM::create('span', 'Select One or More Objects for edit.');
	DOM::append($userPrompt, $content);
DOM::append($listViewerHolder, $userPrompt);

$attr = array();
$attr['templateId'] = $templateID;
$attr['holder'] = '#editorWrapper';

//Tree View
$navTree = new treeView();
$navTreeElement = $navTree->build('', '', TRUE)->get();
DOM::append($listViewerHolder, $navTreeElement);

$templateManager = new template();
$templateManager->load($templateID);
	
$themesArray = $templateManager->getAllThemes();
if(empty($themesArray))
{
	$item = DOM::create("div");
	$content = DOM::create('span', "Nothing Available");
	DOM::append($item, $content);
	DOM::prepend($navTreeElement, $item);
}
else
{
	foreach($themesArray as $themeObject)
	{
		$item = DOM::create("div");
		$content = DOM::create('span', $themeObject);
		DOM::append($item, $content);
		$attr['theme'] = $themeObject;
		$actionFactory->setModuleAction($item, $moduleID, "pageSelector", "", $attr);
		$treeItem = $navTree->insertTreeItem('', $item);				
	}
}	




// Return output
return $HTMLContent->getReport($holder );
//#section_end#
?>