-- Add application to boss market as inactive
INSERT IGNORE INTO BSS_app_market (application_id, active)
VALUES ({pid}, 0);

/* Add private team */
INSERT INTO BSS_app_private (team_id, application_id, time_created)
VALUES ({tid}, {pid}, {time});