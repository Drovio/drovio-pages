<?php
//#section#[header]
// Module Declaration
$moduleID = 410;

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
use \API\Geoloc\locale;
use \BSS\WebDocs\wDoc;
use \BSS\WebDocs\wDocLibrary;
use \ESS\Environment\url;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get document name
$year = engine::getVar("y");
$month = engine::getVar("m");
$day = engine::getVar("d");
$docName = engine::getVar("name");
$docFullName = $year."-".$month.(empty($day) ? "" : "-".$day)."_".$docName;

// Get document
$drovioTeamID = 6;
$blogFolder = "Blog/";
$wdoc = new wDoc($blogFolder, $docFullName, $public = TRUE, $drovioTeamID);
$wdocInfo = $wdoc->getDocumentInfo();

// Build the module content
$page->build($wdocInfo['title'], "blogPostPageContainer", TRUE);

// Add open graph meta
$og = array();
$og['title'] = $wdocInfo['title'];
$og['site_name'] = "Drovio Blog";
$og['url'] = url::resolve("blog", $year."/".$month."/".$day."/".$docName);
$og['description'] = $wdocInfo['subtitle'];
$page->addOpenGraphMeta($og);

// Load navigation bar
$blogHomePage = HTML::select(".blogPostPage")->item(0);
$navbar = $page->loadView($innerModules['landingPage'], $viewName = "navigationBar");
DOM::prepend($blogHomePage, $navbar);


// Set document title and subtitle
$title = HTML::select(".section.section-home .banner__h2.btitle")->item(0);
HTML::innerHTML($title, $wdocInfo['title']);

$subtitle = HTML::select(".section.section-home .banner__intro.bsubtitle")->item(0);
HTML::innerHTML($subtitle, $wdocInfo['subtitle']);

// Get account info
$accountInfo = account::getInstance()->info($wdocInfo['author']['id']);
if (!empty($accountInfo))
{
	if (!empty($accountInfo['profile_image_url']))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $accountInfo['profile_image_url']);
		
		$bauthicon = HTML::select(".blog-info .bauthor .bauthicon")->item(0);
		DOM::append($bauthicon, $img);
	}

	$bauthtitle = HTML::select(".blog-info .bauthor .bauthtitle")->item(0);
	DOM::innerHTML($bauthtitle, $accountInfo['title']);
}

// Date created
$date = datetimer::live($wdocInfo['time_created'], "M d, Y");
$bdate = HTML::select(".blog-info .bdate")->item(0);
DOM::append($bdate, $date);


// Get doc container and append document
$docContainer = HTML::select(".section.section-blog-post .docContainer")->item(0);
$documentContent = $wdoc->load($locale = locale::get(), $public = TRUE, $drovioTeamID);
HTML::innerHTML($docContainer, $documentContent);

// Return output
return $page->getReport();
//#section_end#
?>