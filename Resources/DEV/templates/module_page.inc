<?php
// Page Id
$pageID = {pageID};

// Load System Variables
require_once($_SERVER['DOCUMENT_ROOT'].'/_domainConfig.php');

// Use System Importer
use \API\Platform\importer;

// Load Page Content
importer::import("ESS", "Protocol", "loaders/PageLoader");
use \ESS\Protocol\loaders\PageLoader;
PageLoader::load($pageID);
?>