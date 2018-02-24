SELECT BSS_app_private.*, RB_team.name AS team_name, BSS_app_purchase.version
FROM BSS_app_private
INNER JOIN RB_team ON BSS_app_private.team_id = RB_team.id
LEFT OUTER JOIN BSS_app_purchase ON BSS_app_purchase.team_id = RB_team.id AND BSS_app_purchase.application_id = BSS_app_private.application_id
WHERE BSS_app_private.application_id = {id};