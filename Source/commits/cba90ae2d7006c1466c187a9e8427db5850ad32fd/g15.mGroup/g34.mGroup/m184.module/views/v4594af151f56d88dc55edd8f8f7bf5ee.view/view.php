<?php
//#section#[header]
// Module Declaration
$moduleID = 184;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$page = new HTMLModulePage("OneColumnFullscreen");
$title = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($title, "pageExplorer");
$actionFactory = $page->getActionFactory();

// Head bar
$headbar = DOM::create("div", "", "", "headbar");
$page->appendToSection("mainContent", $headbar);

$pExplorerContainer = DOM::create("div", "", "", "pExplorerContainer");
$page->appendToSection("mainContent", $pExplorerContainer);

$pExplorer = DOM::create("div", "", "", "pExplorer");
DOM::append($pExplorerContainer, $pExplorer);



$columnContainer = DOM::create("div", "", "columnContainer", "columnContainer");
DOM::append($pExplorer, $columnContainer);

// User Group Selector
$dbc = new interDbConnection();
$dbq = new dbQuery("999274607", "security.privileges.user");
$groupsResource = $dbc->execute($dbq);
$userGroups = array();
$userGroups['null'] = "No Group";
$userGroupsDB = $dbc->toArray($groupsResource, "id", "name");
$userGroups = $userGroups + $userGroupsDB;

$form = new simpleForm();
$title = moduleLiteral::get($moduleID, "lbl_userGroupSelector");
$input = $form->getResourceSelect($name = "userGroup", $multiple = FALSE, $class = "", $userGroups, $selectedValue = "null");
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::attr($inputRow, "id", "userGroupSelector");
DOM::append($headbar, $inputRow);

// Page Title
$title = DOM::create("h3", "Page Explorer");
DOM::append($headbar, $title);


$columns = array();
$columnDepth = array();
$currentRow = 0;

// Add domain root column
addColumn($columnContainer, $columns, $columnDepth);

// Get domains
$dbq = new dbQuery("573078142", "units.domains");
$domainResource = $dbc->execute($dbq);
while ($domain = $dbc->fetch($domainResource))
{
	// Get folders
	$dbq = new dbQuery("245679959", "units.domains.folders");
	$attr = array();
	$attr['domain'] = $domain['name'];
	$folderResource = $dbc->execute($dbq, $attr);
	$lastDepth = 0;
	while ($folder = $dbc->fetch($folderResource))
	{
		$folderDepth = $folder['depth'] - 1;
		if ($folder['is_root'] && $folder['depth'] <= 1)
		{
			$folderDepth = 0;
			addColumnItem($columns, $columnDepth, $folderDepth, $currentRow, $folder['domain'], "domain");
		}
		else
		{
			if (empty($columns[$folderDepth]))
				addColumn($columnContainer, $columns, $columnDepth, $folderDepth);
			addColumnItem($columns, $columnDepth, $folderDepth, $currentRow, $folder['name'], "folder");
		}
		
		// Get folder pages
		$dbq = new dbQuery("1487551782", "units.domains.pages");
		$attr = array();
		$attr['id'] = $folder['id'];
		$pageResource = $dbc->execute($dbq, $attr);
		$empty = TRUE;
		while ($folderPage = $dbc->fetch($pageResource))
		{
			// Get module Info (for class)
			$dbq = new dbQuery("361601426", "units.modules");
			$attr = array();
			$attr['id'] = $folderPage['module_id'];
			$moduleResource = $dbc->execute($dbq, $attr);
			$moduleInfo = $dbc->fetch($moduleResource);
			
			$empty = FALSE;
			if (empty($columns[$folderDepth+1]))
				addColumn($columnContainer, $columns, $columnDepth, $folderDepth+1);
			
			// Set class
			$classes = array();
			$classes[] = $moduleInfo['status'];
			$classes[] = $moduleInfo['scope'];
			$classes[] = 'page';
			$itemClass = implode(" ", $classes);
			$pageItem = addColumnItem($columns, $columnDepth, $folderDepth+1, $currentRow, $folderPage['file'], $itemClass);
			$currentRow++;
			
			// Set module action
			if (!empty($folderPage['module_id']))
			{
				DOM::attr($pageItem, "id", "mid_".$folderPage['module_id']);
				$attr = array();
				$attr['mid'] = $folderPage['module_id'];
				$actionFactory->setPopupAction($pageItem, $moduleID, "moduleInfo", $attr);
			}
			else
			{
				$attr = array();
				$attr['pid'] = $folderPage['id'];
				$actionFactory->setPopupAction($pageItem, $moduleID, "pageInfo", $attr);
			}
		}
		
		if ($empty)
			$currentRow++;
	}
}

function addColumn($pExplorer, &$columns, &$columnDepth, $depth = 0)
{
	$column = DOM::create("div", "", "", "exColumn");
	DOM::append($pExplorer, $column);
	
	$columns[$depth] = $column;
	$columnDepth[$depth] = 0;
}

function addColumnItem(&$columns, &$columnDepth, $column, $row, $itemContext, $itemClass = "")
{
	// Get column
	$columnElement = $columns[$column];
	
	// Get items in between
	$numItems = $row - $columnDepth[$column];
	for ($i=0; $i<$numItems; $i++)
	{
		$emptyItem = DOM::create("div", "", "", "exColItem");
		DOM::append($columnElement, $emptyItem);
	}
	
	// Create item
	$item = DOM::create("div", $itemContext, "", "exColItem");
	DOM::appendAttr($item, "class", $itemClass);
	DOM::append($columnElement, $item);
	
	$columnDepth[$column] += ($numItems + 1);
	
	return $item;
}


// Legend
$legend = DOM::create("div", "", "", "legend");
DOM::append($pExplorer, $legend);

// Open Modules
$content = moduleLiteral::get($moduleID, "lbl_moduleScope");
$title = DOM::create("b", $content);
DOM::append($legend, $title);

$item = DOM::create("div", "Open Module", "", "exColItem page open");
DOM::append($legend, $item);

$item = DOM::create("div", "Public Module", "", "exColItem page public");
DOM::append($legend, $item);

$item = DOM::create("div", "Protected Module", "", "exColItem page protected");
DOM::append($legend, $item);

$item = DOM::create("div", "", "", "exColItem page empty");
DOM::append($legend, $item);

$content = moduleLiteral::get($moduleID, "lbl_moduleStatus");
$title = DOM::create("b", $content);
DOM::append($legend, $title);

$item = DOM::create("div", "Under Construction Module", "", "exColItem page uc");
DOM::append($legend, $item);

$item = DOM::create("div", "Off Module", "", "exColItem page off");
DOM::append($legend, $item);

$item = DOM::create("div", "", "", "exColItem page empty");
DOM::append($legend, $item);

$content = moduleLiteral::get($moduleID, "lbl_userGroup");
$title = DOM::create("b", $content);
DOM::append($legend, $title);

$item = DOM::create("div", "On User Group", "", "exColItem page groupOn");
DOM::append($legend, $item);

$item = DOM::create("div", "Off User Group", "", "exColItem page groupOff");
DOM::append($legend, $item);


// Return output
return $page->getReport();
//#section_end#
?>