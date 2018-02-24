<?php
//#section#[header]
// Module Declaration
$moduleID = 269;

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
importer::import("DEV", "Apps");
importer::import("DEV", "Core");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Navigation\sideBar;
use \UI\Presentation\tabControl;
use \UI\Presentation\dataGridList;
use \UI\Developer\codeEditor;
use \UI\Developer\editors\WViewEditor;
use \UI\Developer\devTabber;
use \DEV\Apps\appManifest;
use \DEV\Apps\views\appView;
use \DEV\Apps\source\srcPackage;
use \DEV\Core\manifests;


// Initialize Application
$appID = engine::getVar('id');
$viewFolder = engine::getVar('parent');
$viewName = engine::getVar('name');
$appView = new appView($appID, $viewFolder, $viewName);

// Create object id
$objID = $appID."_view_".str_replace("/", "_", $viewFolder)."_".$viewName;


// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
// Create Global Container
$globalContainer = $pageContent->build("", "applicationViewEditor")->get();

// Create Global Container Toolbar
$tlb = new sideBar();
$navToolbar = $tlb->build(sideBar::LEFT, $globalContainer)->get();
$pageContent->append($navToolbar);

// Delete button
$delTool = DOM::create("div", "", "", "objTool delete");
$tlb->insertToolbarItem($delTool);
$attr = array();
$attr['id'] = $appID;
$attr['parent'] = $viewFolder;
$attr['name'] = $viewName;
$actionFactory->setModuleAction($delTool, $moduleID, "deleteView", "", $attr);


// Create Object Tab Controller
$objectTabber = new tabControl();
$objectTabberControl = $objectTabber->build($id = "tbr_".$objID, FALSE, FALSE)->get();
$pageContent->append($objectTabberControl);

// Create Tabs
//_____ PHP Code Tab
$tab_id = $objID."_phpCode";
$header = moduleLiteral::get($moduleID, "lbl_tab_sourceCode");
$objectSourceContainer = DOM::create("div", "", "", "viewSource");
$objectTabber->insertTab($tab_id, $header, $objectSourceContainer, $selected = TRUE);
//_____ HTML + CSS Code Tab
$tab_id = $objID."_htmlCode";
$header = moduleLiteral::get($moduleID, "lbl_tab_designer");
$objectHtmlContainer = DOM::create("div", "", "", "viewDesigner");
$objectTabber->insertTab($tab_id, $header, $objectHtmlContainer, $selected = FALSE);
//_____ JS Code Tab
$tab_id = $objID."_JSCode";
$header = moduleLiteral::get($moduleID, "lbl_tab_behavior");
$objectScriptContainer = DOM::create("div", "", "", "viewJS");
$objectTabber->insertTab($tab_id, $header, $objectScriptContainer, $selected = FALSE);


// Create form object
$form = new simpleForm();

// Source Code Form 
$sourceForm = $form->build($moduleID, "updateSource", $controls = FALSE)->get();
DOM::append($objectSourceContainer, $sourceForm);

// Hidden Values
//_____ Application ID
$input = $form->getInput("hidden", "id", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View name
$input = $form->getInput("hidden", "name", $viewName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View folder
$input = $form->getInput("hidden", "parent", $viewFolder, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Container
$outerContainer = DOM::create("div");
$form->append($outerContainer);

// Toolbar
$objMgrToolbar = new navigationBar();
$objMgrToolbar->build($dock = "T", $outerContainer);
DOM::append($outerContainer, $objMgrToolbar->get());

// Save button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Settings button
$settingsTool = DOM::create("div", "", "", "objTool settings");
$objMgrToolbar->insertToolbarItem($settingsTool);

// Create Code Container
$viewSourceContainer = DOM::create("div", "", "viewSourceContainer");
DOM::append($outerContainer, $viewSourceContainer);

// PHP Editor
$editor = new codeEditor();
$code = trim($appView->getPHPCode());
$phpEditor = $editor->build("php", $code, "viewSource")->get();
DOM::append($viewSourceContainer, $phpEditor);
$viewInfoContainer = DOM::create("div", "", "", "viewInfo tabPageContent noDisplay");
DOM::append($viewSourceContainer, $viewInfoContainer);


// Create form object
$form = new simpleForm();

// Designer Form 
$designerForm = $form->build($moduleID, "updateHTML", $controls = FALSE)->get();
DOM::append($objectHtmlContainer, $designerForm);

// Hidden Values
//_____ Application ID
$input = $form->getInput("hidden", "id", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View name
$input = $form->getInput("hidden", "name", $viewName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View folder
$input = $form->getInput("hidden", "parent", $viewFolder, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer COntainer
$outerContainer = DOM::create("div");
$form->append($outerContainer);

// Toolbar
$objMgrToolbar = new navigationBar();
$objMgrToolbar->build($dock = "T", $outerContainer);
DOM::append($outerContainer, $objMgrToolbar->get());

// Save button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$objModelContainer = DOM::create("div", "", "viewDesignerContainer");
DOM::append($outerContainer, $objModelContainer);

// CSS Editor
$html = $appView->getHTML();
$css = trim($appView->getCSS());
$editor = new WViewEditor("viewCSS", "viewHTML");
$viewDesigner = $editor->build($html, $css)->get();
DOM::append($objModelContainer, $viewDesigner);


// Create form object
$form = new simpleForm();

// Source Code Form 
$jsForm = $form->build($moduleID, "updateScript", $controls = FALSE)->get();
DOM::append($objectScriptContainer, $jsForm);

// Hidden Values
//_____ Application ID
$input = $form->getInput("hidden", "id", $appID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View name
$input = $form->getInput("hidden", "name", $viewName, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ View folder
$input = $form->getInput("hidden", "parent", $viewFolder, $class = "", $autofocus = FALSE);
$form->append($input);

// Outer Container
$outerContainer = DOM::create("div");
$form->append($outerContainer);

// Toolbar
$objMgrToolbar = new navigationBar();
$objMgrToolbar->build($dock = "T", $outerContainer);
DOM::append($outerContainer, $objMgrToolbar->get());

// Save button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$objMgrToolbar->insertToolbarItem($saveTool);

// Create Code Container (With Documentation)
$viewScriptContainer = DOM::create("div", "", "viewScriptContainer");
DOM::append($outerContainer, $viewScriptContainer);

// Javascript Editor
$editor = new codeEditor();
$code = trim($appView->getJS());
$jsEditor = $editor->build("js", $code, "viewScript")->get();
DOM::append($viewScriptContainer, $jsEditor);




// View Source Settings
$settingsContainer = DOM::create("div", "", "", "settingsContainer");
DOM::append($viewInfoContainer, $settingsContainer);

$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($settingsContainer, $glist);

$headers = array();
$headers[] = "Library";
$headers[] = "Package";
$headers[] = "Type";
$dtGridList->setHeaders($headers);

// Get application dependencies
$dependencies = $appView->getDependencies();

// Get SDK Open Packages
$openPackageList = importer::getOpenPackageList();

// Get application manifest permissions
$manifest = new appManifest($appID);
$appManifests = $manifest->getPermissions();
$coreManifests = new manifests();
foreach ($appManifests as $mfID)
{
	$mfInfo = $coreManifests->info($mfID);
	foreach ($mfInfo['packages'] as $libName => $packageList)
		$openPackageList[$libName] = array_merge($openPackageList[$libName], $mfInfo['packages'][$libName]);
}

ksort($openPackageList);
foreach ($openPackageList as $lib => $pkgs)
{
	asort($pkgs);
	foreach ($pkgs as $pkg)
	{
		$checked = FALSE;
		if (is_array($dependencies['sdk'][$lib]) && in_array($pkg, $dependencies['sdk'][$lib]))
			$checked = TRUE;
			
		// Grid List Contents
		$gridRow = array();
		$gridRow[] = $lib;
		$gridRow[] = $pkg;
		$gridRow[] = "Red SDK";
		
		$dtGridList->insertRow($gridRow, "sdk_dependencies[".$lib.','.$pkg.']', $checked);
	}
}
	
// Get Application Source Packages
$srcPackage = new srcPackage($appID);
$packages = $srcPackage->getList();
asort($packages);
foreach ($packages as $package)
{
	$checked = FALSE;
	if (in_array($package, $dependencies['app']))
		$checked = TRUE;
		
	// Grid List Contents
	$gridRow = array();
	$gridRow[] = srcPackage::LIB_NAME;
	$gridRow[] = $package;
	$gridRow[] = "Application Source";
	
	$dtGridList->insertRow($gridRow, "app_dependencies[".$package.']', $checked);
}

// Send devTabber Tab
$devTabber = new devTabber();
return $devTabber->getReportContent($objID, $viewName, $pageContent->get());
//#section_end#
?>