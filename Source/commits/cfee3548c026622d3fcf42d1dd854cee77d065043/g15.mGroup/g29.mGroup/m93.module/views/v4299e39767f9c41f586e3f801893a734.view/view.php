<?php
//#section#[header]
// Module Declaration
$moduleID = 93;

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
importer::import("UI", "Presentation");
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

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get literal ref
	$literalName = $_POST['name'];
	$literalScope = $_POST['scope'];
	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Library Name
	$empty = (is_null($_POST['value']) || empty($_POST['value']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_literalValue");
		$err = $errFormNtf->addErrorHeader("lbl_literalValue_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_literalValue_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	
	// Update Literal
	$status = literal::update($literalScope, $literalName, $_POST['value'], $_POST['description']);

	// If there is an error in creating the library, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_literalValue");
		$err = $errFormNtf->addErrorHeader("lbl_literalValue_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lbl_literalValue_desc", DOM::create("span", "Error updating literal..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}
else
	$literalScope = $_GET['scope'];


// Build HTMLContent
$content = new HTMLContent();
$content->build("literalEditor");
$actionFactory = $content->getActionFactory();

// Build Translation Scope (Info) Header
$scopeHead = DOM::create('div', '', '', 'scopeHeader');
$content->append($scopeHead);

$currentScope = DOM::create("div", "", "", "currentScope");
$curScopeLabel = moduleLiteral::get($moduleID, "lbl_currentScope");
DOM::append($currentScope, $curScopeLabel);
$curScopeContent = DOM::create('span', " : ".$literalScope);
DOM::append($currentScope, $curScopeContent);
DOM::append($scopeHead, $currentScope);

$createNewLiteral = DOM::create("a", "", "", "newLiteral");
DOM::append($scopeHead, $createNewLiteral);
$newLiteralContent = moduleLiteral::get($moduleID, "lbl_createNewLiteral");
DOM::append($createNewLiteral, $newLiteralContent);
$attr = array();
$attr['scope'] = $literalScope;
$actionFactory->setPopupAction($createNewLiteral, $moduleID, "createNewLiteral", $attr);

$dbc = new interDbConnection();
$dbq = new dbQuery("1169740592", "resources.literals");

$attr = array();
$attr['scope'] = $literalScope;
$attr['locale'] = locale::_default();
$literals = $dbc->execute($dbq, $attr);

while ($literal = $dbc->fetch($literals))
{
	// Create Toggler
	$toggler = new toggler();
	
	// Header
	$headTitle = $literal['name'].(empty($literal['description']) ? "" : " - ".$literal['description']);
	$header = DOM::create("span", $headTitle);
	
	// Body
	$ltForm = new simpleForm("editLiteral_".$literal['id']);
	$literalEditorForm = $ltForm->build($moduleID, "literalEditor")->get();
	
	// Hidden Scope Value
	$input = $ltForm->getInput($type = "hidden", $name = "scope", $literal['scope'], $class = "", $autofocus = FALSE);
	$ltForm->append($input);
	
	// Hidden Literal ID Value
	$input = $ltForm->getInput($type = "hidden", $name = "name", $literal['name'], $class = "", $autofocus = FALSE);
	$ltForm->append($input);
	
	// Literal description
	$title = moduleLiteral::get($moduleID, "lbl_literalDescription");
	$input = $ltForm->getTextarea($name = "description", $value = $literal['description'], $class = "", $autofocus = FALSE);
	$ltForm->insertRow($title, $input, $required = FALSE, $notes = "");
	
	// Literal value lbl_literalValue
	$title = moduleLiteral::get($moduleID, "lbl_literalValue");
	$input = $ltForm->getTextarea($name = "value", $value = $literal['value'], $class = "", $autofocus = FALSE);
	$ltForm->insertRow($title, $input, $required = TRUE, $notes = "");
	
	
	$literalTog = $toggler->build($id = "lt.".$literal['id'], $header, $literalEditorForm, $open = FALSE)->get();
	$content->append($literalTog);
}

// Return report
return $content->getReport();
//#section_end#
?>