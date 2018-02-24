UPDATE DEV_projectRelease
SET comments = '{comments}', time_updated = {time}, status_id = {status}, review_account_id = {raid}
WHERE DEV_projectRelease.project_id = {pid} and DEV_projectRelease.version = '{version}'