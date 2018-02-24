SELECT LIB_teamProject.*, DEV_project.title
FROM LIB_teamProject
INNER JOIN DEV_project ON LIB_teamProject.project_id = DEV_project.id
WHERE LIB_teamProject.team_id = {tid} AND (LIB_teamProject.version != "" OR LIB_teamProject.version IS NOT NULL);