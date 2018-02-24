SELECT DEV_project.id, DEV_project.description, DEV_projectRelease.version, DEV_projectRelease.title, .DEV_projectRelease.changelog
FROM DEV_project
INNER JOIN DEV_projectRelease ON DEV_project.id = DEV_projectRelease.project_id
WHERE DEV_project.id = {id} AND DEV_projectRelease.version = '{version}'