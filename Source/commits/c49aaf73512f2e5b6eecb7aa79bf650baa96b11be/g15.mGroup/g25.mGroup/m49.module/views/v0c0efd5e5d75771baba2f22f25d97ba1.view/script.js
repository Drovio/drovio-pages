// Add container for name resolving
dvbDomain.addContainer("#sqlCommitManager");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");