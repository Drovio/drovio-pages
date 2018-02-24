SELECT DEV_project.*, RB_team.name AS teamName
FROM DEV_project
INNER JOIN RB_team ON DEV_project.team_id = RB_team.id
WHERE DEV_project.team_id = {id}