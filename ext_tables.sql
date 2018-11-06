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
    module_sys_dmail_newsletter tinyint(3) unsigned DEFAULT '0' NOT NULL,
    module_sys_dmail_category int(10) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY email (email)
);

#
# TABLE STRUCTURE FOR TABLE 'tx_mkpostman_logs'
#
CREATE TABLE tx_mkpostman_logs (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,

    cruser_id int(11) DEFAULT '0' NOT NULL,
    subscriber_id int(11) DEFAULT '0' NOT NULL,

    ### registered, activated, unsubscribed
    state tinyint(4) DEFAULT '0' NOT NULL,
    description tinytext DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY subscriber (subscriber_id)
);



#
# Table structure for table 'tx_mkpostman_subscribers_dmail_category_mm'
#
CREATE TABLE tx_mkpostman_subscribers_dmail_category_mm (
    uid_local int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
    tablenames varchar(30) DEFAULT '' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);