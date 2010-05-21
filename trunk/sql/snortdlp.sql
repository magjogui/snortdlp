DROP TABLE IF EXISTS rules;
DROP TABLE IF EXISTS config;
DROP TABLE IF EXISTS folders;
DROP TABLE IF EXISTS db_names;
DROP TABLE IF EXISTS users;
CREATE TABLE config (
        config_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        substr_length INT,
	snort_rules_path VARCHAR(100)
	);
INSERT INTO config VALUES (null, 10, '/etc/snort/rules/');
CREATE TABLE folders (
	folder_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	path VARCHAR(200)
	);
CREATE TABLE users (
        user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50),
	password VARCHAR(50)
	);
CREATE TABLE db_names (
        database_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50),
	password VARCHAR(50),
	table_name VARCHAR(50),
	ip_addr VARCHAR(20),
	port INT
	);
CREATE TABLE rules (
	rule_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	folder_id INT,
	file_name VARCHAR(200),
	rule VARCHAR(300),
	FOREIGN KEY (folder_id) REFERENCES folders(folder_id)
	);
