INSERT INTO BSS_app (id, active, price, tags)
VALUES ({id}, {active}, {price}, '{tags}')
ON DUPLICATE KEY UPDATE active = {active}, price = {price}, tags = '{tags}';