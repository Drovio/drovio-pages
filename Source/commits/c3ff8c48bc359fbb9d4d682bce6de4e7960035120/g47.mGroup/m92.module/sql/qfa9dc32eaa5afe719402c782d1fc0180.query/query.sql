SELECT AC_app.id, DEV_projectRelease.title, DEV_project.description, DEV_projectRelease.version, DEV_projectRelease.changelog
FROM AC_app
INNER JOIN DEV_projectRelease ON DEV_projectRelease.project_id = AC_app.id
INNER JOIN DEV_project ON DEV_project.id = AC_app.id
WHERE DEV_project.projectType NOT IN (1, 2, 3, 10) AND DEV_project.online = 1 AND DEV_projectRelease.status_id = 2 AND AC_app.active = 1 AND DEV_projectRelease.time_created = (
	SELECT MAX(DEV_projectRelease.time_created)
	FROM DEV_projectRelease
	WHERE DEV_projectRelease.project_id = AC_app.id
)