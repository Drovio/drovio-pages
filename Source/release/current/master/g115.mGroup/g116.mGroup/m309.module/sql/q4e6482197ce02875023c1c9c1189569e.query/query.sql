SELECT PLM_account.*, RB_person.*
FROM PLM_account
INNER JOIN PLM_personToAccount ON PLM_personToAccount.account_id = PLM_account.id
INNER JOIN RB_person ON PLM_personToAccount.person_id = RB_person.id
INNER JOIN DEV_accountToProject ON DEV_accountToProject.account_id = PLM_account.id
INNER JOIN PLM_accountKey ON PLM_accountKey.account_id = PLM_account.id
WHERE DEV_accountToProject.project_id = {id} AND PLM_accountKey.context = {id} AND PLM_accountKey.type_id = 2 AND PLM_accountKey.userGroup_id = 7
GROUP BY PLM_account.id