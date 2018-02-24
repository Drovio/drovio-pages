<?php
//#section#[header]
// Module Declaration
$moduleID = 105;

// Inner Module Codes
$innerModules = array();
$innerModules['trunkPage'] = 136;

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
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Security\account;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\gridSplitter;
use \UI\Forms\templates\simpleForm;
use \UI\Interactive\forms\formAutoComplete;

// Create Module Page
$page = new HTMLModulePage();
$actionFactory = $page->getActionFactory();

// Build the page
$page->build("", "modulesTester", TRUE);



// Toolbar navigation
// Loader
$title = moduleLiteral::get($moduleID, "lbl_moduleLoader");
$subItem = $page->addToolbarNavItem("loaderNavSub", $title, $class = "", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $moduleID);


$title = moduleLiteral::get($moduleID, "lbl_testingTrunk");
$subItem = $page->addToolbarNavItem("trunkNavSub", $title, $class = "", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $innerModules['trunkPage']);

// Create Sidebar
$splitter = new gridSplitter();

// Main Holder
$outerHolder = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE)->get();
$testerContainer = HTML::select(".testerContainer")->item(0);
DOM::append($testerContainer, $outerHolder);

// Testing Control Panel
$controlPanel = DOM::create("div", "", "testingControlPanel");
$splitter->appendToSide($controlPanel);

// Form Control
$form = new simpleForm();
$controlForm = $form->build($moduleID, $action = "moduleLoader", $controls = TRUE)->get();
DOM::append($controlPanel, $controlForm);

// Modules Available
$dbc = new interDbConnection();
$dbq = new dbQuery("305935527", "security.privileges.tester");
$attr = array();
$attr['aid'] = account::getAccountID();
$moduleParents = $dbc->execute($dbq, $attr);

$moduleParentsDepth_resource = $dbc->toArray($moduleParents, "id", "depth");
$moduleParents_resource = $dbc->toArray($moduleParents, "id", "description");

foreach ($moduleParentsDepth_resource as $id => $depth)
{
	$tabs = "";
	if ($depth != 0)
		$tabs = str_repeat("   ", $depth)."- ";
	
	$moduleParents_resource[$id] = $tabs.$moduleParents_resource[$id];
}

$selectedModuleParent = array_keys($moduleParents_resource);
$selectedModuleParent = $selectedModuleParent[0];

$title = moduleLiteral::get($moduleID, "lbl_moduleGroup");
$moduleGroupInput = $form->getResourceSelect($name = "moduleGroup", $multiple = FALSE, $class = "", $moduleParents_resource, $selectedModuleParent);
$form->insertRow($title, $moduleGroupInput, $required = FALSE, $notes = "");

// Get Modules
$dbq = new dbQuery("666615842", "units.modules");
$attr = array();
$attr['gid'] = $selectedModuleParent;
$moduleParents = $dbc->execute($dbq, $attr);

$moduleParentsDepth_resource = $dbc->toArray($moduleParents, "id", "depth");
$moduleParents_resource = $dbc->toArray($moduleParents, "id", "title");

$title = moduleLiteral::get($moduleID, "lbl_module");
$moduleInput = $form->getResourceSelect($name = "moduleParent", $multiple = FALSE, $class = "", $moduleParents_resource, "");
$form->insertRow($title, $moduleInput, $required = FALSE, $notes = "");

// Auto Complete
$autoComplete = new formAutoComplete();
$populate = array();
$populate[] = $moduleInput->getAttribute("id");
$autoComplete->engage($moduleGroupInput, "/ajax/modules/testerGroupModules.php", $fill = array(), $hide = array(), $populate, $mode = "lenient");


// Testing Site Page
$testingContainer = DOM::create("div", "", "testingContainer");
$splitter->appendToMain($testingContainer);

// Return output
return $page->getReport();
//#section_end#
?>