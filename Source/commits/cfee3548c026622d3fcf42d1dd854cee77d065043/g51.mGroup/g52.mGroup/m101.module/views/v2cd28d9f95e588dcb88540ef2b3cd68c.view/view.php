<?php
//#section#[header]
// Module Declaration
$moduleID = 101;

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
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("INU", "Developer");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\dbConnection;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;

use \API\Developer\components\units\modules\module;

use \UI\Html\HTMLContent;
use \INU\Developer\vcs\repositoryOverviewer;


set_time_limit(300);
// Module Migration
$dbc = new interDbConnection();
$dbq = new dbQuery("1464459212", "units.modules");
$result = $dbc->execute($dbq);

while ($row = $dbc->fetch($result))
{
	$module = new module($row['id']);
	$module->migrate();
	//$module->export();
}

/*/







$repViewer = new repositoryOverviewer("sdkRepViewer", "/.developer/Repository/Core/");
$control = $repViewer->build("Test Title")->get();


/*
$dbc = new dbConnection();
$dbc->options("MySQL", "db10.grserver.gr", "redbackdb", "rb_sql_user", "3fgVb9#0if5$4Rt");
$query = "SELECT * FROM RB_person";
$startTime = time();
$result = $dbc->execute($query);
$result = $dbc->execute($query);
$result = $dbc->execute($query);
$result = $dbc->execute($query);
$result = $dbc->execute($query);
$endTime = time();
$persons = $dbc->fetch($result, TRUE);
echo "time : ".($endTime - $startTime)."\n";
print_r($persons);
*/

$pageContent = new HTMLContent();
$pageContent->build("Test Page");
$pageContent->append($control);

return $pageContent->getReport();
//#section_end#
?>