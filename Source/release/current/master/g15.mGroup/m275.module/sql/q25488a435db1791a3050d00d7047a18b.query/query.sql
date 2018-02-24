SELECT projectRelease.*, DEV_project.description, DEV_projectType.name AS type
FROM DEV_projectRelease AS projectRelease
INNER JOIN DEV_project ON DEV_project.id = projectRelease.project_id
INNER JOIN DEV_projectType ON DEV_project.projectType = DEV_projectType.id
WHERE DEV_project.projectType NOT IN (1, 2, 3, 5) AND time_created = (
	SELECT MAX(time_created)
	FROM DEV_projectRelease
	WHERE project_id = projectRelease.project_id
) AND projectRelease.status_id != 4
ORDER BY projectRelease.status_id ASC, time_created DESC