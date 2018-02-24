<?php
//#section#[header]
// Module Declaration
$moduleID = 122;

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
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
//---------- AUTO-GENERATED CODE ----------//
use \UI\Html\HTMLModulePage;
use \UI\Presentation\togglers\toggler;
use \UI\Navigation\treeView;
use \UI\Forms\formFactory;
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\sdk\sdkPackage;
//use \API\Resources\archive\zipManager;
//use \API\Developer\profiler\tester;
//use \INU\Forms\HTMLEditor;

// Create Module Page
$page = new HTMLModulePage("simpleFullScreen");

// Build the module
$page->build("Under Construction", "uc");

//$htmlEditor = new HTMLEditor();
//$container = $htmlEditor->build()->get();

$holder = DOM::create("div");

$fform = new formFactory();
$search = $fform->getInput($type = "text", $name = "search", $value = "", $class = "search", $autofocus = FALSE);
DOM::attr($search, "placeholder", "Search for a library, package, namespace, or object...");
DOM::append($holder, $search);

$container = DOM::create("div", "", "", "wrapper");

$library = new sdkLibrary();
$libPacks = $library->getPackageList();
foreach ($libPacks as $key => $value)
{
	$arr = explode("::", $key);
	$lib = $arr[0];
	$pkg = $arr[1];
	
	$toggler = new toggler();
	$t = $toggler->build()->setHead(DOM::create("span", $value))->get();
	DOM::attr($t, "filter", strtolower($lib." ".$pkg));
	DOM::append($container, $t);
	
	$treeView = new treeView();
	$contentsTree = $treeView->build($id = "tv_".$lib."_".$pkg, $class = "", $sorting = TRUE)->get();
	$toggler->appendToBody($contentsTree);
	
	$sdkPkg = new sdkPackage();
	$parentList = array();
	$objects = $sdkPkg->getPackageObjects($lib, $pkg);
	foreach ($objects as $o)
	{
		$ns = $o['ns'];
		$title = $o['title'];
		$name = $o['name'];
		
		$parentTree = $contentsTree;
		// Create structure
		if ((!empty($ns)) && empty($parentList[$ns]))
		{
			$nsarr = explode("::", $ns);
			$nstext = "";
			foreach ($nsarr as $key => $nselem)
			{
				$nstext .= "::".$nselem;
				$nstext = trim($nstext, " :");
			
				if (!empty($parentList[$nstext]))
				{
					$parentTree = $parentList[$nstext];
					continue;
				}
				
				$slice = array_slice($nsarr, $key);
				DOM::appendAttr($parentTree, "filter", strtolower(" ".implode(" ", $slice)));
				
				$parentTree = $treeView->insert_expandableTreeItem($parentTree, "", DOM::create("div", $nselem));
				$parentList[$nstext] = $parentTree;
				DOM::appendAttr($t, "filter", strtolower(" ".$nselem));
				DOM::appendAttr($parentTree, "filter", strtolower(" ".$nselem));
				//$slice = array_slice($nsarr, $key);
				//DOM::appendAttr($parentTree, "filter", strtolower(" ".implode(" ", $slice)));
				$treeView->add_sortValue($parentTree, $nselem);
			}
		}
		
		if (!empty($parentList[$ns]))
			$parentTree = $parentList[$ns];
		
		// Add object
		$treeContents = DOM::create("div", "", "", "objectContents");
		
		$check = $fform->getInput($type = "checkbox", "imports[".$lib."][".$pkg."][".$ns."]", $value = $name, $class = "check", $autofocus = FALSE);
		DOM::append($treeContents, $check);
		$n = DOM::create("span", $name);
		DOM::append($treeContents, $n);
		
		DOM::appendAttr($t, "filter", strtolower(" ".$name));
		$nsarr = explode("::", $ns);
		$nstext = "";
		foreach ($nsarr as $nselem)
		{
			$nstext .= "::".$nselem;
			$nstext = trim($nstext, " :");
			DOM::appendAttr($parentList[$nstext], "filter", strtolower(" ".$name));
		}
		
		if (!empty($title))
				DOM::attr($treeContents, "title", $title);
		$item = $treeView->insert_treeItem($parentTree, "", $treeContents);
		DOM::attr($item, "filter", strtolower($name));
		DOM::appendAttr($contentsTree, "filter", strtolower(" ".$name));
		$treeView->add_sortValue($item, "zzz".$name);
	}
}

DOM::append($holder, $container);

/*
$trunkPath = systemRoot.tester::getTrunk()."/";
$path = $trunkPath."newFolder/";
$container = DOM::create("div", $path);

//zipManager::test();
$name = sys_get_temp_dir()."/test_1.zip";
$contents = directory::getContentList(systemRoot."/_sbd_support/legal/");
	// CREATE ZIP
//print_r(self::create($name, $contents));
	// EXTRACT ZIP
//print_r(self::extract($name, sys_get_temp_dir()."/", FALSE, "index.php"));
	// ZIP DETAILS
//print_r(self::getDetails($name));
	// APPEND FILES
//print_r(self::append($name, $contents, "/terms"));
	// Remove FILES
//self::remove($name, 0);
	// Rename FILES
//self::rename($name, "terms/", "test/");
	// Get source
//print_r(self::read($name, "index.php", self::CONTENTS));
*/

$page->appendToSection("mainContent", $holder);

// Return output
return $page->getReport();
//#section_end#
?>