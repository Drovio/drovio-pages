SELECT PLM_account.*, PLM_account.id AS accountID, RB_person.*
FROM PLM_account
LEFT OUTER JOIN PLM_personToAccount ON PLM_personToAccount.account_id = PLM_account.id
LEFT OUTER JOIN RB_person ON RB_person.id = PLM_personToAccount.person_id
INNER JOIN DEV_accountToProject ON DEV_accountToProject.account_id = PLM_account.id
WHERE DEV_accountToProject.project_id = {pid}
GROUP BY PLM_account.id;