SELECT DEV_project.*, DEV_projectType.name AS projectTypeName
FROM DEV_project
INNER JOIN DEV_projectType ON DEV_project.projectType = DEV_projectType.id
WHERE projectType IN (5, 6, 7);