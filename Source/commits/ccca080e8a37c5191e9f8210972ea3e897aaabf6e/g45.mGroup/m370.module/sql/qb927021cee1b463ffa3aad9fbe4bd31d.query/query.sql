SELECT PLM_accountToTeam.account_id
FROM PLM_accountToTeam
INNER JOIN RB_team ON PLM_accountToTeam.team_id = RB_team.id
WHERE RB_team.id = '{tid}' OR RB_team.uname = '{tname}';