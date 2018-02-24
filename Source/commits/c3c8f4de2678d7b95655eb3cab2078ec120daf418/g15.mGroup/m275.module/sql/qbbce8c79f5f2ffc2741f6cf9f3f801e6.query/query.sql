SELECT *
FROM (
	SELECT DEV_projectRelease.*, DEV_project.title as projectTitle, DEV_projectType.name AS type
	FROM DEV_projectRelease
	INNER JOIN DEV_project ON DEV_project.id = DEV_projectRelease.project_id
	INNER JOIN DEV_projectType ON DEV_project.projectType = DEV_projectType.id
	WHERE DEV_project.projectType NOT IN (1, 2, 3)
) AS projectRel
WHERE projectRel.status_id != 1
ORDER BY time_updated DESC;