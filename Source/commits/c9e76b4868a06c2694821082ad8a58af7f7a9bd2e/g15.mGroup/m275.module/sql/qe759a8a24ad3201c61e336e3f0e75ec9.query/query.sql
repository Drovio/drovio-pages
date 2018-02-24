SELECT PLM_account.*, RB_person.mail
FROM PLM_account
INNER JOIN PLM_personToAccount ON PLM_personToAccount.account_id = PLM_account.id
INNER JOIN RB_person ON PLM_personToAccount.person_id = RB_person.id
INNER JOIN DEV_accountToProject ON DEV_accountToProject.account_id = PLM_account.id
WHERE PLM_account.administrator = 1 AND DEV_accountToProject.project_id = {pid}