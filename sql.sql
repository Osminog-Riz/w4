CREATE TABLE app4 (
    id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    name varchar(128) NOT NULL,
    email varchar(256) NOT NULL,
    age int(4) UNSIGNED NOT NULL,
    sex varchar(6) NOT NULL,
    limbs int(2) UNSIGNED NOT NULL,
    powers varchar(512) NULL,
    bio varchar(512) NOT NULL,
    PRIMARY KEY (id)
);
