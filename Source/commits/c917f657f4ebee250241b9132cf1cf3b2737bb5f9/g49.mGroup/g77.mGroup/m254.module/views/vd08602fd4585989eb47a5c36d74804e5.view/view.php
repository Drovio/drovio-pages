<?php
//#section#[header]
// Module Declaration
$moduleID = 254;

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
importer::import("DEV", "Projects");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Developer\editors\HTMLEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\notification;
use \UI\Presentation\popups\popup;
use \DEV\Projects\project;
use \DEV\Projects\projectReadme;

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Initialize project read me document
$projectReadme = new projectReadme($project->getRootFolder(), TRUE);

if (engine::isPost())
{
	// Update project readme
	$status = $projectReadme->update($_POST['readme']);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		$reportNtf->build($type = notification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("success", "success.save_success");
	}
	else if ($status === FALSE)
	{
		$reportNtf->build($type = notification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("error", "err.save_error");
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	// Create popup
	$pp = new popup();
	$pp->fade(TRUE);
	$pp->timeout(TRUE);
	$pp->build($notification);
	
	return $pp->getReport();
}

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "projectReadmeEditor");

// Class Manual
$form = new simpleForm();
$readmeForm = $form->build("", FALSE)->engageModule($moduleID, "projectReadme")->get();
$pageContent->append($readmeForm);

// Project ID
$input = $form->getInput("hidden", "id", $projectID, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Container
$outerContainer = DOM::create("div", "", "readmeContainer", "outerContainer wDoc");
$form->append($outerContainer);

// Create Source Code Manager Toolbar
$objMgrToolbar = new navigationBar();
$toolbar = $objMgrToolbar->build($dock = navigationBar::TOP, $outerContainer)->get();
DOM::append($outerContainer, $toolbar);

// Save Tool
$saveTool = DOM::create("button", "", "", "docTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

$readmeContent = $projectReadme->get(locale::getDefault());
$editor = new HTMLEditor("", "readme");
$htmleditor = $editor->build($readmeContent, "", TRUE)->get();
DOM::append($outerContainer, $htmleditor);

// Return output
return $pageContent->getReport();
//#section_end#
?>