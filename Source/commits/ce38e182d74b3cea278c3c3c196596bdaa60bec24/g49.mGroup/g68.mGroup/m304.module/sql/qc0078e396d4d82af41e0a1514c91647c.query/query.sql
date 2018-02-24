SELECT BSS_app_private.*, RB_team.name AS team_name
FROM BSS_app_private
INNER JOIN RB_team ON BSS_app_private.team_id = RB_team.id
WHERE BSS_app_private.application_id = {id};