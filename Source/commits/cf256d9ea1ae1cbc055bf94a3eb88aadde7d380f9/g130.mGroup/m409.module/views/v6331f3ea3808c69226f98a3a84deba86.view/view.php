<?php
//#section#[header]
// Module Declaration
$moduleID = 409;

// Inner Module Codes
$innerModules = array();
$innerModules['landingPage'] = 397;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Geoloc");
importer::import("API", "Profile");
importer::import("BSS", "WebDocs");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Profile\account;
use \API\Geoloc\datetimer;
use \BSS\WebDocs\wDoc;
use \BSS\WebDocs\wDocLibrary;
use \ESS\Environment\url;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$page->build("", "blogHomePageContainer", TRUE);

// Load navigation bar
$blogHomePage = HTML::select(".blogHomePage")->item(0);
$navbar = $page->loadView($innerModules['landingPage'], $viewName = "navigationBar");
DOM::prepend($blogHomePage, $navbar);

// Get all web docs from the public folder
$drovioTeamID = 6;
$blogFolder = "Blog/";
$docLib = new wDocLibrary($drovioTeamID);
$blog_docs = $docLib->getFolderDocs($parent = "/Public/".$blogFolder);

// Get blog posts and sort by time created
$blogPosts = array();
foreach ($blog_docs as $docName)
{
	// Initialize document
	$wdoc = new wDoc($blogFolder, $docName, $public = TRUE, $drovioTeamID);
	
	// Get document info and append to posts
	$docInfo = $wdoc->getDocumentInfo();
	if (!empty($docInfo))
		$blogPosts[$docName] = $docInfo;
}
uasort($blogPosts, "sort_blog_posts");

// List all blog posts
$blogContainer = HTML::select(".section.section-posts .middleContainer")->item(0);
foreach ($blogPosts as $postName => $postInfo)
{
	$blogTile = DOM::create("div", "", "", "btile");
	DOM::append($blogContainer, $blogTile);
	
	// icon
	$bicon = DOM::create("div", "", "", "bicon");
	DOM::append($blogTile, $bicon);
	
	// Format post url
	$parts = explode("_", $postName);
	$date = str_replace("-", "/", $parts[0]);
	$pTitle = $parts[1];
	
	// title
	$href = url::resolve("blog", $date."/".$pTitle);
	$btitle = $page->getWeblink($href, $postInfo['title'], $target = "_self", $moduleID = NULL, $viewName = "", $attr = array(), $class = "btitle");
	DOM::append($blogTile, $btitle);
	
	// subtitle
	$bsub = DOM::create("div", $postInfo['subtitle'], "", "bsub");
	DOM::append($blogTile, $bsub);
	
	// Author info and date
	$bfooter = DOM::create("div", "", "", "bfooter");
	DOM::append($blogTile, $bfooter);
	
	// Get account info
	$accountInfo = account::getInstance()->info($postInfo['author']['id']);
	if (!empty($accountInfo))
	{
		$bauthor = DOM::create("div", "", "", "bauthor");
		DOM::append($bfooter, $bauthor);
		
		$img = NULL;
		if (!empty($accountInfo['profile_image_url']))
		{
			$img = DOM::create("img");
			DOM::attr($img, "src", $accountInfo['profile_image_url']);
		}
		$bauthicon = DOM::create("div", $img, "", "bauthicon");
		DOM::append($bauthor, $bauthicon);
		
		$bauthtitle = DOM::create("div", $accountInfo['title'], "", "bauthtitle");
		DOM::append($bauthor, $bauthtitle);
	}
	
	// Date created
	$date = datetimer::live($postInfo['time_created'], "M d, Y");
	$bdate = DOM::create("div", $date, "", "bdate");
	DOM::append($bfooter, $bdate);
}

// Return output
return $page->getReport();

function sort_blog_posts($postA, $postB) {
	if ($postA['time_created'] == $postB['time_created']) {
		return 0;
	}
	return ($postA['time_created'] > $postB['time_created']) ? -1 : 1;
}
//#section_end#
?>