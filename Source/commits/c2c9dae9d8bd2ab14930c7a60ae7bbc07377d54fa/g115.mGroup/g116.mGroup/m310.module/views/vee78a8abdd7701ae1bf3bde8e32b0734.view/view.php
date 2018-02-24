<?php
//#section#[header]
// Module Declaration
$moduleID = 310;

// Inner Module Codes
$innerModules = array();
$innerModules['openPage'] = 308;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "openProjectsPage", TRUE);


// Get projects count
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_open_projects");
$result = $dbc->execute($q);
$projects = $dbc->fetch($result, TRUE);
$prjCount = count($projects);

// Set developers count
$attr = array();
$attr['count'] = ($prjCount < 10 ? $prjCount : floor($prjCount / 10) * 10);
$title = moduleLiteral::get($moduleID, "lbl_prj_count", $attr);
$mainTitle = HTML::select(".list .ctitle")->item(0);
HTML::append($mainTitle, $title);


// List all open projects
$devContainer = HTML::select(".openProjects .list .middleContainer")->item(0);
foreach ($projects as $project)
{
	// Build a project row
	$prjRow = HTML::create("div", "", "", "prjRow");
	HTML::append($devContainer, $prjRow);
	
	// Project icon
	$prjIcon = HTML::create("div", "", "", "prjIcon");
	HTML::append($prjRow, $prjIcon);
	
	// Add icon (if any)
	$version = projectLibrary::getLastProjectVersion($project['id']);
	$projectIcon = projectLibrary::getPublishedPath($project['id'], $version)."/resources/.assets/icon.png";
	if (file_exists(systemRoot.$projectIcon))
	{
		// Resolve path
		$projectIcon = str_replace(paths::getPublishedPath(), "", $projectIcon);
		$projectIcon = url::resolve("lib", $projectIcon);
		
		// Create image
		$img = DOM::create("img");
		DOM::attr($img, "src", $projectIcon);
		DOM::append($prjIcon, $img);
	}
	else
		HTML::addClass($prjIcon, "noIcon");
	
	// Project title
	$href = url::resolve("open", "/projects/project.php");
	$params = array();
	$params['id'] = $project['id'];
	$href = url::get($href, $params);
	$prjTitle = $page->getWeblink($href, $content = $project['title'], $target = "_blank");
	HTML::addClass($prjTitle, "prjTitle");
	HTML::append($prjRow, $prjTitle);
	
	// Project description
	$prjDesc = HTML::create("div", $project['description'], "", "prjDesc");
	HTML::append($prjRow, $prjDesc);
}

// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['openPage'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$frontendPage = HTML::select(".openProjects")->item(0);
$footerMenu = module::loadView($innerModules['openPage'], "footerMenu");
DOM::append($frontendPage, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>