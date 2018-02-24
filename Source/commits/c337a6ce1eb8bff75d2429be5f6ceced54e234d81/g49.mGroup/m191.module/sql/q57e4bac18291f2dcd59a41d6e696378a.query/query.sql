SELECT
	RB_person.id as personID,
	RB_person.username,
	RB_person.firstname,
	RB_person.lastname,
	RB_person.mail,
	PLM_account.id as accountID,
	PLM_account.title as accountTitle,
	PLM_account.username as accountName,
	PLM_account.administrator
FROM PLM_account
INNER JOIN PLM_personToAccount ON PLM_account.id = PLM_personToAccount.account_id
INNER JOIN RB_person ON PLM_personToAccount.person_id = RB_person.id
WHERE PLM_account.id = '{id}' OR PLM_account.username = '{name}' OR RB_person.username = '{name}'