SELECT PLM_account.*, RB_person.mail
FROM PLM_account
INNER JOIN PLM_personToAccount ON PLM_personToAccount.account_id = PLM_account.id
INNER JOIN RB_person ON PLM_personToAccount.person_id = RB_person.id
WHERE (PLM_account.username = '{q}' OR ((RB_person.username = '{q}' OR RB_person.mail = '{q}') AND PLM_account.administrator = 1))
GROUP BY PLM_account.id;