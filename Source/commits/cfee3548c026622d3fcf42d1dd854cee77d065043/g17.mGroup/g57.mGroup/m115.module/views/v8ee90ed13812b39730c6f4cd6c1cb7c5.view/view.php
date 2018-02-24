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
importer::import("UI", "Presentation");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\template;
use \ESS\Protocol\server\ModuleProtocol;
use \UI\Presentation\tabControl;
use \UI\Navigation\treeView;
use \API\Resources\literals\moduleLiteral;

//Module Friendly Name
$templateGroupObject = 120;
// Create TabControl
$templateGroupTabControl = new tabControl();
$templateGroupTabWrapper = $templateGroupTabControl->get_control($id = "tbr_templateGroupBrowser", TRUE);

// Display ALL Tab
$selected = TRUE;
//Tree View
$navTree = new treeView();
$navTreeElement = $navTree->build('', '', TRUE)->get();

$templateGroupsArray = template::getAllGroups();
foreach($templateGroupsArray as $groupID => $groupTitle)
{
	$item = DOM::create("div");
	$content = DOM::create('span', $groupTitle);
	DOM::append($item, $content);
	$attr = array();
	$attr['id'] = $groupID;
	ModuleProtocol::addActionGET($item, $templateGroupObject, "", "", $attr);
	$treeItem = $navTree->insertItem('', $item);
}
$id = "templateGroup";
$header = moduleLiteral::get($moduleID, "hdr_systemLayouts");
$templateGroupTabControl->insert_tab($id, $header, $navTreeElement, $selected);
$selected = FALSE;
	
//Return Report
report::clear();
report::add_content($templateGroupTabWrapper);
return report::get();
//#section_end#
?>