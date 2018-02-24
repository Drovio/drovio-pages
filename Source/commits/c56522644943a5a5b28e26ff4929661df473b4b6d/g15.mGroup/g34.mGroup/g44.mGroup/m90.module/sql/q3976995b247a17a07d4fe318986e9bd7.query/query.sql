SELECT
	PLM_account.*,
	RB_person.username AS personUsername,
	RB_person.mail
FROM RB_person
INNER JOIN PLM_personToAccount ON PLM_personToAccount.person_id = RB_person.id
INNER JOIN PLM_account ON PLM_personToAccount.account_id = PLM_account.id
GROUP BY PLM_account.id;