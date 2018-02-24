<?php
//#section#[header]
// Module Declaration
$moduleID = 261;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Layout");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Apps");
importer::import("DEV", "WebEngine");
importer::import("DEV", "Core");
importer::import("DEV", "Modules");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\pages\sitemap;
use \API\Model\modules\module;
use \API\Profile\person;
use \API\Profile\account;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Layout\layoutManager;
use \UI\Modules\MContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \DEV\Projects\project;
use \DEV\Core\ajax\ajaxDirectory;
use \DEV\Core\sql\sqlDomain;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Modules\moduleManager;
use \DEV\Apps\application;
use \DEV\WebEngine\webManager;

if ($_SERVER['REQUEST_METHOD'] == "POST")
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
		$header = $errorNtf->addErrorHeader("err", $hd);
		$errorNtf->addErrorDescription($header, "errDesc", $errorNtf->getErrorMessage("err.authenticate"));
	}
	
	// Get project id
	$projectID = $_POST['id'];
	$project = new project($projectID);
	$projectInfo = $project->info();
	if (is_null($projectInfo))
		$hasError = TRUE;
	
	// If error, return report notification
	if ($hasError)
		return $errorNtf->getReport();
		
	// Create project release
	$status = $project->release($_POST['version'], $_POST['title'], $_POST['changelog']);
	if (!$status)
		return $errorNtf->getReport();
	
	// Get project online
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "update_project_on_off");
	$attr = array();
	$attr['pid'] = $projectID;
	$attr['status'] = 1;
	$dbc->execute($q, $attr);
	
	// Publish project according to project type
	switch ($projectInfo['projectType'])
	{
		case 1: // Publish Redback Core
			// Deploy SQL Library
			sqlDomain::publish();
			
			// Deploy SDK
			sdkLibrary::publish();
			
			// Publish ajax pages
			ajaxDirectory::publish();
			
			// Export layouts
			layoutManager::export();
			
			// Publish Media and Resources
			$project->publishResources("/Library/Media/c/", $innerFolder = "/media/");
			$project->publishResources("/System/Resources/SDK/", $innerFolder = "/resources/");
			
			break;
		case 2: // Publish Redback Modules
			moduleManager::publish();
			
			// Generate sitemap
			sitemap::generate();
			
			// Publish Resources
			$project->publishResources("/Library/Media/m/");
			
			break;
		case 3:
			// Publish Redback Web Engine SDK
			webManager::publish($_POST['version']);
			
			// Publish Resources
			$project->publishResources("/Library/Media/w/");			
			break;
		case 4:
			// Publish Application
			$app = new application($projectID);
			$app->publish($_POST['version']);
			break;
		case 5:
			// Publish Website
			break;
		case 6:
			// Publish Website Template
			break;
		case 7:
			// Publish Website Extension
			break;
	}
	
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
$title = moduleLiteral::get($moduleID, "lbl_releaseVersion");
$input = $form->getInput($type = "text", $name = "version", $value = $lateRelease['version'], $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Release changelog
$title = moduleLiteral::get($moduleID, "lbl_releaseChangelog");
$input = $form->getTextarea($name = "changelog", $value = "", $class = "", $autofocus = TRUE);
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