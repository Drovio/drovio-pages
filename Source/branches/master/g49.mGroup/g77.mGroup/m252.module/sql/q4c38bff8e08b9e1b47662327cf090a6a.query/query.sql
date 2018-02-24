SELECT COUNT(*) AS count
FROM TR_literalValue
INNER JOIN TR_literal ON TR_literalValue.literal_id = TR_literal.id
WHERE TR_literal.project_id = '{project_id}' AND TR_literalValue.locale = '{locale}'