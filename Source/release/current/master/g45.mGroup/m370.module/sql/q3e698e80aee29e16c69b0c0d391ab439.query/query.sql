-- Remove member from team
DELETE FROM PLM_accountToTeam
WHERE PLM_accountToTeam.account_id = {aid} AND PLM_accountToTeam.team_id = {tid};