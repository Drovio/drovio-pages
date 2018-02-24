<?php
//#section#[header]
// Module Declaration
$moduleID = 150;

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

$dbc = new interDbConnection();

// Create Module Page
$content = new HTMLContent();
$content->build("languageManager");
$actionFactory = $content->getActionFactory();

// Title
$title = DOM::create("h2", "Languages");
$content->append($title);


// Basic Languages
$subTitle = DOM::create("h4", "Basic Languages");
$content->append($subTitle);

$q = new dbQuery("644017852", "resources.geoloc.languages");
$result = $dbc->execute_query($q);
$basicLangs = $dbc->toFullArray($result);
$basicLangsIndex = array();
if (count($basicLangs) == 0)
{
	$contentTitle = DOM::create("p", "There are no basic languages yet.");
	$content->append($contentTitle);
}
else
{
	// Create container
	$gridListContainer = DOM::create("div", "", "", "contentGridListContainer");
	$content->append($gridListContainer);
	
	// Create region list
	$basicGridList = new dataGridList();
	$basicLangGridList = $basicGridList->build($id = "basicLanguages", $checkable = FALSE)->get();
	DOM::append($gridListContainer, $basicLangGridList);
	
	// Set headers
	$headers = array();
	$headers[] = "ID";
	$headers[] = "Universal Desc";
	$headers[] = "Native Desc";
	$headers[] = "ISO2/A3";
	$headers[] = "ISO1/A2";
	$headers[] = "Edit";
	$headers[] = "nonBasic";
	$basicGridList->setHeaders($headers);
	
	// Insert rows
	foreach ($basicLangs as $lang)
	{
		// Keep index for later
		$basicLangsIndex[] = $lang['id'];
		
		// Remove basic field
		unset($lang['basic']);
		
		// Set contents
		$contents = $lang;
	
		// Edit Button
		$btnEdit = DOM::create("a", "Edit", "", "buttonLike");
		DOM::attr($btnEdit, "href", "#");
		DOM::attr($btnEdit, "target", "_self");
		$contents[] = $btnEdit;
		$attr = array();
		$attr['lid'] = $lang['id'];
		$actionFactory->setModuleAction($btnEdit, $moduleID, "editLanguage", "", $attr);
		
		// Remove from basic Button
		$btnEdit = DOM::create("a", "Remove", "", "buttonLike");
		DOM::attr($btnEdit, "href", "#");
		DOM::attr($btnEdit, "target", "_self");
		$contents[] = $btnEdit;
		$attr = array();
		$attr['lid'] = $lang['id'];
		$attr['type'] = 'off';
		$actionFactory->setModuleAction($btnEdit, $moduleID, "setBasicLanguage", "", $attr);
		
		$basicGridList->insertRow($contents, $checkName = NULL, $checked = FALSE);
	}
}

$subTitle = DOM::create("h4", "Non-Basic Languages");
$content->append($subTitle);

$triggerSpan = DOM::create("a", "Show", "toggleNonBasicLangs");
DOM::attr($triggerSpan, "href", "#");
DOM::attr($triggerSpan, "target", "_self");
$content->append($triggerSpan);

$nonBasicLangsContainer = $content->getModuleContainer($moduleID, $action = "nonBasicLanguages", $attr = array(), $startup = FALSE, $containerID = "nonBasicLangsContainer");
$content->append($nonBasicLangsContainer);

// Return output
return $content->getReport();
//#section_end#
?>