SELECT
	BSS_app_market.application_id,
	BSS_app_market.price,
	DEV_projectRelease.title,
	DEV_project.description,
	DEV_projectRelease.version,
	DEV_projectRelease.changelog
FROM BSS_app_market
INNER JOIN DEV_projectRelease ON DEV_projectRelease.project_id = BSS_app_market.application_id
INNER JOIN DEV_project ON DEV_project.id = BSS_app_market.application_id
WHERE DEV_project.projectType NOT IN (1, 2, 3, 10) AND DEV_project.online = 1 AND DEV_projectRelease.status_id = 2 AND DEV_projectRelease.time_created = (
	SELECT MAX(DEV_projectRelease.time_created)
	FROM DEV_projectRelease
	WHERE DEV_projectRelease.project_id = BSS_app_market.application_id
) AND (BSS_app_market.active = 1 OR BSS_app_market.application_id IN (
	SELECT BSS_app_private.application_id
	FROM BSS_app_private
	WHERE BSS_app_private.team_id = {tid}
))