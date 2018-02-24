SELECT DEV_project.*, RB_team.name AS teamName
FROM DEV_project
INNER JOIN DEV_accountToProject on DEV_accountToProject.project_id = DEV_project.id
INNER JOIN RB_team ON DEV_project.team_id = RB_team.id
WHERE DEV_accountToProject.account_id = {id}