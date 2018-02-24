// Add container for name resolving
moduleGroup.addContainer("#moduleCommitManager");
module.addContainer("#moduleCommitManager");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");