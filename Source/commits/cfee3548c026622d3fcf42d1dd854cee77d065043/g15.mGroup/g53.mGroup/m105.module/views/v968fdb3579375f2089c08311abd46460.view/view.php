<?php
//#section#[header]
// Module Declaration
$moduleID = 105;

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
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Interactive");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\server\ModuleProtocol;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Security\account;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\gridSplitter;
use \UI\Forms\templates\simpleForm;
use \UI\Interactive\forms\formAutoComplete;

// Create Module Page
$page = new HTMLModulePage("simpleFullScreen");

// Build the module
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($pageTitle, "testingSite");

// Create Sidebar
$splitter = new gridSplitter();

// Main Holder
$outerHolder = $splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE)->get();
$page->appendToSection("mainContent", $outerHolder);

// Testing Control Panel
$controlPanel = DOM::create("div", "", "testingControlPanel");
$splitter->appendToSide($controlPanel);

// Form Control
$form = new simpleForm();
$controlForm = $form->build($moduleID, $action = "testerLoader", $controls = TRUE)->get();
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