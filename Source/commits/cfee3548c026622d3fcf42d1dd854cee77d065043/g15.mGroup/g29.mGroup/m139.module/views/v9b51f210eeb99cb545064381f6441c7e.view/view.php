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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\geoloc\locale;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Html\HTMLContent;

// Create Module Page
$content = new HTMLContent();

// Build the module
$content->build("literalTranslations_".$literalName, "literalTranslations");

$literalId = $_GET['ltId'];
$literalDescription = $_GET['ltDesc'];
$literalScope = $_GET['ltScope'];
$literalName = $_GET['ltName'];

$reportHolder = "";

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$literalId = $_POST['ltId'];
	$literalDescription = $_POST['ltDesc'];
	$trLocale = $_POST['trLocale'];
	$literalScope = $_POST['ltScope'];
	$literalName = $_POST['ltName'];
	$trNewValue = $_POST['trValue'];
	
	if ($trLocale == locale::getDefault()) 
	{
		$formNtf = new formErrorNotification();
		$formNtf->build();
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_newTranslation");
		$err = $formNtf->addErrorHeader("lbl_newTranslation_h", $err_header);
		$err_desc = moduleLiteral::get($moduleID, "lbl_defaultEdit");
		$formNtf->addErrorDescription($err, "lbl_newTranslation_desc", $err_desc);
	} else if (!inputValidator::checkNotset($trLocale) && !inputValidator::checkNotset($literalScope) && !inputValidator::checkNotset($literalName) && !inputValidator::checkNotset($trNewValue))
	{
		// Update translation
		$dbc = new interDbConnection();
		$dbq = new dbQuery("1307209869", "resources.literals");
		$attr = array();
		$attr['scope'] = $literalScope;
		$attr['name'] = $literalName;
		$attr['desc'] = $literalDescription;
		$attr['value'] = $trNewValue;
		$attr['locale'] = $trLocale;
		$result = $dbc->execute($dbq, $attr);
		
		$formNtf = new formNotification();
		$formNtf->build($type = "success", $header = TRUE, $footer = FALSE, $timeout = TRUE);
		// Notification Message
		$message = $formNtf->getMessage("success", "success.save_success");
		$formNtf->append($message);
	}
	else
	{
		$formNtf = new formErrorNotification();
		$formNtf->build();
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_newTranslation");
		$err = $formNtf->addErrorHeader("lbl_newTranslation_h", $err_header);
		$formNtf->addErrorDescription($err, "lbl_newTranslation_desc", $formNtf->getErrorMessage("err.required"));
	}
	
	$content->append($formNtf->get());
	
	$reportHolder = "#tr_".$literalScope."_".$literalName;
	$reportHolder = str_replace(".", "\\.", $reportHolder);
}

// Get Literal Translations
$dbc = new interDbConnection();
$dbq = new dbQuery("606714136", "resources.literals");
$attr = array();
$attr['literal_id'] = $literalId;
$result = $dbc->execute($dbq, $attr);

// No translations
/*if ($dbc->get_num_rows($result) == 0) 
{
	$row = DOM::create("div", "", "", "untranslated");
	$content->append($row);
	
	// Not Translated
	$p = DOM::create("p");
	$msg = moduleLiteral::get($moduleID, "msg_untranslated");
	DOM::append($p, $msg);
	DOM::append($row, $p);
	
	// Return output
	return $content->getReport();
}*/

$translations = $dbc->toFullArray($result);

// Locale info
$dbc = new interDbConnection();
$dbq = new dbQuery("637187577", "resources.geoloc.locale");

//Show default locale
$row = DOM::create("div", "", "", "translationRow locked");
$content->append($row);

$attr = array();
$attr['locale'] = locale::getDefault();
$result = $dbc->execute($dbq, $attr);
$localeInfo = $dbc->fetch($result);

// Locale flag
$localeFlag = DOM::create("img", "", "", "translationFlag");
DOM::attr($localeFlag, "src", "/Library/Media/repository/geo/flags/".$localeInfo['imageName']);
DOM::attr($localeFlag, "title", $localeInfo['friendlyName']);
DOM::attr($localeFlag, "alt", $localeInfo['friendlyName']);
DOM::append($row, $localeFlag);

$value = DOM::create("span", $literalName, "", "translationValue");
DOM::append($row, $value);

foreach ($translations as $translation)
{
	$row = DOM::create("div", "", "", "translationRow");
	$content->append($row);
	
	$edit = DOM::create("div", "", "", "editTranslation");
	$editLabel = moduleLiteral::get($moduleID, "lbl_editTranslation", FALSE);
	DOM::attr($edit, "title", $editLabel);
	DOM::append($row, $edit);
	
	$attr = array();
	$attr['locale'] = $translation['locale'];
	$result = $dbc->execute($dbq, $attr);
	$localeInfo = $dbc->fetch($result);
	
	// Locale flag
	$localeFlag = DOM::create("img", "", "", "translationFlag");
	DOM::attr($localeFlag, "src", "/Library/Media/repository/geo/flags/".$localeInfo['imageName']);
	DOM::attr($localeFlag, "title", $localeInfo['friendlyName']);
	DOM::attr($localeFlag, "alt", $localeInfo['friendlyName']);
	DOM::append($row, $localeFlag);
	
	$value = DOM::create("span", $translation['value'], "", "translationValue");
	DOM::append($row, $value);
	
	// Build the edit form
	$tForm = new simpleForm();
	$translationForm = $tForm->build($moduleID, "literalTranslations")->get();
	DOM::append($row, $translationForm);
	
	// Hidden Literal Scope
	$input = $tForm->getInput($type = "hidden", $name = "ltId", $value = $literalId, $class = "", $autofocus = FALSE);
	$tForm->append($input);
	
	// Hidden Literal Description
	$input = $tForm->getInput($type = "hidden", $name = "ltDesc", $value = $literalDescription, $class = "", $autofocus = FALSE);
	$tForm->append($input);
	
	// Hidden Translation Locale
	$input = $tForm->getInput($type = "hidden", $name = "trLocale", $value = $translation['locale'], $class = "", $autofocus = FALSE);
	$tForm->append($input);
	
	// Hidden Literal Scope
	$input = $tForm->getInput($type = "hidden", $name = "ltScope", $value = $literalScope, $class = "", $autofocus = FALSE);
	$tForm->append($input);
	
	// Hidden Literal Name
	$input = $tForm->getInput($type = "hidden", $name = "ltName", $value = $literalName, $class = "", $autofocus = FALSE);
	$tForm->append($input);
	
	// Insert Translation input
	$title = moduleLiteral::get($moduleID, "lbl_newTranslation");
	$input = $tForm->getTextarea($name = "trValue", $translation['value'], $class = "");
	$tForm->insertRow($title, $input, $required = FALSE, $notes = "");
}

// Return output
return $content->getReport($reportHolder);
//#section_end#
?>