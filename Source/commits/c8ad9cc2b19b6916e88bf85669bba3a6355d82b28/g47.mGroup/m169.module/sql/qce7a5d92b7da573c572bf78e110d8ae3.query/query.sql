SELECT AC_app.id, DEV_projectRelease.title, DEV_project.description, DEV_projectRelease.version, DEV_projectRelease.changelog
FROM AC_app
INNER JOIN DEV_projectRelease ON DEV_projectRelease.project_id = AC_app.id
INNER JOIN DEV_project ON DEV_project.id = AC_app.id
WHERE AC_app.id = {app_id} AND DEV_projectRelease.status_id = 2
ORDER BY DEV_projectRelease.time_created DESC
LIMIT 0,1;