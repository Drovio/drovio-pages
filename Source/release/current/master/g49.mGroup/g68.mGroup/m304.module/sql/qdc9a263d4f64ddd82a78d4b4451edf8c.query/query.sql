INSERT INTO BSS_app_market (application_id, active)
VALUES ({id}, {active})
ON DUPLICATE KEY UPDATE active = {active};