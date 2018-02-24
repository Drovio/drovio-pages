SELECT LIB_teamProject.*, DEV_projectRelease.title
FROM LIB_teamProject
INNER JOIN DEV_project ON LIB_teamProject.project_id = DEV_project.id
INNER JOIN DEV_projectRelease ON DEV_projectRelease.project_id = LIB_teamProject.project_id
WHERE LIB_teamProject.team_id = {tid} AND (LIB_teamProject.version != "" OR LIB_teamProject.version IS NOT NULL) AND LIB_teamProject.version = DEV_projectRelease.version;