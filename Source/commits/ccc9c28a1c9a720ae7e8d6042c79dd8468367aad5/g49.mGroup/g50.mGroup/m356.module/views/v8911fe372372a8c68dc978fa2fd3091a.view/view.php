<?php
//#section#[header]
// Module Declaration
$moduleID = 356;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("BSS", "WebDocs");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\notification;
use \BSS\WebDocs\wDoc;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "tutorialViewerContainer", TRUE);

// Load function directory
if (!function_exists("getAllTutorials"))
	$pageContent->loadView($moduleID, "tutorialsDirectory");

// Get document name
$docName = engine::getVar("doc");

// Load document
$wDoc = new wDoc("/Developer/Tutorials/", $docName, $public = TRUE, $teamID = 6);
$documentContent = $wDoc->load($locale = locale::get());

$docViewer = HTML::select(".tutorialViewer .docHolder")->item(0);
if (!empty($documentContent))
{
	// Set document
	DOM::innerHTML($docViewer, $documentContent);
	
	// Get previous and next documents
	$allTutorials = getAllTutorials();
	$allTCount = count($allTutorials);
	$tIndex = array_keys($allTutorials);
	$currentIndex = array_search($tIndex, $docName);
	$previousIndex = ($currentIndex - 1 < 0 ? NULL : $currentIndex - 1);
	$nextIndex = ($currentIndex + 1 >= $allTCount ? NULL : $currentIndex + 1);
	
	// Get previous and next documents
	$previousDocName = $tIndex[$previousIndex];
	$previousButton = HTML::selecT(".tutorialViewer .wbutton.previous")->item(0);
	if (!empty($previousDocName))
	{
		// Set title
		$title = "&#x2190; ".$allTutorials[$previousDocName];
		HTML::innerHTML($previousButton, $title);
		$url = url::resolve("developers", "/tutorials/".$previousDocName);
		DOM::attr($previousButton, "href", $url);
		
		// Set action
		$attr = array();
		$attr['doc'] = $previousDocName;
		$actionFactory->setModuleAction($previousButton, $moduleID, "tutorialViewer", "", $attr);
	}
	else
		HTML::replace($previousButton, NULL);
	
	$nextDocName = $tIndex[$nextIndex];
	$nextButton = HTML::selecT(".tutorialViewer .wbutton.next")->item(0);
	if (!empty($nextDocName))
	{
		// Set title
		$title = $allTutorials[$nextDocName]." &#x2192;";
		HTML::innerHTML($nextButton, $title);
		$url = url::resolve("developers", "/tutorials/".$nextDocName);
		DOM::attr($nextButton, "href", $url);
		
		// Set action
		$attr = array();
		$attr['doc'] = $nextDocName;
		$actionFactory->setModuleAction($nextButton, $moduleID, "tutorialViewer", "", $attr);
	}
	else
		HTML::replace($nextButton, NULL);
}
else
{
	// Remove navigation buttons
	$previousButton = HTML::selecT(".tutorialViewer .wbutton.previous")->item(0);
	HTML::replace($previousButton, NULL);
	$nextButton = HTML::selecT(".tutorialViewer .wbutton.next")->item(0);
	HTML::replace($nextButton, NULL);
	
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

// Set back button action
$homeButton = HTML::selecT(".tutorialViewer .wbutton.home")->item(0);
$actionFactory->setModuleAction($homeButton, $moduleID, "tutorialsHome");


// Return output
return $pageContent->getReport(".docTutorials .tutorialContainer");
//#section_end#
?>