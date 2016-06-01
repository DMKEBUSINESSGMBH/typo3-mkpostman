#
# TABLE STRUCTURE FOR TABLE 'tx_mkpostman_subscribers'
#
CREATE TABLE tx_mkpostman_subscribers (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	gender tinyint(4) DEFAULT '0' NOT NULL,
	first_name varchar(60) DEFAULT '' NOT NULL,
	last_name varchar(60) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	confirmstring varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY email (email)
);
