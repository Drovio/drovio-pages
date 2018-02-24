-- Remove member from team
DELETE FROM DEV_accountToProject
WHERE DEV_accountToProject.account_id = {aid} AND DEV_accountToProject.project_id = {pid};