drop table if exists words;
create table words (
       id int NOT NULL AUTO_INCREMENT,
       word varchar(50),
       success int,
       wordcount int,
       primary key(id)
);
