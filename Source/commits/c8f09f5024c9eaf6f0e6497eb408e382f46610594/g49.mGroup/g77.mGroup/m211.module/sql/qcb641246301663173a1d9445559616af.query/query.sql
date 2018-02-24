-- Remove member from team
DELETE FROM DEV_accountToProject
WHERE DEV_accountToProject.account_id = {aid} AND DEV_accountToProject.project_id = {pid};

-- Remove keys
DELETE FROM PLM_accountKey
WHERE PLM_accountKey.account_id = {aid} AND PLM_accountKey.type_id = 2 AND PLM_accountKey.context = {pid};