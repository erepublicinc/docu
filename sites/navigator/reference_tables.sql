create schema reference;
grant select on reference.* to 'web_sites'@'%';
grant select, insert, update, delete on reference.* to 'web_sites_admin'@'%';


CREATE TABLE reference.counties_fips
(
    state  VARCHAR(20) NOT NULL,
    state_fipscode  CHAR(2) NOT NULL,
    county  VARCHAR(100) NOT NULL,
    county_fipscode  CHAR(3) NOT NULL,
    fipscode CHAR(5),
    PRIMARY KEY (state_fipscode,county_fipscode)
)engine InnoDB;

CREATE TABLE reference.k12_districts
(
    ncescode VARCHAR(20) NOT NULL,
    name  varchar (100),
    county  VARCHAR(100) NOT NULL,
    county_fips  VARCHAR(20) NOT NULL,
    mail_adress VARCHAR(100),
    mail_city VARCHAR(100),
    mail_state VARCHAR(20) NOT NULL,
    mail_zip VARCHAR(10),
    phone VARCHAR(15),
    state_fips  VARCHAR(100) NOT NULL,
    district_type  varchar (100),
    num_schools INT,
    students INT,
    teachers INT,
    PRIMARY KEY (ncescode)
)engine InnoDB;