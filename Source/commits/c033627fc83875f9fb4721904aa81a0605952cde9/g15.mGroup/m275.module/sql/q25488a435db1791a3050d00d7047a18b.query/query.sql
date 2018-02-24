SELECT projectRelease.*, RB_team.name as teamName, DEV_project.description, DEV_projectType.name AS type, PLM_account.title AS reviewAccountTitle
FROM DEV_projectRelease AS projectRelease
INNER JOIN DEV_project ON DEV_project.id = projectRelease.project_id
INNER JOIN RB_team ON DEV_project.team_id = RB_team.id
INNER JOIN PLM_account ON PLM_account.id = projectRelease.review_account_id
INNER JOIN DEV_projectType ON DEV_project.projectType = DEV_projectType.id
WHERE DEV_project.projectType NOT IN (1, 2, 3, 5) AND time_created = (
	SELECT MAX(time_created)
	FROM DEV_projectRelease
	WHERE project_id = projectRelease.project_id
) AND projectRelease.status_id != 4
ORDER BY projectRelease.status_id ASC, time_created DESC