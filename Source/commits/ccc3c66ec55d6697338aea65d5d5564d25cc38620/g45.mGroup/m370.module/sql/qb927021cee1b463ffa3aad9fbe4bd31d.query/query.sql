SELECT PLM_account.*
FROM PLM_account
INNER JOIN PLM_accountToTeam ON PLM_accountToTeam.account_id = PLM_account.id
INNER JOIN RB_team ON PLM_accountToTeam.team_id = RB_team.id
WHERE RB_team.id = '{tid}' OR RB_team.uname = '{tname}';