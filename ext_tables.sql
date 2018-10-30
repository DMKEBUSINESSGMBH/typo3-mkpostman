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
    disabled tinyint(4) DEFAULT '0' NOT NULL,

    gender tinyint(4) DEFAULT '0' NOT NULL,
    first_name varchar(60) DEFAULT '' NOT NULL,
    last_name varchar(60) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,
    confirmstring varchar(32) DEFAULT '' NOT NULL,

    ### fields for direct_mail
    name varchar(140) DEFAULT '' NOT NULL,
    module_sys_dmail_html tinyint(3) unsigned NOT NULL default '1',

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY email (email)
);
