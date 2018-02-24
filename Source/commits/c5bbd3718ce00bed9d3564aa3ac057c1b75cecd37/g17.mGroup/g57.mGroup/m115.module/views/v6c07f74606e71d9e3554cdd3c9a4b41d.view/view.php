<?php
//#section#[header]
// Module Declaration
$moduleID = 115;

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
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\template;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;

$viewType = $_GET['viewType'];

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();
$globalContainer = $HTMLContentBuilder->build()->get();
DOM::attr($globalContainer, "style", "height:100%");

switch ($viewType)
{
	case 'all':
		$templatesArray = template::getTemplates('','deploy');
		break;	
	case 'project':
		$templatesArray = template::getUserTemplates('','project');
		break;
	case 'my':
		$templatesArray = template::getUserTemplates('','deploy');
		break;
	default:		
		break;		
}

// Side Bar Menu
$sidebar  = DOM::create("div", "", "", "sidebar");
DOM::append($globalContainer, $sidebar);

// Main
$holderId = "infoHolder_".$viewType;
$rightContent = DOM::create("div", "", $holderId, "rightContent");
DOM::append($globalContainer, $rightContent);

//Tree View
$navTree = new treeView();
$navTreeElement = $navTree->build('', '', TRUE)->get();
DOM::append($sidebar, $navTreeElement);

if(empty($templatesArray))
{
	echo $viewType."_NOTHING";
}
else
{
	$attr = array();
	$attr['viewType'] = $viewType;
	$attr['holder'] = $holderId;
	foreach($templatesArray as $templateID => $templateTitle)
	{
		$content = DOM::create('span', $templateTitle);
		$attr['id'] = $templateID;
		$item = $navTree->insertTreeItem("temp_".$templateID, $content);		
		$actionFactory->setModuleAction($item, $moduleID, "templatePresenter", "", $attr);
	}
}

// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>