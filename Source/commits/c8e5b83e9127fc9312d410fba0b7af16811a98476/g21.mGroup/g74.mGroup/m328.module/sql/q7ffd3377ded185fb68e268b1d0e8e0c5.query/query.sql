UPDATE PLM_account
SET password = '{password}'
WHERE id = {id} AND administrator = 0 AND parent_id IS NOT NULL AND parent_id = {pid};