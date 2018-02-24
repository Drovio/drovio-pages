SELECT PLM_account.*
FROM PLM_account
INNER JOIN PLM_accountAtGroup ON PLM_account.id = PLM_accountAtGroup.account_id
WHERE PLM_account.administrator = 1 AND (PLM_accountAtGroup.userGroup_id = 7 OR PLM_accountAtGroup.userGroup_id = 8) AND PLM_account.id IN (
	SELECT PLM_account.id
	FROM PLM_account
	INNER JOIN DEV_accountToProject ON PLM_account.id = DEV_accountToProject.account_id
	INNER JOIN DEV_project ON DEV_project.id = DEV_accountToProject.project_id
	WHERE DEV_project.public = 1
)
GROUP BY PLM_account.id