-- Update Password and delete reset id
UPDATE PLM_account
SET password = '{password}', reset = ''
WHERE PLM_account.id = {aid};