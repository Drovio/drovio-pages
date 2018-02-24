UPDATE PLM_account
SET
	title = '{title}',
	description = '{description}',
	locked = {locked}
WHERE id = {aid};