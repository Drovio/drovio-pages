<?php
//#section#[header]
// Module Declaration
$moduleID = 146;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \UI\Html\HTMLContent;
use \UI\Presentation\dataGridList;


$content = new HTMLContent();
$content->build("", "nonActiveLocaleContent");
$actionFactory = $content->getActionFactory();


// Create container
$gridListContainer = DOM::create("div", "", "", "contentGridListContainer");
$content->append($gridListContainer);

// Create region list
$gridList = new dataGridList();
$nonBasicLanguageGrid = $gridList->build($id = "nonActiveLocale", $checkable = FALSE)->get();
DOM::append($gridListContainer, $nonBasicLanguageGrid);

$headers = array();
$headers[] = "ID";
$headers[] = "Locale";
$headers[] = "Friendly Name";
$headers[] = "Country";
$headers[] = "Language";
$headers[] = "Edit";
$headers[] = "Activate";
$gridList->setHeaders($headers);

// Get All Languages
$dbc = new interDbConnection();
$q = new dbQuery("664120126", "resources.geoloc.locale");
$result = $dbc->execute_query($q, $attr = array());
while ($row = $dbc->fetch($result))
{
	// Set contents
	$contents = array();
	$contents[] = $row['id'];
	$contents[] = $row['locale'];
	$contents[] = $row['friendlyName'];
	$contents[] = $row['countryName'];
	$contents[] = $row['uniDescription'];
	
	// Edit Button
	$btnEdit = DOM::create("a", "Edit", "", "buttonLike");
	DOM::attr($btnEdit, "href", "#");
	DOM::attr($btnEdit, "target", "_self");
	$contents[] = $btnEdit;
	$attr = array();
	$attr['lid'] = $row['locale'];
	$actionFactory->setModuleAction($btnEdit, $moduleID, "editLocale", "", $attr);
	
	// Remove from basic Button
	$btnEdit = DOM::create("a", "Activate", "", "buttonLike");
	DOM::attr($btnEdit, "href", "#");
	DOM::attr($btnEdit, "target", "_self");
	$contents[] = $btnEdit;
	$attr = array();
	$attr['lid'] = $row['locale'];
	$attr['type'] = 'on';
	$actionFactory->setModuleAction($btnEdit, $moduleID, "setActiveLocale", "", $attr);
	
	$gridList->insertRow($contents, $checkName = NULL, $checked = FALSE);
}

return $content->getReport();
//#section_end#
?>