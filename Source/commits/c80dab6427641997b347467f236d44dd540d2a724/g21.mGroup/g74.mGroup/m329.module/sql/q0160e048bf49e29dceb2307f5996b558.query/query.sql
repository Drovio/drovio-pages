-- Update person username
UPDATE RB_person
SET username = '{username}' 
WHERE id = {pid};

-- Update account username
UPDATE PLM_account
SET username = '{username}' 
WHERE id = {aid};