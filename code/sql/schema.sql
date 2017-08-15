CREATE DATABASE api;
USE api;
CREATE TABLE api.users (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  picture varchar(200) DEFAULT NULL,
  address text,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

