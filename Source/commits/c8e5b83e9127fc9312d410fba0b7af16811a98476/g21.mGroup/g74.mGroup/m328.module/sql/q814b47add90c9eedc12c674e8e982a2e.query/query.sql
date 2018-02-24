UPDATE PLM_account
SET
	title = '{title}',
	description = '{description}',
	locked = {locked},
	username = '{username}'
WHERE id = {id} AND administrator = 0 AND parent_id IS NOT NULL AND parent_id = {pid};