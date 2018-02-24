<?php
//#section#[header]
// Module Declaration
$moduleID = 361;

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
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("DEV", "Projects");
importer::import("DEV", "Websites");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Profile\account;
use \API\Resources\filesystem\directory;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;
use \DEV\Websites\website;
use \DEV\Websites\pages\wsPageManager;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new website($projectID, $projectName);
$projectInfo = $project->info();

// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Build the page
$page->build("", "websitePreviewContainer", TRUE);
$controlPanel = HTML::select(".testingControlPanel")->item(0);

// Form Control
$form = new simpleForm();
$controlForm = $form->build("", FALSE)->engageModule($moduleID, "previewPage")->get();
DOM::append($controlPanel, $controlForm);

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE);
$form->append($input);

$frow = DOM::create("div", "", "", "frow");
$form->append($frow);

// Folder Parent
$pageResources = array();
$pman = new wsPageManager($projectID);
$websiteFolders[] = "/";
$allFolders = $pman->getFolders("", TRUE);
$websiteFolders = array_merge($websiteFolders, $allFolders);
foreach ($websiteFolders as $fl)
{
	// Get pages
	$pages = $pman->getFolderPages($fl);
	foreach ($pages as $pageName)
		$pageResources[$pageName] = directory::normalize($fl."/".$pageName.".page");
}

$moduleInput = $form->getResourceSelect($name = "page", $multiple = FALSE, $class = "tinp", $pageResources, "");
DOM::append($frow, $moduleInput);

// Preview button
$submitButton = $form->getSubmitButton("Preview");
HTML::addClass($submitButton, "wbutton");
$form->append($submitButton);


// Return output
return $page->getReport($_GET['holder'], FALSE);
//#section_end#
?>