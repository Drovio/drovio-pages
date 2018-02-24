// Add container for name resolving
dvbDomain.addContainer("#sdkCommitManager");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");