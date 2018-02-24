// Set sql domain query name resolving
sqlDomain.addContainer(".uiVCSControl");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");