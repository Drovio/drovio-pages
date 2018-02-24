<?php
//#section#[header]
// Module Declaration
$moduleID = 229;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \DEV\BugTracker\bugTracker;
use \UI\Modules\MContent;
use \UI\Presentation\frames\windowFrame;


// Build Frame
$wFrame = new windowFrame();
// Header
$title = "Bug Overview";//moduleLiteral::get($moduleID, "lbl_createWebsite", FALSE);
$wFrame->build($title, $class = "windowFrame");

// Create Module Page
$MContent = new MContent($moduleID);
$actionFactory = $MContent->getActionFactory();
// Build the module 
$MContent->build("", "bugDetails", TRUE);

// Create Content


//
$bugger = new bugTracker($_GET['pid']);
$issue = $bugger->getBug($_GET['bid']);

$title = HTML::select('.title')->item(0);
$title->nodeValue = $issue["title"];

$description = HTML::select('.description > .content pre')->item(0);
$description->nodeValue = $issue["description"];

$solveBugAction = HTML::select('.window-module.actions .solveBugBtnWrapper')->item(0);

$solution = HTML::select('.solution > .content')->item(0);
	$text = DOM::create('div', $issue["notes"]);
	if(empty($issue["notes"]))
	{
		$text = DOM::create('div', '', '', 'noSolution msg_noContent'); 
		$literal = moduleLiteral::get($moduleID, "ntf_noSolutionYet");
		DOM::append($text , $literal);
		
		$addSolutionContainer = DOM::create('div', '', '', 'solveBugForm noDisplay');
		$closeForm = DOM::create('span', '', '', 'closeFormBtn');
		DOM::append($addSolutionContainer, $closeForm);
		$solveBugForm = module::loadview($moduleID, "solveBug");
		DOM::append($addSolutionContainer, $solveBugForm);
		DOM::append($solution, $addSolutionContainer);
		
		HTML::removeClass($solveBugAction, 'noDisplay');
	}
	//$content = DOM::create('div', '', '', $class);
	//DOM::append($content, $text);
	DOM::append($solution, $text);

$comments = HTML::select('.comments > .content')->item(0);
	$text = DOM::create('div', $issue["comments"]);
	if(empty($issue["comments"]))
	{
		$text = DOM::create('div', '', '', 'noComments msg_noContent'); 
		$literal = moduleLiteral::get($moduleID, "ntf_commentNotSupportedYet");
		DOM::append($text, $literal);	
	}
	DOM::append($comments, $text);

$status = HTML::select('.window-module.info .content > .row.status .rowValue')->item(0);
$status->nodeValue = $issue['status'];

$severity = HTML::select('.window-module.info .content > .row.severity .rowValue')->item(0);
$severity->nodeValue = $issue['severity'];

$priority = HTML::select('.window-module.info .content > .row.priority .rowValue')->item(0);
$priority->nodeValue = $issue['priority'];

$user = HTML::select('.headerBar > .subTitle > .createdBy')->item(0);
$user->nodeValue = $issue['accountID'];

$created = HTML::select('.headerBar > .subTitle > .createdOn')->item(0);
$liveDate = datetimer::live($issue['date']);
DOM::append($created, $liveDate);

$updated = HTML::select('.window-module.info .content > .row.updated .rowValue')->item(0);
$liveDate = datetimer::live($issue['dateUpdated']);
DOM::append($updated, $liveDate);

$actionList = HTML::select('.window-module.actions .content')->item(0);
$changeBugStatus = module::loadview($moduleID, "changeBugStatus");
DOM::append($actionList, $changeBugStatus);

$wFrame->append($MContent->get()); 
// return
return  $wFrame->getFrame();
//#section_end#
?>