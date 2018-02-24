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
importer::import("API", "Security");
importer::import("UI", "Modules");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
use \API\Literals\moduleLiteral;
use \DEV\BugTracker\bugTracker;
use \UI\Modules\MContent;
use \API\Security\account;

// Create Module Page
$MContent = new MContent($moduleID);
$actionFactory = $MContent->getActionFactory();
// Build the module 
$MContent->build("", "", TRUE);

$filterBy = empty($_REQUEST['filterBy']) ? '' : $_REQUEST['filterBy'];
$orderBy = empty($_REQUEST['orderBy']) ? '' : $_REQUEST['orderBy'];

$bugger = new bugTracker($_REQUEST['id']);
$issueList = $bugger->getBugList($filterBy);

if(empty($issueList))
{
	$literal = moduleLiteral::get($moduleID, 'ntf_noIssuesFound');
	$msg = DOM::create('div', '', '', 'noBugs msg_noContent');
	DOM::append($msg, $literal);
	$MContent->append($msg);
	
	$viewOptions = HTML::select('.viewOptions')->item(0);
	HTML::addClass($viewOptions, 'noDisplay');
}

// Set filter parameters
$control = HTML::select('.filters .all')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "orderBy" => $orderBy));

$control = HTML::select('.filters .solved')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => bugTracker::ST_RESOLVED, "orderBy" => $orderBy));

$control = HTML::select('.filters .rejected')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => bugTracker::ST_REJECTED, "orderBy" => $orderBy));

// Set ordering parameters
$control = HTML::select('.sorting .date')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => $filterBy));

$control = HTML::select('.sorting .priority')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => $filterBy, "orderBy" => "priority"));

$control = HTML::select('.sorting .severity')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => $filterBy, "orderBy" => "severity"));

// Get item template
$itemTemplate = HTML::select('.issue-summary.itemTemplate')->item(0);
// Get items holder
$holder = HTML::select('.itemsHolder')->item(0);


foreach($issueList as $issue)
{
	// clone template to get element
	$elem = $itemTemplate->cloneNode(TRUE);
		HTML::removeClass($elem, "itemTemplate");
		$id = "issue-summary-".$issue["id"];
		DOM::attr($elem, 'id', $id);
	DOM::append($holder, $elem);
	
	$elemRootSelector = '[id="'.$id.'"]';
 
	$status = HTML::select($elemRootSelector.' .mini-box.left .mini-badges span')->item(0);
	$status->nodeValue = $issue["status"][0].$issue["status"][1];
	
	$priority = HTML::select($elemRootSelector.' .mini-box.middle .mini-badges span')->item(0);
	$priorityName = bugTracker::getPriorityName($issue["priority"]);
	$priority->nodeValue = $priorityName[0].$priorityName[1];
	
	$severity = HTML::select($elemRootSelector.' .mini-box.right .mini-badges span')->item(0);
	$severityName = bugTracker::getSeverityName($issue["severity"]);
	$severity->nodeValue = $severityName[0].$severityName[1];
	$severityOuter = HTML::select($elemRootSelector.'.mini-box.severity')->item(0);
	HTML::addClass($severityOuter, $severityName);
	
	$title = HTML::select($elemRootSelector.' .summary .title')->item(0);
	$actionFactory->setModuleAction($title, $moduleID, "bugDetails", "", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id']));
	$title->nodeValue = $issue["title"];	
	
	$tags = HTML::select($elemRootSelector.' .summary .tags')->item(0);
		$tagList = explode(' ', trim($issue['type']));		
		foreach($tagList as $bTag)
		{
			if(strlen (trim($bTag))){
				$tag = DOM::create('div', '', '', 'issue-tag');
					$span = DOM::create('span', $bTag);
					DOM::append($tag, $span);
				DOM::append($tags, $tag);
			}
		}
	
	$time = HTML::select($elemRootSelector.' .started .started-link span')->item(0);
	$liveDate = datetimer::live($issue['date']);
	DOM::append($time, $liveDate);
	
	// Set user name or email address
	//$user = HTML::select($elemRootSelector.' .started .reporter-id span')->item(0);
	//$accInfo = account::getInfo(intval($issue['accountID'], 10));	
	//$user->nodeValue = $accInfo['username']; //$accInfo['firstname']." ".$accInfo['lastname']
	//$user->nodeValue = $issue['accountID'];
	
	/*	
	
	//controls
	$value = "Assign";
	$prop = DOM::create('span', $value);
	$actionFactory->setModuleAction($prop, $moduleID, "", $holder = "", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue["id"]));
	DOM::append($elem, $prop);
	
	$value = "Solve";
	$prop = DOM::create('span', $value);
	$actionFactory->setModuleAction($prop, $moduleID, "solveIssue", $holder = "", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue["id"], "title" => $issue["title"]));
	DOM::append($elem, $prop);
	
	*/
	
}

// Return output
return $MContent->getReport();
//#section_end#
?>