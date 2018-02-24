SELECT PLM_account.*
FROM PLM_account
INNER JOIN PLM_personToAccount ON PLM_personToAccount.account_id = PLM_account.id
INNER JOIN RB_person ON PLM_personToAccount.person_id = RB_person.id
WHERE RB_person.mail = '{mail}' AND PLM_account.administrator = 1;