-- Remove member from team
DELETE FROM PLM_accountToTeam
WHERE PLM_accountToTeam.account_id = {aid} AND PLM_accountToTeam.team_id = {tid};

-- Remove keys
DELETE FROM PLM_accountKey
WHERE PLM_accountKey.account_id = {aid} AND PLM_accountKey.type_id = 1 AND PLM_accountKey.context = {tid};