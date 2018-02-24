<?php
//#section#[header]
// Module Declaration
$moduleID = 34;

// Inner Module Codes
$innerModules = array();
$innerModules['deleteGroup'] = 65;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("UI", "Navigation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Developer\components\modules\moduleGroup;
use \API\Model\units\sql\dbQuery;
use \API\Comm\database\connections\interDbConnection;
use \API\Security\account;
use \UI\Navigation\fileTreeView;
use \UI\Html\HTMLContent;

use \API\Developer\components\modules\auxiliary;


$dbc = new interDbConnection();

// Create Module Page
$HTMLContentBuilder = new HTMLContent();
$actionFactory = $HTMLContentBuilder->getActionFactory();

//$wrapper = DOM::create('div', '', '', 'treeViewerWrapper');
//DOM::append($wrapper);

$treeView = new fileTreeView();
$moduleViewerTree = $treeView->build($id = "moduleTree", $class = "moduleViewerTree", $sorting = TRUE)->get();
//DOM::append($wrapper, $moduleViewerTree);
$HTMLContentBuilder->buildElement($moduleViewerTree);

// Get Module Groups
$dbq = new dbQuery("677677266", "security.privileges.developer");
$attr = array();
$attr['aid'] = account::getAccountID();
$moduleGroups = $dbc->execute($dbq, $attr);

////// dir.xml auxiliaries: 
// key: module id
// value: array with auxiliaries
$modulesAuxs = array();

// create folders DOM
$lastDepthElem = array();
//$lastDepthElem[] = $moduleViewerTree;
$lastDepthElem[] = "";
while ($row = $dbc->fetch($moduleGroups)) 
{
	$group = new moduleGroup();
	$group->initialize($row['id']);
	
	$folder = DOM::create("div");//, $row['description']);
	$folderIcon = DOM::create("div", "", "", "contentIcon mgIcon");
	DOM::append($folder, $folderIcon);
	$folderName = DOM::create("span", $row['description']);
	DOM::append($folder, $folderName);
	$folderItem = $treeView->insertExpandableTreeItem('mg_'.$row['id'], $folder, $lastDepthElem[$row['depth']]);
	$treeView->assignSortValue($folderItem, $row['description']);

	//$lastDepthElem[$row['depth']+1] = $folderItem;
	$lastDepthElem[$row['depth']+1] = 'mg_'.$row['id'];
	
	// get all auxiliaries by group id
	//$aux = $group->get_aux();
	$aux = $group->getAuxiliary();
	$modulesAuxs = array_merge($modulesAuxs, $aux);
}

// Get Modules
$dbq = new dbQuery("564007386", "security.privileges.developer");
$attr = array();
$attr['aid'] = account::getAccountID();
$modules = $dbc->execute($dbq, $attr);
while ($row = $dbc->fetch($modules)) 
{
	$parentGroup = DOM::find("mg_".$row['group_id']);
	
	$fname = $row['title'];
	$file = DOM::create("div");
	$fileIcon = DOM::create("div", "", "", "contentIcon mIcon");
	DOM::append($file, $fileIcon);
	$fileName = DOM::create("span", $fname);
	DOM::append($file, $fileName);
	
	$attr = array();
	$attr['id'] = $row['id'];
	// CSS Item
	$cssIcon = DOM::create("div", "", "", "contentIcon cssIcon");
	DOM::attr($cssIcon, "title", "CSS");
	DOM::append($file, $cssIcon);
	$attr['type'] = "css";
	$actionFactory->setModuleAction($cssIcon, $moduleID, "moduleCSSEditor", "", $attr);
	// Script Item
	$scriptIcon = DOM::create("div", "", "", "contentIcon jsIcon");
	DOM::attr($scriptIcon, "title", "JS");
	DOM::append($file, $scriptIcon);
	$attr['type'] = "js";
	$actionFactory->setModuleAction($scriptIcon, $moduleID, "moduleJSEditor", "", $attr);

	$pointerFlag = (count($modulesAuxs["'".$row['id']."'"]) > 0); 
	$fileItem = $treeView->insertSemiExpandableTreeItem('m_'.$row['id'], $file, "mg_".$row['group_id']);
	
	$attr = array();
	$attr['id'] = $row['id'];
	$actionFactory->setModuleAction($treeView->getTreeItemContent($fileItem), $moduleID, "editorModule", "", $attr);
	$actionFactory->setModuleAction($fileItem, $moduleID, "editorModule", "", $attr);
	
	// Get Parent Module
	$parentModule = DOM::find("m_".$row['id']);
	$attr = array();
	$attr['id'] = $row['id'];
	
	// append auxs
	if (is_array($modulesAuxs["'".$row['id']."'"]))
		foreach ($modulesAuxs["'".$row['id']."'"] as $maux)
		{
			$fname = $maux;//$maux['title'];
			$file = DOM::create("div");
			$fileIcon = DOM::create("div", "", "", "contentIcon auxIcon");
			DOM::append($file, $fileIcon);
			$fileName = DOM::create("span", $fname);
			DOM::append($file, $fileName);
						
			$fileItem = $treeView->insertTreeItem("aux_".$row['id']."_".$fname, $file, "m_".$row['id']);
			$treeView->assignSortValue($fileItem, $fname);
			
			//--
			//Find aux seed
			$auxModule = new auxiliary();
			$auxModule->initialize($row['id'], '', $maux)->load();			
			//--
			
			$attr = array();
			$attr['id'] = $row['id'];
			$attr['title'] = $maux; //$maux['title'];
			$attr['seed'] = $auxModule->getSeed();//$maux['seed'];
			$actionFactory->setModuleAction($fileItem, $moduleID, "editorModule", "", $attr);
		}
	
}
return $HTMLContentBuilder->getReport();
//#section_end#
?>