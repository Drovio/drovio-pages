<?php
//#section#[header]
// Module Declaration
$moduleID = 153;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Profile\translator as userTranslator;
use \API\Resources\geoloc\locale;
use \API\Resources\literals\moduleLiteral;
use \API\Security\account;
use \UI\Html\HTMLContent;
use \UI\Presentation\togglers\accordion;

use \API\Profile\user;

// Create HTML Content
$content = new HTMLContent();

// Get requested scope
$scope = $_GET['scope'];

// Build the module
$content->build("literalViewer_".$scope, "literalViewer");

// Build Translation Scope (Info) Header
$scopeHead = DOM::create('div', '', '', 'scopeHeader');
$content->append($scopeHead);

$currentScope = DOM::create("div");
$curScopeLabel = moduleLiteral::get($moduleID, "lbl_currentScope");
DOM::append($currentScope, $curScopeLabel);
$curScopeContent = DOM::create('span', " : ".$scope);
DOM::append($currentScope, $curScopeContent);
DOM::append($scopeHead, $currentScope);

// Get Translation locale
$translatorProfile = userTranslator::profile();
$translationLocale = $translatorProfile['translation_locale'];

// Build the accordion
$acc = new accordion();
$acc->build("literals_".$scope);
$accElement = $acc->get();
$content->append($accElement);


$dbc = new interDbConnection();

// Get scope literals
$dbq = new dbQuery("1493289297", "resources.literals");
$attr = array();
$attr['scope'] = $scope;
$result = $dbc->execute($dbq, $attr);
$scopeLiterals = $dbc->toArray($result, "id", "name");

// Get Default locked literals
$dbq = new dbQuery("1169740592", "resources.literals");
$attr = array();
$attr['scope'] = $scope;
$attr['locale'] = locale::getDefault();
$result = $dbc->execute($dbq, $attr);
$defaultLockedLiterals = $dbc->toArray($result, "id", "value");

// Get Translated locked literals
$dbq = new dbQuery("1169740592", "resources.literals");
$attr = array();
$attr['scope'] = $scope;
$attr['locale'] = $translationLocale;
$result = $dbc->execute($dbq, $attr);
$translatedLockedLiterals = $dbc->toArray($result, "id", "value");


foreach ($scopeLiterals as $ltKey => $ltName)
{
	// Create slice header
	$head = DOM::create("div", "", "", "literalHeader".(isset($translatedLockedLiterals[$ltKey]) ? " locked" : ""));
	
	// Create head content
	$headCnt = DOM::create("div", "", "", "content");
	DOM::append($head, $headCnt);
	// Create head indicators
	$headInd = DOM::create("div", "", "", "indicators");
	DOM::append($head, $headInd);	
	
	$ltVal = DOM::create("span", $defaultLockedLiterals[$ltKey], "", "literalVal");
	//DOM::append($head, $ltVal);
	DOM::append($headCnt, $ltVal);
	if (isset($translatedLockedLiterals[$ltKey]))
	{
		$ltSpan = DOM::create("span", " | ");
		//DOM::append($head, $ltSpan);
		DOM::append($headCnt, $ltSpan);
		$ltVal = DOM::create("b", $translatedLockedLiterals[$ltKey], "", "literalVal");
		//DOM::append($head, $ltVal);
		DOM::append($headCnt, $ltVal);
		
		$locked = DOM::create("span", "", "", "lockFlag");
		//DOM::append($head, $locked);
		DOM::append($headInd, $locked);
		
		// Create slice content
		$titleContent = moduleLiteral::get($moduleID, "lbl_literalTranslated");
		$slContent = DOM::create("p");
		DOM::append($slContent, $titleContent);
	}
	else
	{
		// Create slice content
		$attr = array();
		$attr['ltScope'] = $scope;
		$attr['ltName'] = $ltName;
		$slContent = $content->getModuleContainer($moduleID, $action = "literalTranslations", $attr, $startup = FALSE, $containerID = "tr_".$scope."_".$ltName);
		
		DOM::attr($head, "data-ref", $scope."_".$ltName);
		
		// Check Translation Status
		$dbq = new dbQuery("588698109", "resources.literals.translator");
		$attr = array();
		$attr['scope'] = $scope;
		$attr['name'] = $ltName;
		$attr['locale'] = $translationLocale;
		$attr['translator_id'] = account::getAccountID();
		$result = $dbc->execute($dbq, $attr);
		$translations = $dbc->toFullArray($result);
		$translationsResource = $dbc->toArray($result, "id", "value");
		$status = (count($translations) == 0 ? 'notTranslatedFlag' : "translatedFlag");
		$trStIndicator = DOM::create("span", "", "", $status);
		DOM::append($headInd, $trStIndicator);
		
	}
	
	// Add slice
	$acc->addSlice("lt_".$scope."_".$ltName, $head, $slContent, $selected = FALSE);
}

// Return output
return $content->getReport();
//#section_end#
?>