<?php
//#section#[header]
// Module Declaration
$moduleID = 62;

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
//#section_end#
//#section#[code]
use \API\Geoloc\lang\mlgContent;
use \API\Model\layout\components\modulePage;
use \UI\Forms\contentPresenter;
use \API\Model\layout\components\moduleContainer;
use \UI\Presentation\layoutContainer;
use \API\Model\protocol\ajax\ascop;


// Clear report stack
report::clear();

$pageTitle = mlgContent::get_literal("sub::eBuilder::main","lbl_ebuilderMainPageTitle", FALSE);
$moduleBuilder = new modulePage($pageTitle, "oneLeftColumnFixed");


//SideBar Treewview
$sideBar = DOM::create("div");

$tag = DOM::create("span", "click me");
$singleItem = DOM::create("div");
DOM::append($singleItem ,$tag );

ascop::add_actionGET($singleItem , $policyCode, "docViewer", "docViewerHolder");
$attr = array();
$attr['id'] = $row['id'];
//$attr['title'] = $row['title'];
ascop::add_asyncATTR($singleItem , $attr);




DOM::append($sideBar ,$singleItem );

$moduleBuilder->append_to_section("sidebar", $sideBar );

//Doc viewer holder
$docViewerHolder = DOM::create("div", "", "docViewerHolder");
$moduleBuilder->append_to_section("mainContent", $docViewerHolder );


// Clear report
report::clear();
report::add_content($moduleBuilder->get_page_body(), modulePage::HOLDER);
return report::get();
//#section_end#
?>