UPDATE DEV_roadmap
SET
	title = '{title}',
	description = '{description}',
	hashtag = '{hashtag}',
	date_expected = '{date_expected}',
	date_delivered = IF('{date_delivered}' = 'NULL', NULL, '{date_delivered}')
WHERE id = {rid};