<?php
//#section#[header]
// Module Declaration
$moduleID = 315;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \SYS\Resources\settings\dbSettings;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Server Host
	if (empty($_POST['host']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_serverHost");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Database name
	if (empty($_POST['dbname']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_serverDbName");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check Username
	if (empty($_POST['username']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_serverUsername");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check password
	if (empty($_POST['password']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_serverPassword");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check dbms
	if (empty($_POST['dbms']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_serverDbms");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create server
	$serverName = engine::getVar("name");
	$dbMan = new dbSettings($serverName);
	$status = $dbMan->updateServer($_POST['host'], $_POST['dbname'], $_POST['username'], $_POST['password'], $_POST['dbms']);
	
	// If there is an error in creating the module group, show it
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "hd_editServer");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error updating database server..."));
		return $errFormNtf->getReport();
	}
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the frame
$pageContent = new MContent($moduleID);
$pageContent->build("", "serverEditor");

// Initialize server
$serverName = engine::getVar("name");
$dbMan = new dbSettings($serverName);
$settings = $dbMan->get();

// Create form
$form = new simpleForm();
$serverForm = $form->build()->engageModule($moduleID, "editServer")->get();
$pageContent->append($serverForm);

$title = moduleLiteral::get($moduleID, "hd_editServer");
$hd = HTML::create("h3", $title);
$form->append($hd);

$input = $form->getInput($type = "hidden", $name = "name", $value = $serverName, $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

// Server host
$title = moduleLiteral::get($moduleID, "lbl_serverHost");
$input = $form->getInput($type = "text", $name = "host", $value = $settings['SERVER_URL'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Server database name
$title = moduleLiteral::get($moduleID, "lbl_serverDbName");
$input = $form->getInput($type = "text", $name = "dbname", $value = $settings['DB_NAME'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Server username
$title = moduleLiteral::get($moduleID, "lbl_serverUsername");
$input = $form->getInput($type = "text", $name = "username", $value = $settings['USERNAME'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_serverPassword");
$input = $form->getInput($type = "password", $name = "password", $value = $settings['PASSWORD'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = moduleLiteral::get($moduleID, "lbl_serverDbms");
$dbmsList = array();
$dbmsList['MySQL'] = "MySQL";
$input = $form->getResourceSelect($name = "dbms", $multiple = FALSE, $class = "", $dbmsList, $selectedValue = $settings['SERVER_DBMS']);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return the report
return $pageContent->getReport(".dbServers .srvEditor");
//#section_end#
?>