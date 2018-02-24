// Add container for name resolving
moduleGroup.addContainer(".module_privileges");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");