<?php
//#section#[header]
// Module Declaration
$moduleID = 193;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Developer\misc\platformStatus;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\gridView;


// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "pageTitle", FALSE);
$HTMLModulePage = new HTMLModulePage("OneColumnCentered");
$HTMLModulePage->build($pageTitle, "devPrograms");


// Page Banner 
$banner = DOM::create('div', '', '', 'banner');
$HTMLModulePage->appendToSection("mainContent", $banner);

$bannerText = DOM::create('div', '', '', 'content');
DOM::append($banner, $bannerText);

// Page title
$headerWrapper = DOM::create('div', '', '', 'headerWrapper');
$header = DOM::create("h1", moduleLiteral::get($moduleID, "lbl_pageHeader"));
DOM::append($headerWrapper, $header);
DOM::append($bannerText, $headerWrapper);
$title = moduleLiteral::get($moduleID, "lbl_pageSubtitle");
$header = DOM::create("h3", $title);
DOM::append($bannerText, $header);
/*
$title =  moduleLiteral::get($moduleID, "text");
$header = DOM::create("p", $title);
DOM::append($bannerText, $header);
*/

// Page Body
$pContainer = DOM::create("div", "", "", "bodyWrapper");
$HTMLModulePage->appendToSection("mainContent", $pContainer);

// Status Row 
$statusRow = DOM::create('div', '', '', 'smallRow');
DOM::append($pContainer, $statusRow);
$title = DOM::create('span', 'Status :');
DOM::append($statusRow, $title);
$platformStatus = new platformStatus();
$status = DOM::create('span', $platformStatus->getStatus());
DOM::append($statusRow, $status);

// Body Columns Wrapper
$columnWrapper = DOM::create('div', '', '', 'columnWrapper');
DOM::append($pContainer, $columnWrapper);

// Blog wrapper
$gridSecWrapper = DOM::create('div', '', '', 'box medium');
DOM::append($columnWrapper, $gridSecWrapper);
$upperContent = DOM::create('div', '', '', 'upperContent');
DOM::append($gridSecWrapper, $upperContent );
$title = moduleLiteral::get($moduleID, "lbl_blogHeader");
$header = DOM::create("div", $title, '', 'header');
DOM::append($gridSecWrapper, $header);

$blogBody = DOM::create('div', '', '', 'mainContent');
DOM::append($gridSecWrapper, $blogBody);
//get blog posts
$blogPosts = array();

if(count($blogPosts) > 0)
{
	$post = DOM::create("div");
	DOM::append($blogBody, $post);
	$postTitle = DOM::create("div");
	DOM::append($post, $postTitle);
	$postFooter = DOM::create("div");
	DOM::append($post, $postFooter);
	$postDate = DOM::create("div");
	DOM::append($postFooter, $postDate);
	$postAuthor = DOM::create("div");
	DOM::append($postFooter, $postAuthor);
}
else
{
	$msg = moduleLiteral::get($moduleID, "msg_blogNoPosts");
	DOM::append($blogBody, $msg);
}



// Docs wrapper
$gridSecWrapper = DOM::create('div', '', '', 'box small');
DOM::append($columnWrapper, $gridSecWrapper);
$upperContent = DOM::create('div', '', '', 'upperContent');
DOM::append($gridSecWrapper, $upperContent );
$title = moduleLiteral::get($moduleID, "lbl_docsHeader");
$header = DOM::create("div", $title, '', 'header');
DOM::append($gridSecWrapper, $header);
$boxBody = DOM::create('div', '', '', 'mainContent');
DOM::append($gridSecWrapper, $boxBody);
$ul = DOM::create('ul');
DOM::append($boxBody , $ul);
$documentationItem = DOM::create('li');
$title = moduleLiteral::get($moduleID, "lbl_documentation");
DOM::append($documentationItem, $title);
DOM::append($ul, $documentationItem);

$documentantionUl  = DOM::create('ul');
DOM::append($documentationItem , $documentantionUl);
// Application Documentation
$item = DOM::create('li');
$title = moduleLiteral::get($moduleID, "lbl_appDeveloper");
$link = $HTMLModulePage->getWeblink(url::resolve("developer", "/docs/appCenter/"), $title, "_self");
DOM::append($item, $link);
DOM::append($documentantionUl , $item);
// Website Documentation
$item = DOM::create('li');
$title = moduleLiteral::get($moduleID, "lbl_wDeveloper");
$link = $HTMLModulePage->getWeblink(url::resolve("developer", "/docs/ebuilder/"), $title, "_self");
DOM::append($item, $link);
DOM::append($documentantionUl , $item);

$quidesItem = DOM::create('li');
$title = moduleLiteral::get($moduleID, "lbl_guides");
DOM::append($quidesItem , $title);
DOM::append($ul, $quidesItem );

// Tools wrapper
$gridSecWrapper = DOM::create('div', '', '', 'box small');
DOM::append($columnWrapper, $gridSecWrapper);
$upperContent = DOM::create('div', '', '', 'upperContent');
DOM::append($gridSecWrapper, $upperContent );
$title = moduleLiteral::get($moduleID, "lbl_toolsHeader");
$header = DOM::create("div", $title, '', 'header');
DOM::append($gridSecWrapper, $header);
$boxBody = DOM::create('div', '', '', 'mainContent');
DOM::append($gridSecWrapper, $boxBody);

return $HTMLModulePage->getReport();
//#section_end#
?>