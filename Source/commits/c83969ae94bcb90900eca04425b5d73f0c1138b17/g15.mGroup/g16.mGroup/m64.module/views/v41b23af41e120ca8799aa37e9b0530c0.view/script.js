// Add container for name resolving
moduleGroup.addContainer(".uiVCSControl");
module.addContainer(".uiVCSControl");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");