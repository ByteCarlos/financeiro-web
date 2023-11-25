ALTER TABLE receita 
ADD rubrica_fk int(11) NULL;

ALTER TABLE receita 
ADD FOREIGN KEY (rubrica_fk) REFERENCES rubrica(id);