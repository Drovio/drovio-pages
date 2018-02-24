<?php
//#section#[header]
// Module Declaration
$moduleID = 191;

// Inner Module Codes
$innerModules = array();
$innerModules['developerHome'] = 100;

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
use \API\Profile\account;
use \UI\Modules\MPage;
use \DEV\Projects\project;
use \DEV\Resources\paths;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get account profile id and username
$accountID = engine::getVar('id');
$accountName = engine::getVar('name');
if (empty($accountID) && empty($accountName))
{
	// Redirect to proper url
	$accountName = account::getUsername();
	$accountID = account::getAccountID();
	if (!empty($accountName))
		$url = url::resolve("developer", "/profile/".$accountName);
	else
	{
		$params = array();
		$params['id'] = $accountID;
		$url = url::resolve("developer", "/profile/index.php", $params);
	}
	
	// Return redirect report
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Get account information
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_info");
$attr = array();
$attr['id'] = $accountID;
$attr['name'] = $accountName;
$result = $dbc->execute($q, $attr);
$accountInfo = $dbc->fetch($result);
$accountID = $accountInfo['accountID'];
if (empty($accountName) && !empty($accountInfo['accountName']))
{
	$accountName = $accountInfo['accountName'];
	$url = url::resolve("developer", "/profile/".$accountName);
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = FALSE);
}

// Build the page content
$page->build($accountInfo['accountTitle'], "devProfilePage", TRUE);

// Set account title
$accTitle = HTML::select(".devProfile .header .title")->item(0);
HTML::innerHTML($accTitle, $accountInfo['accountTitle']);


// Get account projects
$q = module::getQuery($moduleID, "get_account_projects");
$attr = array();
$attr['id'] = $accountID;
$result = $dbc->execute($q, $attr);
$projects = $dbc->fetch($result, TRUE);
$publicProjects = array();
foreach ($projects as $project)
	if ($project['open'])
		$publicProjects[] = $project;

$attr = array();
$attr['count'] = count($projects);
$title = moduleLiteral::get($moduleID, "lbl_project_count", $attr);
$pcountTitle = HTML::select(".devProfile .header .pcount")->item(0);
HTML::append($pcountTitle, $title);

$attr = array();
$attr['count'] = count($publicProjects);
$title = moduleLiteral::get($moduleID, "lbl_publicProjects", $attr);
$pcountTitle = HTML::select(".devProfile .portfolio .projects .hd")->item(0);
HTML::append($pcountTitle, $title);


// List all public projects
$projectContainer = HTML::select(".devProfile .portfolio .projects")->item(0);
foreach ($publicProjects as $projectInfo)
{
	// Build a project row
	$prjRow = HTML::create("div", "", "", "prjRow");
	HTML::append($projectContainer, $prjRow);
	
	// Project icon
	$prjIcon = HTML::create("div", "", "", "prjIcon");
	HTML::append($prjRow, $prjIcon);
	
	// Add icon (if any)
	$project = new project($projectInfo['id']);
	$projectIcon = $project->getResourcesFolder()."/.assets/icon.png";
	if (file_exists(systemRoot.$projectIcon))
	{
		// Resolve path
		$projectIcon = str_replace(paths::getRepositoryPath(), "", $projectIcon);
		$projectIcon = url::resolve("repo", $projectIcon);
		
		// Create image
		$img = DOM::create("img");
		DOM::attr($img, "src", $projectIcon);
		DOM::append($prjIcon, $img);
	}
	else
		HTML::addClass($prjIcon, "noIcon");
	
	// Project title
	if (empty($projectInfo['name']))
	{
		$params = array();
		$params['id'] = $projectInfo['id'];
		$href = url::resolve("open", "/projects/project.php", $params);
	}
	else
		$href = url::resolve("open", "/projects/".$projectInfo['name']);
	$prjTitle = $page->getWeblink($href, $content = $projectInfo['title'], $target = "_blank");
	HTML::addClass($prjTitle, "prjTitle");
	HTML::append($prjRow, $prjTitle);
	
	// Project description
	$prjDesc = HTML::create("div", $projectInfo['description'], "", "prjDesc");
	HTML::append($prjRow, $prjDesc);
}


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['developerHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$featuresPage = HTML::select(".devProfile")->item(0);
$footerMenu = module::loadView($innerModules['developerHome'], "footerMenu");
DOM::append($featuresPage, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>