// Add container for name resolving
dvbDomain.addContainer("#ajaxCommitManager");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");