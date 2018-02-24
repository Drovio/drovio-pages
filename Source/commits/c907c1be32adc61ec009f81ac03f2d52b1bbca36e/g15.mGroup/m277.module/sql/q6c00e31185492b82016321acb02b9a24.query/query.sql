SELECT projectType, open, COUNT(*) AS count
FROM DEV_project
GROUP BY projectType, open