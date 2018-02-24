<?php
//#section#[header]
// Module Declaration
$moduleID = 175;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\appcenter\application;
use \API\Developer\appcenter\appManager;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\geoloc\locale;
use \API\Resources\forms\inputValidator;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;

// Create Module Page
$pageContent = new HTMLContent();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Initialize application
	$appID = $_POST['appID'];
	$devApp = new application($appID);
	$appLiterals = $devApp->getLiterals();
	
	// Update application settings
	$literals = $_POST['literal'];
	foreach ($literals as $name => $value)
	{
		if ($_POST['delete'][$name] == "on")
			$value = "";
		
		// Set literal value
		$appLiterals->create($_POST['locale']);
		$appLiterals->set($name, $value, $_POST['locale']);
	}
	
	$result = TRUE;
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_libraryName");
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error updating literals..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the module
$pageContent->build("literal_".$_GET['locale']);

// Validate and Load application data
$appID = $_GET['appID'];
$applicationData = appManager::getApplicationData($appID);
if (is_null($applicationData))
{
	// Error Message
	$errorMessage = DOM::create("h2", "Application request not valid");
	$pageContent->append($errorMessage);
	return $pageContent->getReport();
}

// Create application and get literal manager
$devApp = new application($appID);
$appLiterals = $devApp->getLiterals();

// Translate title (if different locale)
if ($_GET['locale'] != $appLiterals->getDefaultLocale())
{
	$translatorHeader = DOM::create("h4", "Translate your application.");
	$pageContent->append($translatorHeader);
}


$form = new simpleForm();
$litForm = $form->build($moduleID, "literalEditor", FALSE)->get();
$pageContent->append($litForm);

// Application id
$input = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
$form->append($input);

// Locale
$input = $form->getInput($type = "hidden", $name = "locale", $value = $_GET['locale'], $class = "", $autofocus = FALSE);
$form->append($input);

// literal list
$gridList = new dataGridList();
$literalList = $gridList->build("", TRUE)->get();
$form->append($literalList);

// Set List Headers
$headers = array();
$headers[] = "Name";
$headers[] = "Value";
$gridList->setHeaders($headers);

$defaultLiterals = $appLiterals->get();
$literals = $appLiterals->get("", $_GET['locale']);
foreach ($defaultLiterals as $name => $value)
{
	$gridRow = array();
	
	// Literal Name Label
	$label = DOM::create("span", $name);
	$gridRow[] = $label;
	
	// Literal value
	$value = $literals[$name];
	$input = $form->getInput($type = "text", "literal[".$name."]", $value, $class = "", $autofocus = FALSE, $required = FALSE);
	$gridRow[] = $input;
	
	// Insert Grid Row
	$gridList->insertRow($gridRow, "delete[".$name."]");
}


// Delete message and submit button
if (count($defaultLiterals) > 0)
{
	$message = DOM::create("p", "Click on the checkbox to select the literals you wish to delete.");
	$form->append($message);
	
	$title = literal::dictionary("save");
	$submitBtn = $form->getSubmitButton($title, $id = "applyLiteralChanges");
	$form->append($submitBtn);
}
else
{
	$message = DOM::create("p", "There are no literals yet. Use the form below to add a new.");
	$form->append($message);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>