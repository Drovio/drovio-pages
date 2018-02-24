-- Create account
INSERT INTO PLM_account (title, description, username, password, locked, administrator, parent_id)
VALUES ('{title}', '{description}', IF('{username}' = '', NULL, '{username}'), '{password}', {locked}, 0, {aid});

-- Get inserted account id
SELECT last_insert_id() INTO @accountID;

/* Link to person */
INSERT INTO PLM_personToAccount (person_id, account_id)
VALUES ({pid}, @accountID)