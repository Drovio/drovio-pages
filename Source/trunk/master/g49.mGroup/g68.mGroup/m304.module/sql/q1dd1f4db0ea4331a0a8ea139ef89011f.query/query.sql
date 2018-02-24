INSERT INTO BSS_app_market (application_id, active, price, tags)
VALUES ({id}, {active}, {price}, '{tags}')
ON DUPLICATE KEY UPDATE active = {active}, price = {price}, tags = '{tags}';