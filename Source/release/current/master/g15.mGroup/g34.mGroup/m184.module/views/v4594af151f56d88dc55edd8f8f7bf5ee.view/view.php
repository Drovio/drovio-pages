<?php
//#section#[header]
// Module Declaration
$moduleID = 184;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\pages\domain;
use \SYS\Resources\pages\pageFolder;
use \SYS\Resources\pages\page;
use \API\Model\modules\module;
use \API\Model\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Security\privileges;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

$dbc = new dbConnection();

// Create Module Page
$pageContent = new MContent($moduleID);
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$pageContent->build("", "pageModuleExplorer", TRUE);
$actionFactory = $pageContent->getActionFactory();

// Get containers
$headbar = HTML::select(".pageModuleExplorerContainer .headbar")->item(0);
$columnContainer = HTML::select(".pageModuleExplorerContainer .columnContainer")->item(0);

// User Group Selector
$userGroups = array();
$userGroups['null'] = "No Group";
$userGroupsDB = privileges::getPermissionGroups();
$userGroups = $userGroups + $userGroupsDB;

$form = new simpleForm();
$title = moduleLiteral::get($moduleID, "lbl_userGroupSelector");
$input = $form->getResourceSelect($name = "userGroup", $multiple = FALSE, $class = "", $userGroups, $selectedValue = "null");
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
DOM::attr($inputRow, "id", "userGroupSelector");
DOM::prepend($headbar, $inputRow);


$columns = array();
$columnDepth = array();
$currentRow = 0;

// Add domain root column
addColumn($columnContainer, $columns, $columnDepth);

// Get domains
$domains = domain::getAllDomains();
foreach ($domains as $domain)
{
	// Get domain folders
	$domainFolders = pageFolder::getDomainFolders($domain['domain_name']);
	$lastDepth = 0;
	foreach ($domainFolders as $folder)
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
		$folderPages = page::getFolderPages($folder['id']);
		$empty = TRUE;
		foreach ($folderPages as $folderPage)
		{
			// Get module Info (for class)
			$moduleInfo = module::info($folderPage['module_id']);
			
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
		}
		
		if ($empty)
			$currentRow++;
	}
}

// Return output
return $pageContent->getReport();

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
//#section_end#
?>