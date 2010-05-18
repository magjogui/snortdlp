DROP TABLE IF EXISTS config;
DROP TABLE IF EXISTS sensitive_file_paths;
DROP TABLE IF EXISTS words;
CREATE TABLE config (
        config_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        substr_length INT,
	snort_rules_path VARCHAR(100)
	);
CREATE TABLE sensitive_file_paths (
	spath_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	config_id INT NOT NULL, 	
	path VARCHAR(100),
	FOREIGN KEY (config_id) REFERENCES config(config_id)
	);
CREATE TABLE words (
	words_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	word VARCHAR(200)
	);
