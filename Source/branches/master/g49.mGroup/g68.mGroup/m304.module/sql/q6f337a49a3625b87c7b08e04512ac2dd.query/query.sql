INSERT INTO AC_app (id, active)
VALUES ({id}, {active})
ON DUPLICATE KEY UPDATE active = {active};