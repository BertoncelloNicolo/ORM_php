--create_database
CREATE DATABASE IF NOT EXISTS :dbname;
--create_table
CREATE TABLE IF NOT EXISTS :dbname.:tbname(id int not null auto_increment primary key, codice int, titolo varchar(50), descrizione varchar(50), dataConcerto DateTime);
--insert_record
INSERT INTO :dbname.:tbname (codice, titolo, descrizione, dataConcerto) VALUES (:codice, :titolo, :descrizione, :dataConcerto);
--select_id
SELECT * FROM :dbname.:tbname where id = :id;
--select_all
SELECT * FROM :dbname.:tbname;
--delete_id
DELETE FROM :dbname.:tbname where id = :id;
--update_codice
UPDATE :dbname.:tbname SET codice=:codice WHERE id = :id;
--update_titolo
UPDATE :dbname.:tbname SET titolo=:titolo WHERE id = :id;
--update_descrizione
UPDATE :dbname.:tbname SET descrizione=:descrizione WHERE id = :id;
--update_data
UPDATE :dbname.:tbname SET dataConcerto=:dataConcerto WHERE id = :id;
--insert_record_sala
INSERT INTO :dbname.:tbname (codice, nome, capienza) VALUES (:codice, :nome, :capienza);
--select_sala_concerti
SELECT * FROM :dbname.:tbname where concerto_id = :id;


