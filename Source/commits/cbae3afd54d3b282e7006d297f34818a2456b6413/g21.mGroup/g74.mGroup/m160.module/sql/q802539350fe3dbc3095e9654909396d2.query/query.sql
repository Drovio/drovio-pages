UPDATE RB_person 
SET
	firstname = '{firstname}', 
	middle_name = '{middle_name}', 
	lastname = '{lastname}'
WHERE RB_person.id = {pid};