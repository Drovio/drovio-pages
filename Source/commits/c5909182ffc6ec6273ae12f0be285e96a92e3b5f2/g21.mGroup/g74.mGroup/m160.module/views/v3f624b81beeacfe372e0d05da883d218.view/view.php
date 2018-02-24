<?php
//#section#[header]
// Module Declaration
$moduleID = 160;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\person;
use \API\Profile\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Modules\MContent;

// Build the content
$pageContent = new MContent($moduleID);
$pageContent->build("myPersonalInfo");

if (engine::isPost())
{
	$dbc = new dbConnection();
	// Updated Personal Information
	$q = module::getQuery($moduleID, "update_person_info");
	$attr = array();
	$attr['firstname'] = $_POST['firstname'];
	$attr['lastname'] = $_POST['lastname'];
	$attr['middle_name'] = $_POST['middle_name'];
	$attr['pid'] = account::getPersonID();
	$result = $dbc->execute($q, $attr);
	
	// Update Account Display Name (title)
	$complete = $_POST['firstname']." ".$_POST['middle_name']." ".$_POST['lastname'];
	$standard = $_POST['firstname']." ".$_POST['lastname'];
	$reversed = $_POST['lastname']." ".$_POST['firstname'];
	if (empty($_POST['middle_name']))
		$complete = $standard;
	switch ($_POST['display_name'])
	{
		case "complete":
			$title = $complete;
			break;
		case "standard":
			$title = $standard;
			break;
		case "reversed":
			$title = $reversed;
			break;
	}

	$q = module::getQuery($moduleID, "update_account_info");
	$attr = array();
	$attr['title'] = $title;
	$attr['aid'] = account::getAccountID();
	$result = $dbc->execute($q, $attr);
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}


// Get person's information
$person = person::info();
$accountInfo = account::info();


$sForm = new simpleForm();
$personalDataForm = $sForm->build($moduleID, "personalInfo")->get();
$pageContent->append($personalDataForm);

// Firstname
$title = moduleLiteral::get($moduleID, "lbl_personal_firstName");
$input = $sForm->getInput($type = "text", $name = "firstname", $value = $person['firstname'], $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Firstname
$title = moduleLiteral::get($moduleID, "lbl_personal_middleName");
$ph = moduleLiteral::get($moduleID, "lbl_optional", array(), FALSE);
$input = $sForm->getInput($type = "text", $name = "middle_name", $value = $person['middle_name'], $class = "", $autofocus = FALSE);
DOM::attr($input, "placeholder", $ph);
$sForm->insertRow($title, $input, $required = FALSE, $notes = "");

// Lastname
$title = moduleLiteral::get($moduleID, "lbl_personal_lastName");
$input = $sForm->getInput($type = "text", $name = "lastname", $value = $person['lastname'], $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Account display name
$accountTitle = $accountInfo['accountTitle'];
$title = moduleLiteral::get($moduleID, "lbl_account_displayName");
$complete = $person['firstname']." ".$person['middle_name']." ".$person['lastname'];
$standard = $person['firstname']." ".$person['lastname'];
$reversed = $person['lastname']." ".$person['firstname'];
if (empty($person['middle_name']))
		$complete = $standard;
$options = array();
$options[] = $sForm->getOption($complete, "complete", $accountTitle == $complete);
if (!empty($person['middle_name']))
	$options[] = $sForm->getOption($standard, "standard", $accountTitle == $standard);
$options[] = $sForm->getOption($reversed, "reversed", $accountTitle == $reversed);
$input = $sForm->getSelect($name = "display_name", $multiple = FALSE, $class = "", $options);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $pageContent->getReport();
//#section_end#
?>