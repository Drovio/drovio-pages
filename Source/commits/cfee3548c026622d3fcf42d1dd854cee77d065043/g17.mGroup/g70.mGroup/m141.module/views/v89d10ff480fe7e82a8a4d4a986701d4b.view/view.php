<?php
//#section#[header]
// Module Declaration
$moduleID = 141;

// Inner Module Codes
$innerModules = array();
$innerModules['extensionObject'] = 142;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Prototype");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\extManager;
use \API\Resources\literals\moduleLiteral;
use \UI\Presentation\tabControl;
use \UI\Html\HTMLModulePage;
use \UI\Html\pageComponents\HTMLRibbon;
use \UI\Html\pageComponents\ribbonComponents\ribbonPanel;
use \UI\Presentation\frames\windowFrame;
use \ESS\Prototype\html\ModuleContainerPrototype;
use \UI\Navigation\treeView;

// Create Module Page
$page = new HTMLModulePage("simpleFullScreen");
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);

// Build the module
$page->build($pageTitle);

//____________________ Build Top Navigation ____________________//
// _____ Toolbar Navigation

// Database Navigation Collection
$navCollection = $page->getRibbonCollection("templateManagerNav");
$subItem = $page->addToolbarNavItem("templateManagerNavSub", $title = "", $class = "add_new", $navCollection, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0);


// Create Namespace/Object
$new_objPanel = new ribbonPanel();
//$new_objPanel->insert_group();
//___ Object
$objTitle = moduleLiteral::get($moduleID, "createTemplate");
$new_objItem = $new_objPanel->insertPanelItem("small", $objTitle);
windowFrame::setAction($new_objItem, $innerModules['extensionObject'], 'newExtension');
HTMLRibbon::insertItem($navCollection, $new_objItem );
//____________________ Build Top Navigation ____________________//__________End


$container = DOM::create('div');
$page->appendToSection("mainContent", $container);

$extensionssArray = extManager::getUserExtensions('','project');



if(empty($extensionssArray))
{
	$content = DOM::create('span', "Nothing");
	DOM::append($container, $content );
}
else
{
	//Tree View
	$navTree = new treeView();
	$navTreeElement = $navTree->build('', '', TRUE)->get();
	DOM::append($container, $navTreeElement);

	$attr = array();
	$attr['viewType'] = $viewType;
	$attr['holder'] = $holderId;
	foreach($extensionssArray as $extensionID => $extensionTitle)
	{		
		$item = DOM::create("div");
		$content = DOM::create('span', $extensionTitle);
		DOM::append($item, $content);
		$attr['id'] = $extensionID;
		//ModuleProtocol::addActionGET($item, $moduleID, "templatePresenter", "", $attr);
		$treeItem = $navTree->insertItem('', $item);				
	}
}

// Return output
return $page->getReport();
//#section_end#
?>