<?php
//#section#[header]
// Module Declaration
$moduleID = 352;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("BSS", "WebDocs");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MPage;
use \UI\Presentation\notification;
use \BSS\WebDocs\wDoc;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get name parameter
$pName = engine::getVar('name');

// Build the page content
$title = moduleLiteral::get($moduleID, "title_".$pName, array(), FALSE);
$page->build($title, "featurePage", TRUE);

// Add class to feature container
$featureContainer = HTML::select(".featureContainer")->item(0);
HTML::addClass($featureContainer, $pName);

// Set document names
$docs = array();
$docs['spa'] = array("doc" => "Developer/Features:SinglePageApplication", "url" => "apps");
$docs['localization'] = array("doc" => "Developer/Features:Localization", "url" => "literals_api");
$docs['mvc'] = array("doc" => "Developer/Features:ModelViewController", "url" => "mvc");
$docs['rap'] = array("doc" => "Developer/Features:RAPProtocol", "url" => "rap");
$docs['vcs'] = array("doc" => "Developer/Features:VersionControl", "url" => "version");


$docParts = explode(":", $docs[$pName]['doc']);
$docDirectory = $docParts[0];
$docName = $docParts[1];

// Load document
$wDoc = new wDoc($docDirectory, $docName, $public = TRUE, $teamID = 6);
$documentContent = $wDoc->load($locale = locale::get());

$docViewer = HTML::select(".featureContent .doc_viewer")->item(0);
if (!empty($documentContent))
{
	DOM::innerHTML($docViewer, $documentContent);
}
else
{
	// Notification, document not found
	$ntf = new notification();
	$notification = $ntf->build(notification::ERROR, $header = TRUE, $disposable = FALSE)->get();
	
	$prompt = moduleLiteral::get($moduleID, "hd_docNotFound");
	$hd = DOM::create("h2", $prompt);
	$ntf->append($hd);
	
	$context = moduleLiteral::get($moduleID, "lbl_documentError");
	$context = DOM::create("p", $context);
	$ntf->append($context);
	
	DOM::append($docViewer, $notification);
}

// Set the document url
$urlName = $docs[$pName]['url'];
$href = url::resolve("developer", "/docs/".$urlName);
$weblink = HTML::select(".featureContainer .featureContent a")->item(0);
HTML::attr($weblink, "href", $href);


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['developerHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$featuresPage = HTML::select(".featureContainer")->item(0);
$footerMenu = module::loadView($innerModules['developerHome'], "footerMenu");
DOM::append($featuresPage, $footerMenu);

// Return output
return $page->getReport();
//#section_end#
?>