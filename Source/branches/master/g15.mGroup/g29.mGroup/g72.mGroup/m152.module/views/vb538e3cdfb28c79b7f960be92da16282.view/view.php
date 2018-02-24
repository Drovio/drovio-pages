<?php
//#section#[header]
// Module Declaration
$moduleID = 152;

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

// Create Module Page
$content = new HTMLContent();
$content->build("townManager");
$actionFactory = $content->getActionFactory();

// Title
$title = DOM::create("h2", "Town Listing");
$content->append($title);

// Create container
$gridListContainer = DOM::create("div", "", "", "contentGridListContainer");
$content->append($gridListContainer);

// Create region list
$gridList = new dataGridList();
$regionGridList = $gridList->build($id = "townsGridList", $checkable = FALSE)->get();
DOM::append($gridListContainer, $regionGridList);

$headers = array();
$headers[] = "ID";
$headers[] = "Name";
$headers[] = "Country";
$headers[] = "Latitude";
$headers[] = "Longitude";
$headers[] = "Edit";
$gridList->setHeaders($headers);

// Get all Towns
$dbc = new interDbConnection();
$q = new dbQuery("948279537", "resources.geoloc.towns");
$result = $dbc->execute_query($q, $attr = array());
while ($row = $dbc->fetch($result))
{
	$contents = array();
	$contents[] = $row['id'];
	$contents[] = $row['description'];
	$contents[] = $row['countryName'];
	$contents[] = $row['latitude'];
	$contents[] = $row['longitude'];
	
	// Edit Button
	$btnEdit = DOM::create("a", "Edit", "", "buttonLike");
	DOM::attr($btnEdit, "href", "#");
	DOM::attr($btnEdit, "target", "_self");
	$contents[] = $btnEdit;
	$attr = array();
	$attr['tid'] = $row['id'];
	$actionFactory->setModuleAction($btnEdit, $moduleID, "editTown", "", $attr);
	
	$gridList->insertRow($contents, $checkName = NULL, $checked = FALSE);
}



// Return output
return $content->getReport();
//#section_end#
?>