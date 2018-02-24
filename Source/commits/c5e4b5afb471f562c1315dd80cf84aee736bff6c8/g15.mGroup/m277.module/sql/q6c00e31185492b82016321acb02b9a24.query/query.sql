SELECT projectType, public, COUNT(*) AS count
FROM DEV_project
GROUP BY projectType, public