SELECT node.id, node.description, (COUNT(parent.description) - 1) AS depth, parent.id AS parent_id
FROM UNIT_moduleGroup AS node, UNIT_moduleGroup AS parent 
WHERE node.lft BETWEEN parent.lft AND parent.rgt 
GROUP BY node.id 
ORDER BY node.lft ASC