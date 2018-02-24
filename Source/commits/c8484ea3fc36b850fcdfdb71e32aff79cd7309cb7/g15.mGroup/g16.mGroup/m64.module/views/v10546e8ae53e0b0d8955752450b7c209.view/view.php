<?php
//#section#[header]
// Module Declaration
$moduleID = 64;

// Inner Module Codes
$innerModules = array();
$innerModules['viewEditor'] = 65;

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
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Developer\components\units\modules\module;
use \API\Developer\components\units\modules\moduleGroup;
use \API\Model\units\sql\dbQuery;
use \API\Security\account;
use \UI\Navigation\fileTreeView;
use \UI\Html\HTMLContent;

// Create Module Page
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();

// Initialize database connection
$dbc = new interDbConnection();

$treeView = new fileTreeView();
$moduleViewerTree = $treeView->build($id = "moduleExplorer", $class = "moduleViewerTree", $sorting = TRUE)->get();
$rootContainer = $pageContent->buildElement($moduleViewerTree)->get();
DOM::append($rootContainer);

// Get Module Groups
$dbq = new dbQuery("677677266", "security.privileges.developer");
$attr = array();
$attr['aid'] = account::getAccountID();
$moduleGroups = $dbc->execute($dbq, $attr);

// Module Groups
$lastDepthElem = array();
$lastDepthElem[] = "";
while ($row = $dbc->fetch($moduleGroups))
{
	// Create group item container
	$folder = DOM::create("div", "", "", "mGroup");
	$folderIcon = DOM::create("span", "", "", "contentIcon mgIcon");
	DOM::append($folder, $folderIcon);
	$folderName = DOM::create("span", $row['description']);
	DOM::append($folder, $folderName);
	
	// Insert
	$folderItem = $treeView->insertExpandableTreeItem('mg'.$row['id'], $folder, $lastDepthElem[$row['depth']]);
	$treeView->assignSortValue($folderItem, $row['description']);
	
	// Set last depth element for children
	$lastDepthElem[$row['depth']+1] = 'mg'.$row['id'];
}

// Get Developer Modules
$dbq = new dbQuery("564007386", "security.privileges.developer");
$attr = array();
$attr['aid'] = account::getAccountID();
$modules = $dbc->execute($dbq, $attr);
while ($row = $dbc->fetch($modules)) 
{
	// Get moduleID
	$moduleID = $row['id'];
	
	// View Item
	$fname = $row['title'];
	$file = DOM::create("div");
	$fileIcon = DOM::create("div", "", "", "contentIcon mIcon");
	DOM::append($file, $fileIcon);
	$fileName = DOM::create("span", $fname);
	DOM::append($file, $fileName);

	$moduleFileItem = $treeView->insertSemiExpandableTreeItem('m'.$moduleID, $file, "mg".$row['group_id']);
	
	
	// Get module views
	$module = new module($moduleID);
	$views = $module->getViews();
	
	foreach ($views as $viewID => $viewName)
	{
		// If view name is same with module (main view), continue to next view
		if ($viewName == $row['title'])
		{
			// Set action to main view
			$attr = array();
			$attr['mid'] = $moduleID;
			$attr['vid'] = $viewID;
			$actionFactory->setModuleAction($moduleFileItem, $innerModules['viewEditor'], "", "", $attr);
			continue;
		}
		
		// Create view
		$fname = $viewName;
		$file = DOM::create("div");
		$fileIcon = DOM::create("div", "", "", "contentIcon auxIcon");
		DOM::append($file, $fileIcon);
		$fileName = DOM::create("span", $viewName);
		DOM::append($file, $fileName);
					
		$fileItem = $treeView->insertTreeItem("mv".$viewID, $file, "m".$row['id']);
		$treeView->assignSortValue($fileItem, $fname);
		
		$attr = array();
		$attr['mid'] = $moduleID;
		$attr['vid'] = $viewID;
		$actionFactory->setModuleAction($fileItem, $innerModules['viewEditor'], "", "", $attr);
	}
}
return $pageContent->getReport();
//#section_end#
?>