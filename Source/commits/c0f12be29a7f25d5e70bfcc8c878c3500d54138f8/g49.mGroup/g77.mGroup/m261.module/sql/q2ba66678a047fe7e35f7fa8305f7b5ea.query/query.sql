UPDATE DEV_projectRelease
SET comments = 'OK', time_updated = {time}, status_id = 2
WHERE DEV_projectRelease.project_id = {pid} and DEV_projectRelease.version = '{version}'