CREATE DATABASE PAASAL_RIYA_DB_01;
USE PAASAL_RIYA_DB_01;

CREATE TABLE Users(

user_id INT auto_increment primary Key NOT NULL,
username VARCHAR(255),
email VARCHAR(255),
password TEXT

);

create index idx_userid on Users(user_id);

select * from Users;