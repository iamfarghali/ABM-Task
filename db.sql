
-- Create [task] Database
	CREATE DATABASE IF NOT EXISTS `task` CHARACTER SET = `utf8` COLLATE `utf8_general_ci`;

-- Create [user] Table  
	CREATE TABLE `task`.`user` (
		`id` 	int(11) NOT NULL AUTO_INCREMENT,
		`name` 	varchar(50) NOT NULL,
		`age`	TINYINT UNSIGNED NOT NULL,
		PRIMARY KEY (`id`)
	);