<?php
//#section#[header]
// Module Declaration
$moduleID = 185;

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
importer::import("DEV", "Projects");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Security\accountKey;
use \API\Security\privileges;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Projects\project;

$pageContent = new MContent();
$actionFactory = $pageContent->getActionFactory();

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Project Title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_projectTitle");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check project name (if not empty)
	if (!empty($_POST['name']))
	{
		// Get projects by name
		$pr = new project("", $_POST['name']);
		$prInfo = $pr->info();
		if (!is_null($prInfo))
		{
			$has_error = TRUE;
		
			// Header
			$err_header = moduleLiteral::get($moduleID, "lbl_projectName");
			$err = $errFormNtf->addHeader($err_header);
			$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.exists"));
		}
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create project
	$project = new project();
	$projectID = $project->create($_POST['title'], $_POST['type'], $_POST['desc']);
	
	if (!$projectID)
	{
		// Error occurred
		$err_header = moduleLiteral::get($moduleID, "lbl_newProject");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating project..."));
		return $errFormNtf->getReport();
	}
	
	// Add account to project and add PROJECT_ADMIN accountKey
	$accountID = account::getAccountID();
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "add_account_to_project");
	$attr = array();
	$attr['aid'] = $accountID;
	$attr['pid'] = $projectID;
	$status = $dbc->execute($q, $attr);
	
	// Create account key for the given role
	privileges::addAccountToGroupID($accountID, 7);
	accountKey::create(7, 2, $projectID, $accountID);
	
	// If name not empty, set project name
	$projectName = $_POST['name'];
	if (!empty($projectName))
		$project->setName($projectName);
	
	// Redirect to project main page
	if (empty($projectName))
	{
		$params = array();
		$params['id'] = $projectID;
		$projectHomeUrl = url::resolve("developer", "/projects/project.php", $params);
	}
	else
		$projectHomeUrl = url::resolve("developer", "/projects/".$projectName."/");
	return $actionFactory->getReportRedirect($projectHomeUrl, "", TRUE);
}

// Create main form
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_newProject");
$frame->build($title, "", FALSE)->engageModule($moduleID, "projectWizard");
$form = $frame->getFormFactory();

// Project information page
$apps[] = $form->getOption("Application", 4, TRUE);
$appGroup = $form->getOptionGroup("App Engine", $apps);

$web[] = $form->getOption("Website", 5);
$web[] = $form->getOption("Web Template", 6);
$web[] = $form->getOption("Web Extension", 7);
$webGroup = $form->getOptionGroup("Web Engine", $web);

$input = $form->getSelect($name = "type", $multiple = FALSE, $class = "", $options = array());
DOM::append($input, $appGroup);
DOM::append($input, $webGroup);

$title = moduleLiteral::get($moduleID, "lbl_projectType");
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

$title = moduleLiteral::get($moduleID, "lbl_projectTitle");
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

$notes = moduleLiteral::get($moduleID, "lbl_projectName_notes");
$title = moduleLiteral::get($moduleID, "lbl_projectName");
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($inputRow);

$title = moduleLiteral::get($moduleID, "lbl_projectDesc");
$input = $form->getTextarea($name = "desc", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>