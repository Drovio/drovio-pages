<?php
//#section#[header]
// Module Declaration
$moduleID = 252;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Literals");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \API\Profile\translator;
use \API\Security\account;
use \UI\Modules\MContent;
use \UI\Presentation\togglers\accordion;
use \DEV\Literals\literal;
use \DEV\Literals\translator as literalTranslator;
use \DEV\Literals\literalController;

// Get requested scope
$projectID = $_GET['pid'];
$scope = $_GET['scope'];

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactor = $pageContent->getActionFactory();

// Build the module
$translationsContainer = $pageContent->build("literalViewer_".$pid."_".$scope, "transLiterals")->get();

// Get Translation locale
$translatorProfile = translator::profile();
$translationLocale = $translatorProfile['translation_locale'];

// Get locked literals
$defaultLockedLiterals = literal::get($projectID, $scope, "", array(), FALSE, locale::getDefault());
$translatedLockedLiteralsAll = literalController::get($projectID, $scope, $translationLocale);
$translatedLockedLiterals = $translatedLockedLiteralsAll['translated'];


foreach ($defaultLockedLiterals as $ltKey => $ltName)
{
	// Create translation row
	$tRow = DOM::create("div", "", "", "tRow");
	DOM::append($translationsContainer, $tRow);
	
	// Add header
	$ltVal = DOM::create("span", $ltName, "", "ltVal");
	$tHead = DOM::create("div", $ltVal, "", "tHead");
	DOM::append($tRow, $tHead);
	$rgt = DOM::create("div", "", "", "rgt");
	DOM::append($tHead, $rgt);
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($rgt, $ico);
	$status = DOM::create("span", "", "", "status");
	DOM::append($rgt, $status);
	
	// Check if literal is locked
	if (isset($translatedLockedLiterals[$ltKey]))
	{
		$translation = DOM::create("b", " | ".$translatedLockedLiterals[$ltKey]);
		DOM::Append($tHead, $translation);
		
		// Add locked class and continue to next
		HTML::addClass($tRow, "locked");
		continue;
	}
	
	// Get translations
	$translations = literalTranslator::getTranslations($projectID, $scope, $ltKey);
	//print_r($translations);
	$attr['count'] = count($translations);
	$translationsStatus = moduleLiteral::get($moduleID, "lbl_translationStatus", $attr);
	DOM::append($status, $translationsStatus);
	
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['scope'] = $scope;
	$attr['name'] = $ltKey;
	$tID = str_replace(".", "_", "tr_".$scope."_".$ltKey);
	$tContent = $pageContent->getModuleContainer($moduleID, $action = "translations", $attr, $startup = FALSE, $containerID = $tID);
	HTML::addClass($tContent, "tContent");
	DOM::append($tRow, $tContent);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>