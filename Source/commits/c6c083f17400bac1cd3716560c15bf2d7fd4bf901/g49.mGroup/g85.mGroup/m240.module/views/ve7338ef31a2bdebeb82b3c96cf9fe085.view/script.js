// Add container for name resolving
moduleGroup.addContainer(".uiCommitManager");
module.addContainer(".uiCommitManager");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");