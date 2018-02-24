UPDATE DEV_projectRelease
SET comments = '{comments}', time_updated = {time}, status_id = {status}
WHERE DEV_projectRelease.project_id = {pid}