<?php
//#section#[header]
// Module Declaration
$moduleID = 258;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleform;
use \UI\Navigation\sideMenu;

// Initialize
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$locale = $_POST['locale'];
	locale::set($locale);
	
	return $actionFactory->getReportReload($formSubmit = TRUE);
}

// Build Content
$pageContent->build("", "localeSelectorDialog", TRUE);


$localeSelector = HTML::select(".localeSelector")->item(0);

// Build form
$form = new simpleForm("languageForm");
$languageForm = $form->build($moduleID)->get();
DOM::append($localeSelector, $languageForm);

// Get Active Locale
$activeLocale = locale::active();
$localeOptions = array();
foreach ($activeLocale as $locale)
{
	$localeOptions[$locale['region_id']] = array();
	$localeOptions[$locale['region_id']][] = $form->getOption($locale['friendlyName'], $locale['countryCode_ISO2A'].":".$locale['locale'], (locale::get() == $locale['locale']));
}

$regionOptGroups = array();
foreach ($activeLocale as $locale)
	$regionOptGroups[$locale['region_id']] = $form->getOptionGroup($locale['name'], $localeOptions[$locale['region_id']]);


$title = moduleLiteral::get($moduleID, "lbl_language");
$input = $form->getSelect($name = "locale", $multiple = FALSE, $class = "", $options = array());
foreach ($regionOptGroups as $optionGroup)
	DOM::append($input, $optionGroup);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

return $pageContent->getReport();
//#section_end#
?>