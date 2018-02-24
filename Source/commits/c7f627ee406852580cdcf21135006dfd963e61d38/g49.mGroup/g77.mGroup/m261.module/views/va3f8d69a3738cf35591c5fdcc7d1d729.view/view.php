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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("DEV", "Projects");
importer::import("DEV", "Version");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Geoloc\datetimer;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Presentation\dataGridList;
use \DEV\Version\vcs;
use \DEV\Projects\project;

// Get project ID
$projectID = engine::getVar('id');
$project = new project($projectID);
if (engine::isPost())
{
	// Set step number
	$step = 1;
	
	// Create Module Content
	$pageContent = new MContent($moduleID);
	
	// Build the module content
	$pageContent->build("", "repositoryPublisher");
	
	// Set step count
	$pageContent->addReportAction('prj.publisher.setStep', $step);
	
	// Add action to add status title
	$title = moduleLiteral::get($moduleID, "lbl_status_preparingPublish", array(), FALSE);
	$pageContent->addReportAction('prj.publisher.addStatusTitle', $title);
	
	// Get project online
	if (engine::getVar('take_online'))
	{
		$dbc = new dbConnection();
		$q = module::getQuery($moduleID, "update_project_on_off");
		$attr = array();
		$attr['pid'] = $projectID;
		$attr['status'] = 1;
		$dbc->execute($q, $attr);
	}
	
	// Get the user input that configures the publish process
	// Create form to start the source release process
	
	// Build Form
	$form = new simpleForm();
	$repositoryReleaseForm = $form->build("", FALSE)->engageModule($moduleID, "repositoryRelease")->get();
	$pageContent->append($repositoryReleaseForm);
	
	// Set website id
	$input = $form->getInput("hidden", "id", $projectID, "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected release title
	$input = $form->getInput("hidden", "title", engine::getVar("title"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set Release Version
	$input = $form->getInput("hidden", "version", engine::getVar("version"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set Release changelog
	$input = $form->getInput("hidden", "changelog", engine::getVar("changelog"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set package to release
	if (engine::getVar('repo_release') == 1)
	{
		// Set to create new repository release
		$input = $form->getInput("hidden", "new_repo_release", 1, "", FALSE, FALSE);
		$form->append($input);
		
		// Set repository release version
		$input = $form->getInput("hidden", "new_repo_version", engine::getVar("new_repo_version"), "", FALSE, FALSE);
		$form->append($input);
		
		// Set repository release branch
		$input = $form->getInput("hidden", "new_repo_branch", engine::getVar("new_repo_branch"), "", FALSE, FALSE);
		$form->append($input);
		
		// Set status title
		$statusTitle = moduleLiteral::get($moduleID, "lbl_status_releaseRepository", array(), FALSE);
	}
	else
	{
		// Set repository release package
		$input = $form->getInput("hidden", "repo_package", engine::getVar("repo_package"), "", FALSE, FALSE);
		$form->append($input);
		
		// Set status title
		$statusTitle = moduleLiteral::get($moduleID, "lbl_status_checkingRepository", array(), FALSE);
	}
	
	// Add action for status title
	$pageContent->addReportAction('prj.publisher.addStatusTitle', $statusTitle);
	
	// Set step ok and proceed to next form
	$pageContent->addReportAction("prj.publisher.stepOK", $step);
	
	// Return output
	return $pageContent->getReport(".projectPublisherContainer .formsHolder", "replace");
}


// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "projectPublisher", TRUE);

// Build Form
$form = new simpleForm();
$form->build()->engageModule($moduleID, "");

// Get publisher into the form
$publisher = HTML::select(".projectPublisherContainer .publisher")->item(0);
$form->append($publisher);

// Prepend form to wsPublisher
$wsPublisher = HTML::select(".projectPublisherContainer")->item(0);
DOM::append($wsPublisher, $form->get());
$mainForm = HTML::select(".projectPublisherContainer .mainForm")->item(0);

// Set project id
$input = $form->getInput("hidden", "id", $projectID, "", TRUE, TRUE);
$form->append($input);

// Release Title
$projectInfo = $project->info();
$title = moduleLiteral::get($moduleID, "lbl_releaseTitle");
$input = $form->getInput($type = "text", $name = "title", $value = $projectInfo['title'], $class = "", $autofocus = TRUE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($mainForm, $inputRow);

// Release version
$releases = $project->getReleases();
$lastRelease = $releases[0];
$currentVersion = $lastRelease['version'];
// Calculate next version
$versionParts = explode(".", $currentVersion);
$versionParts[count($versionParts)-1] = $versionParts[count($versionParts)-1]+1;
$nextVersion = implode(".", $versionParts);

$title = moduleLiteral::get($moduleID, "lbl_releaseVersion");
$attr = array();
$attr['version'] = $currentVersion;
$notes = moduleLiteral::get($moduleID, "lbl_releaseVersion_notes", $attr);
$input = $form->getInput($type = "text", $name = "version", $value = $nextVersion, $class = "", $autofocus = FALSE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes);
DOM::append($mainForm, $inputRow);

// Release Changelog
$title = moduleLiteral::get($moduleID, "lbl_releaseChangelog");
$notes = moduleLiteral::get($moduleID, "lbl_releaseChangelog_notes");
$input = $form->getTextArea("changelog", "", "", FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes);
DOM::append($mainForm, $inputRow);

// Take project online
$title = moduleLiteral::get($moduleID, "lbl_projectOnline");
$input = $form->getInput($type = "checkbox", $name = "take_online", $value = TRUE, $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes);
DOM::append($mainForm, $inputRow);


// Repository release parameters
$repositoryReleaseForm = HTML::select(".projectPublisherContainer .repoReleaseContainer .repoReleaseFormContainer")->item(0);

// New repository release
$title = moduleLiteral::get($moduleID, "lbl_existingRepository");
$input = $form->getInput($type = "radio", $name = "repo_release", $value = 0, $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes);
DOM::append($repositoryReleaseForm, $inputRow);

// Select repository release to publish
// Get releases
$vcs = new vcs($projectID);
$releases = $vcs->getReleases();
$repoPackagesResource = array();
foreach ($releases as $branchName => $branchData)
{
	$packages = $branchData['packages'];
	foreach ($packages as $packageData)
	{
		$latestRelease = reset($packageData['releases']);
		$repoPackagesResource[$packageData['version']] = $branchName.", ".$packageData['version']." - ".$latestRelease['title'];
	}
}
$title = moduleLiteral::get($moduleID, "lbl_repositoryPackage");
$input = $form->getResourceSelect($name = "repo_package", $multiple = FALSE, $class = "", $repoPackagesResource, $selectedValue = "");
DOM::attr($input, "disabled", TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($repositoryReleaseForm, $inputRow);

// New repository release
$title = moduleLiteral::get($moduleID, "lbl_releaseRepository");
$input = $form->getInput($type = "radio", $name = "repo_release", $value = 1, $class = "", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "checked", "checked");
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes);
DOM::append($repositoryReleaseForm, $inputRow);


// Create new release container
$newReleaseContainer = DOM::create("div", "", "", "newReleaseContainer");
DOM::append($repositoryReleaseForm, $newReleaseContainer);


// Repository release version
$relVersion = $releases['master']['current'];
$title = moduleLiteral::get($moduleID, "lbl_repositoryVersion");
$attr = array();
$attr['version'] = $releases['master']['current']."-".$releases['master']['packages']['v'.$relVersion]['build'];
$notes = moduleLiteral::get($moduleID, "lbl_repositoryVersion_notes", $attr);
$input = $form->getInput($type = "text", $name = "new_repo_version", $value = $nextVersion, $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes);
DOM::append($newReleaseContainer, $inputRow);

// Repository release branch
$branches = $vcs->getBranches();
$branchResource = array();
foreach ($branches as $branchName => $branchData)
	$branchResource[$branchName] = $branchName;
$title = moduleLiteral::get($moduleID, "lbl_repositoryBranch");
$input = $form->getResourceSelect($name = "new_repo_branch", $multiple = FALSE, $class = "", $branchResource, $selectedValue = "");
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($newReleaseContainer, $inputRow);



// Build frame
$frame = new windowFrame();
$title = moduleLiteral::get($moduleID, "title");
$frame->build($title, "projectPublisherFrame");
$frame->append($pageContent->get());
return $frame->getFrame();
//#section_end#
?>