-- Create team
INSERT INTO RB_team (uname, name)
VALUES ('{uname}', '{name}');

-- Get team id
SELECT last_insert_id() INTO @teamID;

-- Add account to team
INSERT INTO PLM_accountToTeam (account_id, team_id)
VALUES ({aid}, @teamID);

-- Return team id
SELECT @teamID AS id;