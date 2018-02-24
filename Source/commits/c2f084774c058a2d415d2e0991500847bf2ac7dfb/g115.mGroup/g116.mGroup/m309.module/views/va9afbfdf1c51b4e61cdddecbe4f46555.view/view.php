<?php
//#section#[header]
// Module Declaration
$moduleID = 309;

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
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("DEV", "Version");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Resources\paths;
use \DEV\Version\vcs;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];


// Build module page
$page->build($projectTitle, "openProjectPage", TRUE);


// Add icon (if any)
$imageBox = HTML::select(".info .logoBox .logo")->item(0);
$projectIcon = $project->getResourcesFolder()."/.assets/icon.png";
if (file_exists(systemRoot.$projectIcon))
{
	// Resolve path
	$projectIcon = str_replace(paths::getRepositoryPath(), "", $projectIcon);
	$projectIcon = url::resolve("repo", $projectIcon);
	
	// Create image
	$img = DOM::create("img");
	DOM::attr($img, "src", $projectIcon);
	DOM::append($imageBox, $img);
}
else
	HTML::addClass($imageBox, "noIcon");


// Project Title, name and Description
$pTitle = HTML::select(".projectTitle")->item(0);
DOM::innerHTML($pTitle, $projectTitle);

if (!empty($projectInfo['name']))
{
	$pName = HTML::create("span", "(".$projectInfo['name'].")", "", "projectName");
	DOM::append($pTitle, $pName);
}

$pDescription = HTML::select(".projectDescription")->item(0);
DOM::innerHTML($pDescription, $projectInfo['description']);

// Add author count
$vcs = new vcs($projectID);
$authors = $vcs->getAuthors();

$attr = array();
$attr['count'] = count($authors);
$title = moduleLiteral::get($moduleID, "lbl_authorCount", $attr);
$authorCount = HTML::select(".info .logoBox .authors")->item(0);
DOM::append($authorCount, $title);

/*
// Check if member of the project
if ($project->validate())
{
	$requestButton = HTML::select(".info .logoBox .request")->item(0);
	HTML::replace($requestButton, NULL);
}*/


$sections = array();
$sections[] = "repository";
$sections[] = "issues";
foreach ($sections as $section)
{
	// Set panel target group
	$panel = HTML::select(".panels #".$section)->item(0);
	NavigatorProtocol::selector($panel, "navGroup");
	
	// Set navigation item action
	$navItem = HTML::select(".navigation .navitem.".$section)->item(0);
	NavigatorProtocol::staticNav($navItem, $section, "sectionContainer", "navGroup", "navItemsGroup", $display = "none");
}


// Load repository main view
$content = module::loadView($moduleID, "repositoryMainView");
$container = HTML::select(".panels #repository")->item(0);
DOM::append($container, $content);

// Load issues main view
$content = module::loadView($moduleID, "issuesMainView");
$container = HTML::select(".panels #issues")->item(0);
DOM::append($container, $content);

// Return output
return $page->getReport();
//#section_end#
?>