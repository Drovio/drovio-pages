-- Create team
INSERT INTO RB_team (name)
VALUES ('{name}');

-- Get team id
SELECT last_insert_id() INTO @teamID;

-- Add account to team
INSERT INTO PLM_accountToTeam (account_id, team_id)
VALUES ({aid}, @teamID);

-- Return team id
SELECT @teamID AS id;