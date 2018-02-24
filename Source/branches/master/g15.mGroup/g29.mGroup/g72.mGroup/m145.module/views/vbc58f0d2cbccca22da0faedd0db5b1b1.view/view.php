<?php
//#section#[header]
// Module Declaration
$moduleID = 145;

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
use \UI\Presentation\togglers\toggler;
use \UI\Presentation\dataGridList;

// Create Module Page
$content = new HTMLContent();
$content->build("regionManager");
$actionFactory = $content->getActionFactory();

// Title
$title = DOM::create("h2", "Country Listing");
$content->append($title);

// Region Grid Lists
$regionGridLists = array();
$headers = array();
$headers[] = "ID";
$headers[] = "Name";
$headers[] = "ISO2";
$headers[] = "ISO3";
$headers[] = "Edit";

// Get all Regions
$dbc = new interDbConnection();
$q = new dbQuery("1085838709", "resources.geoloc.regions");
$result = $dbc->execute_query($q, $attr = array());
$regionToggler = new toggler();
while ($row = $dbc->fetch($result))
{
	$gridListContainer = DOM::create("div", "", "", "contentGridListContainer");
	$regionHeader = DOM::create("span", $row['name']);
	$regionTogglerElement = $regionToggler->build($id = "", $header = $regionHeader, $body = $gridListContainer, $open = FALSE)->get();
	$content->append($regionTogglerElement);
	
	$gridList = new dataGridList();
	$regionGridList = $gridList->build($id = "regionGridList_".$row['id'], $checkable = FALSE)->get();
	$gridList->setHeaders($headers);
	DOM::append($gridListContainer, $regionGridList);
	
	$regionGridLists[$row['id']] = $gridList;
}

// No Region Grid List
$gridListContainer = DOM::create("div", "", "", "contentGridListContainer");
$regionHeader = DOM::create("span", "No Region");
$regionTogglerElement = $regionToggler->build($id = "", $header = $regionHeader, $body = $gridListContainer, $open = FALSE)->get();
$content->append($regionTogglerElement);
	
$noRegionGridList = new dataGridList();
$noRegionGridListElement = $noRegionGridList->build($id = "noRegionGridList", $checkable = FALSE)->get();
$noRegionGridList->setHeaders($headers);
DOM::append($gridListContainer, $noRegionGridListElement);

// Get all Countries
$q = new dbQuery("1434209549", "resources.geoloc.countries");
$result = $dbc->execute_query($q, $attr = array());
while ($row = $dbc->fetch($result))
{
	$contents = array();
	$contents[] = $row['id'];
	$contents[] = $row['countryName'];
	$contents[] = $row['countryCode_ISO2A'];
	$contents[] = $row['countryCode_ISO3A'];
	
	// Edit Button
	$btnEdit = DOM::create("a", "Edit", "", "buttonLike");
	DOM::attr($btnEdit, "href", "#");
	DOM::attr($btnEdit, "target", "_self");
	$contents[] = $btnEdit;
	$attr = array();
	$attr['cid'] = $row['id'];
	$actionFactory->setModuleAction($btnEdit, $moduleID, "editCountry", "", $attr);
	
	if (!empty($row['region_id']))
	{
		$gridList = $regionGridLists[$row['region_id']];
		$gridList->insertRow($contents, $checkName = "c[".$row['id']."]", $checked = FALSE);
	}
	else
		$noRegionGridList->insertRow($contents, $checkName = "c[".$row['id']."]", $checked = FALSE);
}


// Add new Country
// TEMP
$btnNewCountry = DOM::create("a", "Create New Country");
DOM::attr($btnNewCountry, "href", "#");
DOM::attr($btnNewCountry, "target", "_self");
// TEMP__END
$actionFactory->setPopupAction($btnNewCountry, $moduleID, "addCountry");
$content->append($btnNewCountry);


// Return output
return $content->getReport();
//#section_end#
?>