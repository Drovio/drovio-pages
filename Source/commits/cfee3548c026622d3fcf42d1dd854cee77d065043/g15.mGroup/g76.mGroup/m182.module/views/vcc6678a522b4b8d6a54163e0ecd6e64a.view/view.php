<?php
//#section#[header]
// Module Declaration
$moduleID = 182;

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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Html\HTMLContent;
use \API\Content\analytics;


// Create Module Content
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();
$container = $HTMLContent->build()->get();

/*
$timeSlopMin = 15; // In minutes
$timeSlopSecs = 15 * 60; // In Seconds
$max = (int) ((24 * 60) / $timeSlopMin);
if(((24 * 60) / $timeSlopMin) > 0)
	$max++;

$d = array();
for($i = 1; $i <= $max; $i++)
{
	$d[] = 0;
}

$midnight = strtotime("00:00"); // Midnight measured in seconds since Unix Epoch
$mData = analytics::getData($date);
foreach ($mData as $entry)
{
	$time = $entry['time'];
	$sinceMidnight = $time - $midnight;
	
	$n = (int) ($sinceMidnight / $timeSlopSecs);
	if(($sinceMidnight % $timeSlopSecs) > 0)
		$n++;
	$d[$n]++;
}
*/


$graph = DOM::create('canvas', '', 'graph', 'graph');
DOM::append($container, $graph);

$sForm = new simpleForm();
$sForm->build('', "", $controls = FALSE);

$title = DOM::create("span", "Display");
$submit = $sForm->getButton($title, $id = "");
DOM::attr($submit, "onclick", "plotGraph(this);");
$sForm->append($submit);

DOM::append($container, $sForm->get());

return $HTMLContent->getReport();

/*
<div style="float:right; text-align:center; width:400px;" class="box1">
<div style="position:relative;">
  <canvas style="width:100%;" class="graph" id="data_chart"></canvas> 

  <table width="100%" cellspacing="0" cellpadding="0" align="center" style="margin-top:5px;"><tbody><tr>
    <td style="text-align:left;" id="lineColor"></td>
    <td style="text-align:right;" id="lineWidth"></td>
  </tr></tbody></table>

  <div style="margin:0;text-align:left; word-wrap: break-word; overflow:visible;" id="lineData"></div>

  <form style="padding:10px;">
  <input type="button" onclick="plotGraph(this);" value="Plot Chart">
  </form>
</div>
</div>
*/
//#section_end#
?>