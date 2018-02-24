<?php
//#section#[header]
// Module Declaration
$moduleID = 140;

// Inner Module Codes
$innerModules = array();
$innerModules['literalViewer'] = 153;

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
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Profile\translator as userTranslator;
use \API\Resources\geoloc\locale;
use \API\Resources\literals\translator;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLModulePage;
use \UI\Navigation\TreeView;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Join to translator group with the given locale
	userTranslator::join($_POST['locale']);
}

// Create Module Page
$page = new HTMLModulePage("TwoColumnsLeftSidebarCentered");

// Build the module
$page->build("Redback Translator", "translator");
$actionFactory = $page->getActionFactory();

$scopeTreeContainer = DOM::create("div", "", "", "literalScopes");
$page->appendToSection("sidebar", $scopeTreeContainer);

// Build the sidebar (Literal scopes)
$treeView = new treeView();
$scopeTree = $treeView->build($id = "", $class = "", $sorting = FALSE)->get();
DOM::append($scopeTreeContainer, $scopeTree);


// Get all literal scopes
$dbc = new interDbConnection();
$q = new dbQuery("928581721", "resources.literals");
$result = $dbc->execute($q);
while ($row = $dbc->fetch($result))
{
	// Create the item content
	$item = DOM::create("span", $row['scope']);
	$treeItem = $treeView->insertItem($id, $item);
	
	// Set the item action
	$attr = array();
	$attr['scope'] = $row['scope'];
	$actionFactory->setModuleAction($treeItem, $innerModules['literalViewer'], "", "#literalViewer", $attr);
}

// Get Translator profile
$translatorProfile = userTranslator::profile();
print_r($translatorProfile);
$hr = DOM::create("hr");
$page->appendToSection("sidebar", $hr);

$languageSelector = DOM::create("div", "", "", "languageSelector");
$page->appendToSection("sidebar", $languageSelector);

$titleContent = moduleLiteral::get($moduleID, "lbl_languageSelector");
$title = DOM::create("h4");
DOM::append($title, $titleContent);
DOM::append($languageSelector, $title);


$titleContent = moduleLiteral::get($moduleID, "lbl_currentLanguage");
$currentLanguageTitle = DOM::create("p", ": ");
DOM::prepend($currentLanguageTitle, $titleContent);
DOM::append($languageSelector, $currentLanguageTitle);

$languageB = DOM::create("b", $translatorProfile['friendlyName']);
DOM::append($currentLanguageTitle, $languageB);

// Get Available Locale
$dbc = new interDbConnection();
$dbq = new dbQuery("664120126", "resources.geoloc.locale");
$result = $dbc->execute($dbq);
$localesResource = $dbc->toArray($result, "locale", "friendlyName");

// Build the locale selection form
$lsForm = new simpleForm("localeSelector");
$localeSelectionForm = $lsForm->build($moduleID, "", FALSE)->get();
DOM::append($languageSelector, $localeSelectionForm);

// Insert Locale selector
$title = moduleLiteral::get($moduleID, "lbl_changeLanguage");
$lsForm->append($title);
$input = $lsForm->getResourceSelect($name = "locale", $multiple = FALSE, $class = "", $localesResource, $selectedValue = NULL);
$inputRow = $lsForm->buildRow($title, $input, $required = FALSE, $notes = "");
$lsForm->append($inputRow);

$title = moduleLiteral::get($moduleID, "lbl_change");
$submitButton = $lsForm->getSubmitButton($title, $id = "");
$lsForm->append($submitButton);


// Translation Statistics
$titleContent = moduleLiteral::get($moduleID, "lbl_translationStatistics");
$title = DOM::create("h4");
DOM::append($title, $titleContent);
DOM::append($languageSelector, $title);

// Total Literals
$q = new dbQuery("94571834", "resources.literals");
$attr = array();
$attr['locale'] = locale::_default();
$result = $dbc->execute($q, $attr);
$defaultLocale = $dbc->fetch($result);
$defaultLockedCount = $defaultLocale['count'];

$title = moduleLiteral::get($moduleID, "lbl_totalLiterals");
$totalLiterals = DOM::create("p", " : ");
$numLocked = DOM::create("b", $defaultLockedCount);
DOM::prepend($totalLiterals, $title);
DOM::append($totalLiterals, $numLocked);
$page->appendToSection("sidebar", $totalLiterals);


// Locked literals of the translated locale
$attr['locale'] = $translatorProfile['translation_locale'];
$result = $dbc->execute($q, $attr);
$lockedTranslatedLocale = $dbc->fetch($result);
$lockedPercentage = round(($lockedTranslatedLocale['count'] / $defaultLockedCount) * 100, 2);

$title = moduleLiteral::get($moduleID, "lbl_lockedPercentage");
$lockedLiterals = DOM::create("p", " : ");
$numLocked = DOM::create("b", $lockedPercentage."%");
DOM::prepend($lockedLiterals, $title);
DOM::append($lockedLiterals, $numLocked);
$page->appendToSection("sidebar", $lockedLiterals);


// Translated literals of the translated locale
$q = new dbQuery("1836048327", "resources.literals.translator");
$attr = array();
$attr['locale'] = $translatorProfile['translation_locale'];
$result = $dbc->execute($q, $attr);
$translatedLocale = $dbc->fetch($result);
$translatedPercentage = round((($translatedLocale['count'] + $lockedTranslatedLocale['count']) / $defaultLockedCount) * 100, 2);

$title = moduleLiteral::get($moduleID, "lbl_translatedPercentage");
$translatedLiterals = DOM::create("p", " : ");
$numLocked = DOM::create("b", $translatedPercentage."%");
DOM::prepend($translatedLiterals, $title);
DOM::append($translatedLiterals, $numLocked);
$page->appendToSection("sidebar", $translatedLiterals);


// Create Literal Viewer
$literalViewer = DOM::create("div", "", "literalViewer");
$page->appendToSection("mainContent", $literalViewer);

// Return output
return $page->getReport();
//#section_end#
?>