<?php
//#section#[header]
// Module Declaration
$moduleID = 111;

// Inner Module Codes
$innerModules = array();
$innerModules['layoutObjectEditor'] = 110;

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
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Developer\resources\layouts\systemLayout;
use \API\Developer\resources\layouts\ebuilderLayout;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;
use \UI\Presentation\tabControl;
use \ESS\Protocol\server\ModuleProtocol;

// Create Module Page
$HTMLContentBuilder = new HTMLContent();

// Create TabControl
$layoutTabControl = new tabControl();
$layoutTabWrapper = $layoutTabControl->build($id = "tbr_layoutBrowser", TRUE)->get();

$ModuleHTMLContent = $HTMLContentBuilder->buildElement($layoutTabWrapper)->get();

//If usser to system Developer group
$selected = TRUE;
if(TRUE)
{
	//Tree View
	$navTree = new treeView();
	$navTreeElement = $navTree->build('', '', TRUE)->get();
	
	$sysLayoutManager = new systemLayout();
	$sysLayouts = $sysLayoutManager->getAllLayouts();
	$group = "system";
	foreach($sysLayouts as $layout)
	{
		$item = DOM::create("div");
		$content = DOM::create('span', $layout);
		DOM::append($item, $content);
		$attr = array();
		$attr['group'] = $group;
		$attr['name'] = $layout;
		ModuleProtocol::addActionGET($item, $innerModules['layoutObjectEditor'], "", "", $attr);
		$treeItem = $navTree->insertItem('', $item);
	}
	$id = "sysLayouts";
	$header = moduleLiteral::get($moduleID, "hdr_systemLayouts");
	$layoutTabControl->insertTab($id, $header, $navTreeElement, $selected);
	$selected = FALSE;
}
//If usser to ebuilder system Developer group
if(TRUE)
{
	//Tree View
	$navTree = new treeView();
	$navTreeElement = $navTree->build('', '', TRUE)->get();
	
	$ebldLayoutManager = new ebuilderLayout();
	$ebldLayouts = $ebldLayoutManager->getAllLayouts();
	$group = "ebuilder";
	foreach($ebldLayouts as $layout)
	{
		$item = DOM::create("div");
		$content = DOM::create('span', $layout);
		DOM::append($item, $content);
		$attr = array();
		$attr['group'] = $group;
		$attr['name'] = $layout;
		ModuleProtocol::addActionGET($item, $innerModules['layoutObjectEditor'], "", "", $attr);
		$treeItem = $navTree->insertItem('', $item);
	}
	$id = "ebldLayouts";
	$header = moduleLiteral::get($moduleID, "hdr_ebuilderLayouts");
	$layoutTabControl->insertTab($id, $header, $navTreeElement, $selected);
	$selected = FALSE;
}


return $HTMLContentBuilder->getReport();
//#section_end#
?>