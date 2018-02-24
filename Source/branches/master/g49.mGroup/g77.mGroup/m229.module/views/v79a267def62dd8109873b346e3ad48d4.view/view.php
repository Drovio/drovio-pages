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



$filterBy = empty($_REQUEST['filterBy']) ? '' : $_REQUEST['filterBy'];
$orderBy = empty($_REQUEST['orderBy']) ? '' : $_REQUEST['orderBy'];
$assigned = empty($_REQUEST['assigned']) ? '' : $_REQUEST['assigned'];

$bugger = new bugTracker($_REQUEST['id']);
$user = '';
$rootClass = 'bugList';
if(!empty($assigned))
{
	$user = account::getAccountID();
	$rootClass .= ' assigned';
}
$rootSelector = '.'.$rootClass;
//str_replace(' ', ' .', $rootSelector);
$rootSelector = preg_replace('/\s+/', '.', $rootSelector);
$issueList = $bugger->getBugList($user, $filterBy, $orderBy);


// Create Module Page
$MContent = new MContent($moduleID);
$actionFactory = $MContent->getActionFactory();
// Build the module 
$MContent->build("", $rootClass, TRUE);


if(empty($issueList))
{
	$literal = moduleLiteral::get($moduleID, 'ntf_noIssuesFound');
	$msg = DOM::create('div', '', '', 'noBugs msg_noContent');
	DOM::append($msg, $literal);
	$MContent->append($msg);
	
	// Add Reset Option
	$literal = moduleLiteral::get($moduleID, 'ntf_resetFilters');
	$msg = DOM::create('div', '', '', 'resetSearchBtn clickable msg_noContent');
	DOM::append($msg, $literal);
	$MContent->append($msg);
	$actionFactory->setModuleAction($msg, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "assigned" =>$assigned));
	
	$viewOptions = HTML::select($rootSelector.' .viewOptions')->item(0);
	HTML::addClass($viewOptions, 'noDisplay');
}

// Set filter on click events
$control = HTML::select($rootSelector.' .filters .all')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "orderBy" => $orderBy, "assigned" =>$assigned));
if(empty($filterBy))
	HTML::addClass($control, "selected");

$control = HTML::select($rootSelector.' .filters .solved')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => bugTracker::ST_RESOLVED, "orderBy" => $orderBy, "assigned" =>$assigned));
if($filterBy == bugTracker::ST_RESOLVED)
	HTML::addClass($control, "selected");

$control = HTML::select($rootSelector.' .filters .rejected')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => bugTracker::ST_REJECTED, "orderBy" => $orderBy, "assigned" =>$assigned));
if($filterBy == bugTracker::ST_REJECTED)
	HTML::addClass($control, "selected");

// Set ordering on click events
$control = HTML::select($rootSelector.' .sorting .date')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => $filterBy, "assigned" =>$assigned));
if(empty($orderBy))
	HTML::addClass($control, "selected");
	
$control = HTML::select($rootSelector.' .sorting .priority')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => $filterBy, "orderBy" => "priority", "assigned" =>$assigned));
if($orderBy == "priority")
	HTML::addClass($control, "selected");

$control = HTML::select($rootSelector.' .sorting .severity')->item(0);
$actionFactory->setModuleAction($control, $moduleID, "bugList", ".bugList", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id'], "filterBy" => $filterBy, "orderBy" => "severity", "assigned" =>$assigned));
if($orderBy == "severity")
	HTML::addClass($control, "selected");
	
// Get item template
$itemTemplate = HTML::select($rootSelector.' .issue-summary.itemTemplate')->item(0);
// Get items holder
$holder = HTML::select($rootSelector.' .itemsHolder')->item(0);


foreach($issueList as $issue)
{
	// clone template to get element
	$elem = $itemTemplate->cloneNode(TRUE);
		HTML::removeClass($elem, "itemTemplate");
		$id = "issue-summary-".$issue["id"];
		DOM::attr($elem, 'id', $id);
	DOM::append($holder, $elem);
	
	$elemRootSelector = '[id="'.$id.'"]';
 
	$status = HTML::select($rootSelector.' '.$elemRootSelector.' .mini-box.left .mini-badges span')->item(0);
	$status->nodeValue = $issue["status"][0].$issue["status"][1];
	
	$priority = HTML::select($rootSelector.' '.$elemRootSelector.' .mini-box.middle .mini-badges span')->item(0);
	$priorityName = bugTracker::getPriorityName($issue["priority"]);
	$priority->nodeValue = $priorityName[0].$priorityName[1];
	
	$severity = HTML::select($rootSelector.' '.$elemRootSelector.' .mini-box.right .mini-badges span')->item(0);
	$severityName = bugTracker::getSeverityName($issue["severity"]);
	$severity->nodeValue = $severityName[0].$severityName[1];
	$severityOuter = HTML::select($rootSelector.' '.$elemRootSelector.'.mini-box.severity')->item(0);
	HTML::addClass($severityOuter, $severityName);
	
	$title = HTML::select($rootSelector.' '.$elemRootSelector.' .summary .title')->item(0);
	$actionFactory->setModuleAction($title, $moduleID, "bugDetails", "", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id']));
	$title->nodeValue = $issue["title"];	
	
	$tags = HTML::select($rootSelector.' '.$elemRootSelector.' .summary .tags')->item(0);
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
	
	$time = HTML::select($rootSelector.' '.$elemRootSelector.' .started .started-link span')->item(0);
	$liveDate = datetimer::live($issue['date']);
	DOM::append($time, $liveDate);
	
	// Assign Delete Control	
	$control = HTML::select($elemRootSelector.' .deleteBtn')->item(0);
	$actionFactory->setModuleAction($control, $moduleID, "deleteBug", "", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id']));

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