// Add containers for name resolving
moduleGroup.addContainer("#commits");
module.addContainer("#commits");
sqlDomain.addContainer("#commits");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");