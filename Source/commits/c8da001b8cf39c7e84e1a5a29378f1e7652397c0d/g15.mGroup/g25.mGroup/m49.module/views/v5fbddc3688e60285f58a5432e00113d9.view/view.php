<?php
//#section#[header]
// Module Declaration
$moduleID = 49;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Model\units\sql\dbQuery;
use \API\Developer\components\sql\dvbLib;
use \API\Developer\components\sql\dvbDomain;
use \API\Developer\resources\paths;
use \API\Resources\DOMParser;
use \UI\Html\HTMLContent;
use \UI\Navigation\treeView;
use \ESS\Protocol\server\ModuleProtocol;

// Create Module Page
$content = new HTMLContent();
$content->build("sqlQueryViewer", "queryViewer");

// Create domain tree on the sidebar
$navTree = new treeView();
$navTreeElement = $navTree->build('dbQueriesTree', 'dbQueriesViewerTree', TRUE)->get();
$content->append($navTreeElement);


function build_sub_tree($container, $navTree, $name, $sub, $full_domain, $moduleID)
{
	// Build the domain tree item
	$item = DOM::create("div", $name);
	$treeItem = $navTree->insert_expandableTreeItem($container, $name, $item);
	//_____ Build the query tree list
	build_q_tree($navTree, $treeItem, $full_domain, $moduleID);
	
	// If there are no subdomains, exit function
	if (is_array($sub) & count($sub) == 0)
		return;
	
	// Foreach subdomain, build a tree
	foreach ($sub as $key => $value)
		build_sub_tree($treeItem, $navTree, $key, $value, $full_domain.".".$key, $moduleID);
}

function build_q_tree($navTree, $container, $domain, $moduleID)
{
	$queries = dvbDomain::getQueries($domain);
	foreach ($queries as $key => $value)
	{
		$item = DOM::create("div", "[".$key."] ".$value);
		ModuleProtocol::addActionGET($item, $moduleID, "queryEditor");
		$attr = array();
		$attr['domain'] = $domain;
		$attr['qid'] = $key;
		ModuleProtocol::addAsyncATTR($item, $attr);
		$treeItem = $navTree->insert_treeItem($container, $key, $item);
	}
}

$domains = dvbLib::getDomainList();
foreach ($domains as $key => $value)
	build_sub_tree($navTreeElement, $navTree, $key, $value, $key, $moduleID);
	
return $content->getReport();
//#section_end#
?>