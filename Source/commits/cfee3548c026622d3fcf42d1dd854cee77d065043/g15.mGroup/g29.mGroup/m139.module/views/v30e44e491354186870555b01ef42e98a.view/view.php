<?php
//#section#[header]
// Module Declaration
$moduleID = 139;

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\geoloc\locale;
use \UI\Html\HTMLContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \UI\Presentation\togglers\toggler;

$literalScope = $_GET['scope'];

// Build HTMLContent
$content = new HTMLContent();
$content->build("translationsEditor");
$actionFactory = $content->getActionFactory();

$defaultLocale = locale::getDefault();

// Default locale info
$dbc = new interDbConnection();
$dbq = new dbQuery("637187577", "resources.geoloc.locale");
$attr = array();
$attr['locale'] = $defaultLocale;
$result = $dbc->execute($dbq, $attr);
$localeInfo = $dbc->fetch($result);

// Build Translation Scope (Info) Header
$scopeHead = DOM::create('div', '', '', 'scopeHeader');
$content->append($scopeHead);
$currentScope = DOM::create("div", "", "", "currentScope");

// Locale flag
$localeFlag = DOM::create("img", "", "", "localeFlag");
DOM::attr($localeFlag, "src", "/Library/Media/repository/geo/flags/".$localeInfo['imageName']);
DOM::attr($localeFlag, "title", $localeInfo['friendlyName']);
DOM::attr($localeFlag, "alt", $localeInfo['friendlyName']);
DOM::append($currentScope, $localeFlag);

$curScopeLabel = moduleLiteral::get($moduleID, "lbl_currentScope");
DOM::append($currentScope, $curScopeLabel);
$curScopeSeparator = DOM::create('span', ": ");
DOM::append($currentScope, $curScopeSeparator);
$curScopeContent = DOM::create('span', $literalScope);
DOM::append($currentScope, $curScopeContent);
DOM::append($scopeHead, $currentScope);

// Get Locked literals from default locale for current scope
$dbc = new interDbConnection();
$dbq = new dbQuery("1169740592", "resources.literals");

$attr = array();
$attr['scope'] = $literalScope;
$attr['locale'] = $defaultLocale;
$literals = $dbc->execute($dbq, $attr);

while ($literal = $dbc->fetch($literals))
{
	// Create Toggler
	$toggler = new toggler();
	
	// Header
	$headTitle = $literal['name'].(empty($literal['description']) ? "" : " - ".$literal['description']);
	$header = DOM::create("span", $headTitle);
	
	// Body
	$attr = array();
	$attr['ltId'] = $literal['id'];
	$attr['ltDesc'] = $literal['description'];
	$attr['ltScope'] = $literalScope;
	$attr['ltName'] = $literal['name'];
	$body = $content->getModuleContainer($moduleID, $action = "literalTranslations", $attr, $startup = FALSE, $containerID = "tr_".$literalScope."_".$literal['name']);
	
	$literalTog = $toggler->build($id = "lt.".$literal['id'], $header, $body, $open = FALSE)->get();
	DOM::attr($literalTog, "data-lt-ref", "tr_".$literalScope."_".$literal['name']);
	$content->append($literalTog);
}

// Return report
return $content->getReport();
//#section_end#
?>