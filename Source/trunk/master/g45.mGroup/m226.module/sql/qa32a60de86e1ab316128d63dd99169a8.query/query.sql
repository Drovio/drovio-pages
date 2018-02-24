SELECT BSS_app_purchase.*, DEV_projectRelease.title
FROM BSS_app_purchase
INNER JOIN DEV_project ON BSS_app_purchase.application_id = DEV_project.id
INNER JOIN DEV_projectRelease ON DEV_projectRelease.project_id = BSS_app_purchase.application_id
WHERE BSS_app_purchase.team_id = {tid} AND (BSS_app_purchase.version != "" OR BSS_app_purchase.version IS NOT NULL) AND BSS_app_purchase.version = DEV_projectRelease.version;