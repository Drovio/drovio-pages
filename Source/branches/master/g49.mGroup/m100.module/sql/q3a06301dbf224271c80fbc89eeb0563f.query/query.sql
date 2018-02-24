SELECT DEV_project.*, RB_team.name AS teamName
FROM DEV_project
INNER JOIN DEV_accountToProject on DEV_accountToProject.project_id = DEV_project.id
INNER JOIN RB_team on RB_team.id = DEV_project.team_id
WHERE DEV_accountToProject.account_id = {aid}