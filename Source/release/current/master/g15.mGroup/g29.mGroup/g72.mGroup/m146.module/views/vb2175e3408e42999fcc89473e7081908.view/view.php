<?php
//#section#[header]
// Module Declaration
$moduleID = 146;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;
use \UI\Forms\templates\simpleForm;

$dbc = new dbConnection();

// Create Module Page
$content = new MContent($moduleID);
$content->build("localeManager");
$actionFactory = $content->getActionFactory();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$q = new dbQuery("1570095450", "resources.geoloc.locale");
	$attr = array();
	$attr['locale'] = $_POST['locale'];
	$result = $dbc->execute($q, $attr);
	
	if ($result)
		return $actionFactory->getReportReload($formSubmit = TRUE);
}

// Title
$titleContent = moduleLiteral::get($moduleID, "lbl_headTitle");
$title = DOM::create("h2");
DOM::append($title, $titleContent);
$content->append($title);

// Basic Languages
$subtitleContent = moduleLiteral::get($moduleID, "lbl_activeLocale");
$subTitle = DOM::create("h4");
DOM::append($subTitle, $subtitleContent);
$content->append($subTitle);

$q = new dbQuery("1572562382", "resources.geoloc.locale");
$result = $dbc->execute($q);
$activeLocales = $dbc->toFullArray($result);
$basicLangsIndex = array();
if (count($activeLocales) == 0)
{
	$noLocale = moduleLiteral::get($moduleID, "lbl_noActiveLocale");
	$contentTitle = DOM::create("p");
	DOM::append($contentTitle, $noLocale);
	$content->append($contentTitle);
}
else
{
	// Create container
	$gridListContainer = DOM::create("div", "", "", "contentGridListContainer");
	$content->append($gridListContainer);
	
	// Create region list
	$basicGridList = new dataGridList();
	$basicLangGridList = $basicGridList->build($id = "activeLocale", $checkable = FALSE)->get();
	DOM::append($gridListContainer, $basicLangGridList);
	
	// Set headers
	$headers = array();
	$headers[] = "ID";
	$headers[] = "Locale";
	$headers[] = "Friendly Name";
	$headers[] = "Country";
	$headers[] = "Language";
	$headers[] = "Edit";
	$headers[] = "Deactivate";
	$basicGridList->setHeaders($headers);
	
	// Insert rows
	foreach ($activeLocales as $locale)
	{
		// Set contents
		$contents = array();
		$contents[] = $locale['id'];
		$contents[] = $locale['locale'];
		$contents[] = $locale['friendlyName'];
		$contents[] = $locale['countryName'];
		$contents[] = $locale['uniDescription'];
	
		// Edit Button
		$btnEdit = DOM::create("a", "Edit", "", "buttonLike");
		DOM::attr($btnEdit, "href", "#");
		DOM::attr($btnEdit, "target", "_self");
		$contents[] = $btnEdit;
		$attr = array();
		$attr['lid'] = $locale['locale'];
		$actionFactory->setModuleAction($btnEdit, $moduleID, "editLocale", "", $attr);
		
		// Remove from basic Button
		$btnEdit = DOM::create("a", "Deactivate", "", "buttonLike");
		DOM::attr($btnEdit, "href", "#");
		DOM::attr($btnEdit, "target", "_self");
		$contents[] = $btnEdit;
		$attr = array();
		$attr['lid'] = $locale['locale'];
		$attr['type'] = 'off';
		$actionFactory->setModuleAction($btnEdit, $moduleID, "setActiveLocale", "", $attr);
		
		$basicGridList->insertRow($contents, $checkName = NULL, $checked = FALSE);
	}
}

$subtitleContent = moduleLiteral::get($moduleID, "lbl_availableLocale");
$subTitle = DOM::create("h4");
DOM::append($subTitle, $subtitleContent);
$content->append($subTitle);

$nonBasicLangsContainer = $content->getModuleContainer($moduleID, $action = "nonActiveLocale", $attr = array(), $startup = TRUE, $containerID = "nonActiveLocalesContainer");
$content->append($nonBasicLangsContainer);

// Add new Locale
// TEMP
$btnContent = moduleLiteral::get($moduleID, "lbl_createNewLocale");
$btnNewCountry = DOM::create("a");
DOM::append($btnNewCountry, $btnContent);
DOM::attr($btnNewCountry, "href", "#");
DOM::attr($btnNewCountry, "target", "_self");
// TEMP__END
$actionFactory->setPopupAction($btnNewCountry, $moduleID, "addLocale");
$content->append($btnNewCountry);


// Default locale
$subtitleContent = moduleLiteral::get($moduleID, "lbl_defaultLocale");
$subTitle = DOM::create("h4");
DOM::append($subTitle, $subtitleContent);
$content->append($subTitle);

$sForm = new simpleForm();
$defaultLocaleForm = $sForm->build($moduleID)->get();
$content->append($defaultLocaleForm);

// __ default locale
$q = new dbQuery("641161041", "resources.geoloc.locale");
$result = $dbc->execute($q);
$defaultLocale = $dbc->fetch($result);

// __ active locale
$q = new dbQuery("1572562382", "resources.geoloc.locale");
$result = $dbc->execute($q);
$activeLocaleResource = $dbc->toArray($result, "locale", "friendlyName");

$title = moduleLiteral::get($moduleID, "lbl_defaultLocale");
$input = $sForm->getResourceSelect($name = "locale", $multiple = FALSE, $class = "", $activeLocaleResource, $selectedValue = $defaultLocale['locale']);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $content->getReport();
//#section_end#
?>