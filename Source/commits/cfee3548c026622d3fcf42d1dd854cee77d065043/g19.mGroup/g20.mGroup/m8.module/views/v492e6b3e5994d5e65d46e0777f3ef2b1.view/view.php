<?php
//#section#[header]
// Module Declaration
$moduleID = 8;

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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\HTMLServerReport;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\geoloc\locale;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleform;
use \UI\Navigation\sideMenu;

// Initialize
$content = new HTMLContent();
$actionFactory = $content->getActionFactory();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$locale = $_POST['locale'];
	locale::set($locale);
	
	return $actionFactory->getReportReload($formSubmit = TRUE);
}

// Build Content
$content->build("localeSelectorMini", "localeSelector");

$title = DOM::create("h3");
$titleContent = moduleLiteral::get($moduleID, "lbl_chooseLocale");
DOM::append($title, $titleContent);
$content->append($title);

// Build form
$lForm = new simpleForm("localeSelectorMiniForm");
$lFormElement = $lForm->build($moduleID)->get();
$content->append($lFormElement);

// Get Geolocation Data
$dbc = new interDbConnection();
$dbq = new dbQuery("1572562382", "resources.geoloc.locale");
$result = $dbc->execute_query($dbq);
$localeResourceFull = $dbc->toFullArray($result);

$localeOptions = array();
foreach ($localeResourceFull as $locale)
{
	$localeOptions[$locale['region_id']] = array();
	$localeOptions[$locale['region_id']][] = $lForm->getOption($locale['friendlyName'], $locale['countryCode_ISO2A'].":".$locale['locale'], (locale::get() == $locale['locale']));
}

$regionResource = $dbc->toArray($result, "region_id", "name");
$regionOptGroups = array();
foreach ($regionResource as $key => $value)
	$regionOptGroups[$key] = $lForm->getOptionGroup($value, $localeOptions[$key]);


$title = moduleLiteral::get($moduleID, "lbl_language");
$input = $lForm->getSelect($name = "locale", $multiple = FALSE, $class = "", $options = array());
foreach ($regionOptGroups as $optionGroup)
	DOM::append($input, $optionGroup);
$lForm->insertRow($title, $input, $required = TRUE, $notes = "");

return $content->getReport();
//#section_end#
?>