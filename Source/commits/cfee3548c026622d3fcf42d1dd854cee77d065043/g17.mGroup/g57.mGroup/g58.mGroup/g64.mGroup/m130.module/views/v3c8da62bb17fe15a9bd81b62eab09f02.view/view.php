<?php
//#section#[header]
// Module Declaration
$moduleID = 130;

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
importer::import("API", "Content");
importer::import("API", "Developer");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \UI\Html\HTMLContent;

use \API\Developer\components\prime\indexing\packageIndex;
use \API\Developer\components\prime\indexing\libraryIndex;
use \API\Developer\components\sdk\sdkLibrary;
use \API\Developer\components\sdk\sdkPackage;

$HTMLContent = new HTMLContent();
$HTMLContent->build();

echo "libraryIndex::createReleaseIndex : ".libraryIndex::createReleaseIndex("/System/Resources/Documentation/SDK/", 'API');
exportLibrary('API');

return $HTMLContent->getReport();

function exportLibrary($libName)
{
	$sdkLib = new sdkLibrary();
	
	$packages = $sdkLib->getPackageList($libName);
	foreach ($packages as $packageName)
	{
		export($libName, $packageName);
	}
}

function export($libName, $packageName)
{		
	// Create Documentation index and library entry
	echo "packageIndex::createReleaseIndex : ".packageIndex::createReleaseIndex(systemRoot."/System/Resources/Documentation/SDK/", $libName, $packageName);
	echo "packageIndex::addLibraryEntry: ".packageIndex::addLibraryEntry("/System/Resources/Documentation/SDK/", $libName, $packageName);
}
//#section_end#
?>