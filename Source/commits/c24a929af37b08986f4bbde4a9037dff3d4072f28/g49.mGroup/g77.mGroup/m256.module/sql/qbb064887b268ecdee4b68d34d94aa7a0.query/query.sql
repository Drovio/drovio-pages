INSERT INTO BSS_app (id, price, active, tags)
VALUES ({id}, {active}, {price}, '{tags}')
ON DUPLICATE KEY UPDATE active = {active}, price = {price}, tags = '{tags}';