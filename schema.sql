/*
* Supportix Database Schema v2 (MVC)
* Powered by Evilnapsis
*/

CREATE DATABASE IF NOT EXISTS supportix;
USE supportix;
SET sql_mode='';

CREATE TABLE IF NOT EXISTS user (
	id int not null auto_increment primary key,
	username varchar(50),
	name varchar(50),
	lastname varchar(50),
	email varchar(255),
	password varchar(60),
	is_active boolean not null default 1,
	kind int not null default 1, /* 1: Administrador, 2: Usuario normal */
	created_at datetime
);

INSERT INTO user (username, password, kind, is_active, created_at)
SELECT 'admin', '90b9aa7e25f80cf4f64e990b78a9fc5ebd6cecad', 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM user WHERE username = 'admin');

CREATE TABLE IF NOT EXISTS project (
	id int not null auto_increment primary key,
	name varchar(200),
	description text
);

CREATE TABLE IF NOT EXISTS category (
	id int not null auto_increment primary key,
	name varchar(200)
);

CREATE TABLE IF NOT EXISTS kind (
	id int not null auto_increment primary key,
	name varchar(100)
);

INSERT INTO kind (id, name) VALUES
(1, 'Ticket'),
(2, 'Bug'),
(3, 'Sugerencia'),
(4, 'Caracteristica')
ON DUPLICATE KEY UPDATE name=VALUES(name);

CREATE TABLE IF NOT EXISTS status (
	id int not null auto_increment primary key,
	name varchar(100)
);

INSERT INTO status (id, name) VALUES
(1, 'Pendiente'),
(2, 'En Desarrollo'),
(3, 'Terminado'),
(4, 'Cancelado')
ON DUPLICATE KEY UPDATE name=VALUES(name);

CREATE TABLE IF NOT EXISTS priority (
	id int not null auto_increment primary key,
	name varchar(100)
);

INSERT INTO priority (id, name) VALUES
(1, 'Alta'),
(2, 'Media'),
(3, 'Baja')
ON DUPLICATE KEY UPDATE name=VALUES(name);

CREATE TABLE IF NOT EXISTS ticket (
	id int not null auto_increment primary key,
	title varchar(100),
	description text,
	updated_at datetime,
	created_at datetime,
	kind_id int not null,
	user_id int not null,
	asigned_id int,
	project_id int,
	category_id int,
	priority_id int not null default 1,
	status_id int not null default 1,
	foreign key (priority_id) references priority(id),
	foreign key (status_id) references status(id),
	foreign key (user_id) references user(id),
	foreign key (kind_id) references kind(id),
	foreign key (category_id) references category(id),
	foreign key (project_id) references project(id)
);
