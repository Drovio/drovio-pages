SELECT DEV_project.*
FROM DEV_project
WHERE DEV_project.team_id = {tid} AND DEV_project.projectType IN (5, 6, 7)
ORDER BY DEV_project.title ASC