<?php
//#section#[header]
// Module Declaration
$moduleID = 97;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;
use \UI\Presentation\togglers\accordion;

// Create Module Content
$content = new HTMLContent();

// Build the content
$content->build("", "moduleViewerContent");


$mdlAcc = new accordion();
$moduleAccordion = $mdlAcc->build()->get();
$content->append($moduleAccordion);

// Get Modules
$dbc = new interDbConnection();
$dbq = new dbQuery("727912579", "units.modules");
$attr = array();
$attr['gid'] = $_GET['gid'];
$result = $dbc->execute_query($dbq, $attr);
$allModules = $dbc->toFullArray($result);

foreach ($allModules as $module)
{
	// Set slice head
	$head = DOM::create("div", "", "", "moduleHeader");
	$mdlTitle = DOM::create("b", $module['module_title'], "", "moduleTitle");
	DOM::append($head, $mdlTitle);
	$mdlScope = DOM::create("span", " [".$module['scope']."] ", "", "moduleScope");
	DOM::append($head, $mdlScope);
	$mdlStatus = DOM::create("span", " [".$module['status']."] ", "", "moduleStatus");
	DOM::append($head, $mdlStatus);
	
	// Set slice content
	$sliceContent = DOM::create("span", "empty content for now");
	$attr = array();
	$attr['id'] = $module['module_id'];
	$sliceContent = $content->getModuleContainer($moduleID, $action = "moduleEditor", $attr, $startup = FALSE, $containerID = "mdl_".$module['module_id']);
	
	DOM::attr($head, "data-ref", $module['module_id']);
	
	// Add slice
	$mdlAcc->addSlice("sl_".$module['module_id'], $head, $sliceContent, $selected = FALSE);
}


// Return output
return $content->getReport();
//#section_end#
?>