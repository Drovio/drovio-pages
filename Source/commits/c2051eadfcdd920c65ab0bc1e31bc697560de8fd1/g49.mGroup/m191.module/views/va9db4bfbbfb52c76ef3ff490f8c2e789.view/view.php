<?php
//#section#[header]
// Module Declaration
$moduleID = 191;

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
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \DEV\Projects\project;
use \DEV\Resources\paths;

// Create Module Page
$pageContent = new MContent($moduleID);

// Get action factory
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "developerPublicProjects", TRUE);


// Get account profile id and username
$accountID = engine::getVar('id');
$accountName = engine::getVar('name');
// Get account information
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_info");
$attr = array();
$attr['id'] = $accountID;
$attr['name'] = $accountName;
$result = $dbc->execute($q, $attr);
$accountInfo = $dbc->fetch($result);
$accountID = $accountInfo['accountID'];


// Get account projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_projects");
$attr = array();
$attr['id'] = $accountID;
$result = $dbc->execute($q, $attr);
$projects = $dbc->fetch($result, TRUE);
$publicProjects = array();
foreach ($projects as $project)
	if ($project['open'] || $project['public'])
		$publicProjects[] = $project;


// List all public projects
$projectContainer = HTML::select(".portfolio .projects")->item(0);
foreach ($publicProjects as $projectInfo)
{
	// Build a project row
	$prjRow = HTML::create("div", "", "", "prjRow");
	HTML::append($projectContainer, $prjRow);
	
	// Add open project ribbon
	if ($projectInfo['open'])
		HTML::addClass($prjRow, "open");
	
	// Project icon
	$prjIcon = HTML::create("div", "", "", "prjIcon");
	HTML::append($prjRow, $prjIcon);
	
	// Add icon (if any)
	$project = new project($projectInfo['id']);
	$pInfo = $project->info();
	if (isset($pInfo['icon_url']))
	{
		// Add project image
		$img = DOM::create("img");
		DOM::attr($img, "src", $pInfo['icon_url']);
		DOM::append($prjIcon, $img);
	}
	else
		HTML::addClass($prjIcon, "noIcon");
	
	// Project title and weblink
	if (empty($projectInfo['name']))
	{
		$params = array();
		$params['id'] = $projectInfo['id'];
		if ($projectInfo['open'])
			$href = url::resolve("open", "/projects/project.php", $params);
		else
			$href = url::resolve("developer", "/public/project.php", $params);
	}
	else if ($projectInfo['open'])
		$href = url::resolve("open", "/projects/".$projectInfo['name']);
	else
		$href = url::resolve("developer", "/public/".$projectInfo['name']);
	$prjTitle = $pageContent->getWeblink($href, $content = $projectInfo['title'], $target = "_blank");
	HTML::addClass($prjTitle, "prjTitle");
	HTML::append($prjRow, $prjTitle);
	
	// Project description
	$prjDesc = HTML::create("div", $projectInfo['description'], "", "prjDesc");
	HTML::append($prjRow, $prjDesc);
}


// Return output
return $pageContent->getReport();
//#section_end#
?>