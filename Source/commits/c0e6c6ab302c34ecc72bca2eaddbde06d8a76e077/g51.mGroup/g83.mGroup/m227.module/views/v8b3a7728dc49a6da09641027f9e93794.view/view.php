<?php
//#section#[header]
// Module Declaration
$moduleID = 227;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \DEV\BugTracker\bugTracker;
use \UI\Modules\MContent;

// Create Module Page
$MContent = new MContent($moduleID);
$actionFactory = $MContent->getActionFactory();
// Build the module 
$MContent->build();

$bugger = new bugTracker($_REQUEST['id']);
$issueList = $bugger->getAllBugs();

if(empty($issueList))
{
	$literal = moduleLiteral::get($moduleID, 'ntf_noIssuesFound');
	$msg = DOM::create('div', '', '', 'msg_noContent');
	DOM::append($msg, $literal);
	$MContent->append($msg);
}

foreach($issueList as $issue)
{
	//$item['id']
	//$item['title']
	//$item['date']
	//$item['severity']
	//$item['location'] 
	//$item['description']
	//$item['actionSequence'] 
	
	$id="issue-summary-".$issue["id"];
	$elem = DOM::create('div', '', $id, 'issue-summary narrow');
	
	$left = DOM::create('div', '', '', 'cursor-default'); //onclick="window.location.href='/questions/24313761/d3-js-3d-array-interpolation'" class="cp"
	DOM::append($elem, $left);
	
	$votes = DOM::create('div', '', '', 'left'); 
		$value = DOM::create('div', '', '', 'mini-badges');
			$span = DOM::create('span', $issue["status"][0].$issue["status"][1]);
			DOM::append($value, $span);
		DOM::append($votes, $value);
		$title = DOM::create('div');
			$span = DOM::create('span', 'status');
			DOM::append($title, $span);
		DOM::append($votes, $title);	
	DOM::append($left, $votes);
	
	$votes = DOM::create('div', '', '', 'middle'); 
		$value = DOM::create('div', '', '', 'mini-badges');
			$span = DOM::create('span', $issue["priority"][0].$issue["priority"][1]);
			DOM::append($value, $span);
		DOM::append($votes, $value);
		$title = DOM::create('div');
			$span = DOM::create('span', 'priority');
			DOM::append($title, $span);
		DOM::append($votes, $title);	
	DOM::append($left, $votes);
	
	$votes = DOM::create('div', '', '', 'right'); 
		$value = DOM::create('div', '', '', 'mini-badges');
			$span = DOM::create('span', $issue["severity"][0].$issue["severity"][1]);
			DOM::append($value, $span);
		DOM::append($votes, $value);
		$title = DOM::create('div');
			$span = DOM::create('span', 'severity');
			DOM::append($title, $span);
		DOM::append($votes, $title);	
	DOM::append($left, $votes);
	
	$summary = DOM::create('div', '', '', 'summary');
	DOM::append($elem, $summary);
	
	$title = DOM::create('h3', $issue["title"]."{clickable}", '', 'clickable');
		$actionFactory->setModuleAction($title, $moduleID, "issueDetails", $holder = "", $attr = array("pid" => $_REQUEST['id'], "bid" => $issue['id']));
	DOM::append($summary, $title);
	
	$tags = DOM::create('div', '', '', 'tags');
		$tag = DOM::create('div', '', '', 'issue-tag');
			$span = DOM::create('span', 'a tag / or type');
			DOM::append($tag, $span);
		DOM::append($tags, $tag);
	DOM::append($summary, $tags);
	
	$started = DOM::create('div', '', '', 'started');
		$time = DOM::create('div', '', '', 'started-link');
			$span = DOM::create('span', $issue["date_created"]);
			DOM::append($time, $span);
		DOM::append($started , $time);
		$user= DOM::create('div', '', '', 'reporter-id');
			$span = DOM::create('span', 'thanasis');
			DOM::append($user, $span);
		DOM::append($started , $user);
	DOM::append($summary, $started);
	
	
	/*
	
	
	
	$value = ;
	$prop = DOM::create('span', $value);
	DOM::append($elem, $prop);	
	
	$value = $issue["severity"];
	$prop = DOM::create('span', $value);
	DOM::append($elem, $prop);
	
	$value = $issue["location"];
	$prop = DOM::create('span', $value);
	DOM::append($elem, $prop);
	
	$value = $issue["description"];
	$prop = DOM::create('span', $value);
	DOM::append($elem, $prop);
	
	
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

	$MContent->append($elem);
}






// Return output
return $MContent->getReport();
//#section_end#
?>