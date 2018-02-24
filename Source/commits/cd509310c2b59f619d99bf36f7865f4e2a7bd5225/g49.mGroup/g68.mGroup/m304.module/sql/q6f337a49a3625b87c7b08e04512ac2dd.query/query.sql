INSERT INTO AC_app (id, active, tags)
VALUES ({id}, {active}, '{tags}')
ON DUPLICATE KEY UPDATE active = {active}, tags = '{tags}';