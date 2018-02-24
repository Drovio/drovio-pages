jq = jQuery.noConflict();

// Add containers for name resolving
moduleGroup.addContainer(".vcsWorkingItems");
module.addContainer(".vcsWorkingItems");
sqlDomain.addContainer(".vcsWorkingItems");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");