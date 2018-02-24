DELETE FROM PLM_userGroupCommand
WHERE module_id = {mid} AND userGroup_id IN ({ids});