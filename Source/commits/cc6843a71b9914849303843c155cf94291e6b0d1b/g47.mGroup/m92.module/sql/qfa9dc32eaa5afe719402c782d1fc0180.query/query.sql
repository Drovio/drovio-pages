SELECT AC_app.id, DEV_project.title, DEV_projectRelease.title as rTitle, DEV_project.name, DEV_project.description, DEV_projectRelease.version, DEV_projectRelease.changelog, RB_team.name as teamName
FROM AC_app
INNER JOIN DEV_projectRelease ON DEV_projectRelease.project_id = AC_app.id
INNER JOIN DEV_project ON DEV_project.id = AC_app.id
INNER JOIN RB_team ON DEV_project.team_id = RB_team.id
WHERE DEV_project.projectType NOT IN (1, 2, 3, 10) AND DEV_project.online = 1 AND DEV_projectRelease.status_id = 2 AND AC_app.active = 1 AND DEV_projectRelease.time_created = (
	SELECT MAX(DEV_projectRelease.time_created)
	FROM DEV_projectRelease
	WHERE DEV_projectRelease.project_id = AC_app.id
)