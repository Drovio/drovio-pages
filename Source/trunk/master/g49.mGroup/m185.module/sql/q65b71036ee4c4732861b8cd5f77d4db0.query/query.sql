SELECT DEV_project.*
FROM DEV_project
WHERE DEV_project.team_id = {tid}
ORDER BY DEV_project.title ASC