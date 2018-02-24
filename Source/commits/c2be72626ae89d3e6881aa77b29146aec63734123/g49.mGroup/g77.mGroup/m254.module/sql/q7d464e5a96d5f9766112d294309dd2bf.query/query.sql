DELETE FROM DEV_project
WHERE DEV_project.projectType != 1 AND DEV_project.projectType != 2 AND DEV_project.projectType != 3 AND DEV_project.id = {pid};