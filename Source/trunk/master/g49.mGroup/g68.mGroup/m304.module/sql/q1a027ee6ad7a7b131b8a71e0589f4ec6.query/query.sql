SELECT RB_team.*
FROM RB_team
WHERE RB_team.name = '{q}' AND RB_team.id NOT IN (
	SELECT BSS_app_private.team_id
	FROM BSS_app_private
	WHERE BSS_app_private.application_id = {pid}
)