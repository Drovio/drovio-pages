<?php
//#section#[header]
// Module Declaration
$moduleID = 261;

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
importer::import("DEV", "Apps");
importer::import("DEV", "Core");
importer::import("DEV", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("DEV", "WebEngine");
importer::import("DEV", "Websites");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\person;
use \API\Profile\account;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Version\vcs;

use \DEV\Projects\project;
use \DEV\Core\coreProject;
use \DEV\Modules\modulesProject;
use \DEV\Apps\application;
use \DEV\Websites\website;
use \DEV\WebEngine\webCoreProject;

if (engine::isPost())
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	// Validate form
	if (!simpleForm::validate())
		$hasError = TRUE;
	
	// Authenticate account
	$username = person::getUsername();
	$password = $_POST['password'];
	if (!account::authenticate($username, $password))
	{
		$hasError = TRUE;
		$hd = moduleLiteral::get($moduleID, "authentication_error_header");
		$header = $errorNtf->addHeader($hd);
		$errorNtf->addDescription($header, $errorNtf->getErrorMessage("err.authenticate"));
	}
	
	// Get project id
	$projectID = engine::getVar('id');
	$project = new project($projectID);
	$projectInfo = $project->info();
	if (is_null($projectInfo))
		$hasError = TRUE;
	
	// If error, return report notification
	if ($hasError)
		return $errorNtf->getReport();
		
	// Create project release
	$branchName = engine::getVar('branch');
	$releaseVersion = engine::getVar("version");
	$status = $project->release($releaseVersion, $_POST['title'], $_POST['changelog']);
	if (!$status)
		return $errorNtf->getReport();
	
	// Publish project according to project type
	$releaseStatus = FALSE;
	switch ($projectInfo['projectType'])
	{
		case 1: // Publish Redback Core
			// Set red project release as published
			$dbc = new dbConnection();
			$q = module::getQuery($moduleID, "publish_red_project");
			$attr = array();
			$attr['pid'] = $projectID;
			$attr['version'] = $releaseVersion;
			$attr['time'] = time();
			$attr['comments'] = "[AUTO REVIEW RED PROJECT] OK"
			$dbc->execute($q, $attr);
			
			// Publish project
			$cProject = new coreProject();
			$releaseStatus = $cProject->publish($releaseVersion, $branchName);
			
			break;
		case 2: // Publish Redback Modules
			// Set red project release as published
			$dbc = new dbConnection();
			$q = module::getQuery($moduleID, "publish_red_project");
			$attr = array();
			$attr['pid'] = $projectID;
			$attr['version'] = $releaseVersion;
			$attr['time'] = time();
			$attr['comments'] = "[AUTO REVIEW RED PROJECT] OK"
			$dbc->execute($q, $attr);
			
			// Publish project
			$mProject = new modulesProject();
			$releaseStatus = $mProject->publish($releaseVersion, $branchName);
			
			break;
		case 3: // Publish Redback Web Engine SDK
			// Set red project release as published
			$dbc = new dbConnection();
			$q = module::getQuery($moduleID, "publish_red_project");
			$attr = array();
			$attr['pid'] = $projectID;
			$attr['version'] = $releaseVersion;
			$attr['time'] = time();
			$attr['comments'] = "[AUTO REVIEW RED PROJECT] OK"
			$dbc->execute($q, $attr);

			// Publish project
			$webCoreProject = new webCoreProject();		
			$releaseStatus = $webCoreProject->publish($releaseVersion, $branchName);
			
			break;
		case 4: // Publish Application
			$app = new application($projectID);
			$releaseStatus = $app->publish($releaseVersion, $branchName);
			
			break;
		case 5:	// Publish Website
			$website = new website($projectID);
			$releaseStatus = $website->publish($releaseVersion, $branchName);
			
			break;
		case 6:
			// Publish Website Template
			break;
		case 7:
			// Publish Website Extension
			break;
	}
	
	// Check release status and rollback if needed
	if (!$releaseStatus)
	{
		// Rollback
		$project->unrelease($releaseVersion);
		
		// Return error notification
		return $errorNtf->getReport();
	}
	
	// Get project online
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "update_project_on_off");
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['status'] = 1;
	$dbc->execute($q, $attr);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build pageContent
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "publisherWizard", TRUE);

// Get project id and name
$projectID = $_REQUEST['id'];

// Get project info
$project = new project($projectID);
$projectInfo = $project->info();
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Create form for new release
$publishFormContainer = HTML::select(".publisherWizardContainer .releaseFormContainer")->item(0);
$form = new simpleForm();
$publishForm = $form->build($moduleID, "", TRUE)->get();
DOM::append($publishFormContainer, $publishForm);

$releases = $project->getReleases();
$lateRelease = $releases[0];

// Hidden project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

// Release title (Project title as default)
$title = moduleLiteral::get($moduleID, "lbl_releaseTitle");
$input = $form->getInput($type = "text", $name = "title", $value = $projectTitle, $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Release version
$currentVersion = $lateRelease['version'];
// Calculate next version
$versionParts = explode(".", $currentVersion);
$versionParts[count($versionParts)-1] = $versionParts[count($versionParts)-1]+1;
$nextVersion = implode(".", $versionParts);

$title = moduleLiteral::get($moduleID, "lbl_releaseVersion");
$attr = array();
$attr['version'] = $currentVersion;
$notes = moduleLiteral::get($moduleID, "lbl_releaseVersion_notes", $attr);
$input = $form->getInput($type = "text", $name = "version", $value = $nextVersion, $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes);

// Release changelog
$title = moduleLiteral::get($moduleID, "lbl_releaseChangelog");
$input = $form->getTextarea($name = "changelog", $value = "", $class = "", $autofocus = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Source branch to release
$vcs = new vcs($projectID);
$branches = $vcs->getBranches();
$branchResource = array();
foreach ($branches as $branchName => $branchInfo)
	$branchResource[$branchName] = $branchName;
$workingBranch = $vcs->getWorkingBranch();
$title = moduleLiteral::get($moduleID, "lbl_sourceBranch");
$input = $form->getResourceSelect($name = "branch", $multiple = FALSE, $class = "", $branchResource, $selectedValue = $workingBranch);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Account authentication
$title = moduleLiteral::get($moduleID, "lbl_accountPassword");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Create form for getting project offline
$offlineFormContainer = HTML::select(".publisherWizardContainer .offlineFormContainer")->item(0);
$form = new simpleForm();
$offlineForm = $form->build($moduleID, "setOffline", TRUE)->get();
DOM::append($offlineFormContainer, $offlineForm);

// Hidden project id
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE, $required = TRUE);
$form->append($input);

// Account authentication
$title = moduleLiteral::get($moduleID, "lbl_accountPassword");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


// Create frame
$frame = new windowFrame();
$title = moduleLiteral::get($moduleID, "lbl_projectPublisherTitle");
$frame->build($title);

// Return the report
return $frame->append($pageContent->get())->getFrame();
//#section_end#
?>