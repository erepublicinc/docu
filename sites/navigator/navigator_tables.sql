 
-- navigator tables

--  requirements , fast simple, less joins, less corruption through constraints 

-- data model changes:
-- 1 table for jurisdictions, institutions and agencies called "orgs"
-- extra tables for jurisdiction or institution data
-- tables org_types, and org_functional_types (agency types)

-- a contact_role   link table between contacts and orgs which contains the contact info

-- contact information is inside the bid

-- have a generic account record,  with linked nav_users extension records

-- no more permissions,  just usergroups (so only 1 join is needed )

--  =========== QUESTIONS
--  do we want to keep the old pk, it looks messy, but since pks are unique, it will give the updatescript more robustness through the relational constraints

-- backup and restore the database as root:
-- $ mysqldump -u root -p  --opt navigator > dump.sql
-- $ mysql -u root -p  --database=navigator < dump.sql

--  to create this database
-- mysql -u root -p  --database=navigator < navigator_tables.sql


drop database navigator;
create schema navigator;
use navigator;
grant select on navigator.* to 'web_sites'@'%';
grant select, insert, update, delete on navigator.* to 'web_sites_admin'@'%';


CREATE TABLE navigator.states
(
    states_code  CHAR(2) NOT NULL,
    states_title VARCHAR(40) NOT NULL,
    states_fips_code CHAR(2),
    states_territories BIT DEFAULT 0,
    states_pk    INT, CONSTRAINT states_pk_unique UNIQUE (states_pk),
    PRIMARY KEY (states_code)
) engine InnoDB ; 

CREATE TABLE navigator.accounts
    (
        accounts_id             INT NOT NULL AUTO_INCREMENT,
        accounts_title             VARCHAR(40) NOT NULL,
        accounts_sf_id             VARCHAR(40),
        accounts_sf_deleted        BIT DEFAULT 0 NOT NULL,        
        accounts_is_navigator     BIT DEFAULT 1 NOT NULL,
        CONSTRAINT accounts_title_unique UNIQUE (accounts_title),
           accounts_pk    INT, CONSTRAINT accounts_pk_unique UNIQUE (accounts_pk),
           
        PRIMARY KEY (accounts_id)
    ) engine InnoDB ; 

  
CREATE TABLE navigator.users
    (
        users_id             INT NOT NULL AUTO_INCREMENT,
        users_last_name     VARCHAR(20) ,
        users_first_name     VARCHAR(20),
        users_job_title     VARCHAR(50),
        users_password         VARCHAR(60) ,
        users_email         VARCHAR(70)   NOT NULL,
        users_ad_user         VARCHAR(20) ,
        users_accounts_fid  INT NOT NULL,
        users_notes         VARCHAR(255) ,
        users_active         BIT DEFAULT 1 NOT NULL,
        users_salesforce_fid VARCHAR(20),
        users_internal         BIT DEFAULT 0 NOT NULL,  -- if this is 0 it requires a subclassed user, like nav_user 
        users_type          VARCHAR(20)  ,   -- ?? do we need this use roles instead?  EREPUBLIC, NAVIGATOR, BLOGGER
           users_pk    INT, CONSTRAINT users_pk_unique UNIQUE (users_pk),
        PRIMARY KEY (users_id),
        CONSTRAINT  FOREIGN KEY (users_accounts_fid) REFERENCES accounts (accounts_id),
        CONSTRAINT users_email_unique UNIQUE (users_email)
    ) engine InnoDB ; 

CREATE TABLE navigator.usergroups   
    (
         usergroups_code    VARCHAR(40) NOT NULL ,
         usergroups_title   VARCHAR(255) NOT NULL ,
         usergroups_summary VARCHAR(255) NOT NULL ,
         PRIMARY KEY (usergroups_code)
    ) engine InnoDB ;

CREATE TABLE navigator.users_x_usergroups   
    (
        users_fid   INT NOT NULL ,
        usergroups_fcode  VARCHAR(40) NOT NULL ,
        CONSTRAINT  FOREIGN KEY (users_fid) REFERENCES users (users_id) ON DELETE CASCADE,  
        CONSTRAINT  FOREIGN KEY (usergroups_fcode) REFERENCES usergroups (usergroups_code) ON DELETE CASCADE,      
        PRIMARY KEY (users_fid, usergroups_fcode)
    ) engine InnoDB;  


    
CREATE TABLE navigator.logins
    (
        logins_users_fid     INT NOT NULL ,
        logins_timestamp    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        logins_site_code     SET('DGN','DEN','EMN') NOT NULL,
        logins_method         VARCHAR(20) COMMENT 'values: PASSWORD, COOKIE',
        logins_browser         VARCHAR(100) COMMENT 'the user agent string',
        logins_http_address VARCHAR(50),
        logins_http_port     NUMERIC,
        PRIMARY KEY (logins_users_fid, logins_timestamp),
        CONSTRAINT  FOREIGN KEY (logins_users_fid) REFERENCES users (users_id)
    ) engine InnoDB;   
    

CREATE TABLE navigator.licenses -- used to be nav-accounts   nava_
    (
        licenses_accounts_fid       INT NOT NULL ,
        licenses_site_code          SET('DGN','DEN','EMN') NOT NULL,
        licenses_access_level       SET('STANDARD','PREMIUM') NOT NULL,     
        licenses_number_of_users    INT NOT NULL,
        licenses_status              SET('EXPIRED','RENEWAL','ACTIVE','CANCELED','TRIAL') NOT NULL,
        licenses_expiration         DATETIME NOT NULL,
        licenses_monthly_bonus_hours INT NOT NULL,
        licenses_account_rep_fid     INT NOT NULL,
        CONSTRAINT  FOREIGN KEY (licenses_account_rep_fid) REFERENCES users (users_id), 
        CONSTRAINT  FOREIGN KEY (licenses_accounts_fid)   REFERENCES accounts (accounts_id),  
        PRIMARY KEY (licenses_accounts_fid, licenses_site_code)
    ) engine InnoDB;  --  a navigator account will have multiple of these linked to it, one for each navigator
    

CREATE TABLE navigator.org_functional_types           -- formally known as agency types
(    
    oft_code        VARCHAR(40)  NOT NULL ,
    oft_description VARCHAR(40)  NOT NULL ,
    oft_den         BIT,
    oft_dgn         BIT,
    oft_emn         BIT,
    oft_cms_only    BIT,
    oft_pk            INT, CONSTRAINT oft_pk_unique UNIQUE (oft_pk),
    CONSTRAINT oft_description_unique UNIQUE (oft_description),  
    PRIMARY KEY (oft_code)   
) engine InnoDB ;

--  DO WE NEED THIS ??
CREATE TABLE navigator.org_types           -- formally known as oganization types
(                                  -- CITY, COUNTY, CONSOLIDATED , UNIVERSITY, AGENCY, K12, DOE
    ot_code         VARCHAR(40)  NOT NULL ,
    ot_description  VARCHAR(40)  NOT NULL ,
    ot_den            BIT,
    ot_dgn            BIT,
    ot_emn            BIT,
    ot_cms_only        BIT,
    CONSTRAINT ot_description_unique UNIQUE (ot_description),
    PRIMARY KEY (ot_code)   
) engine InnoDB ;

CREATE TABLE navigator.orgs
    (    
        orgs_id                     INT NOT NULL AUTO_INCREMENT,
        orgs_title                     VARCHAR(100) NOT NULL,
        orgs_canonical_title           VARCHAR(100) NOT NULL, --  used for matching with fips table and other temp. use
        orgs_abbreviation             VARCHAR(20),
        orgs_type_code                VARCHAR(40) NOT NULL  COMMENT 'CITY, COUNTY, CONSOLIDATED etc.. found in org_types',          
        orgs_subtype                VARCHAR(100)  COMMENT 'for K12 subtypes',          
        orgs_functional_type_code     VARCHAR(40) COMMENT 'used to be agency type',
     --    orgs_group                  VARCHAR(40),         -- GOV ED (do we need this)
        orgs_state_code                VARCHAR(20) NOT NULL,   -- state abbrev + FEDERAL, MULTIPLE, ...
        orgs_fips_code                 VARCHAR(40),        -- federal id
        orgs_entity_id               VARCHAR(40) COMMENT 'for e-rates', 
        orgs_nces_code               VARCHAR(40), 
        orgs_parent                 INT COMMENT 'foreign key into orgs table. ',
                     
        orgs_fiscal_year_begins        INT COMMENT 'the month,  mostly 1 or 7',     
        orgs_budget_cycle           SET('ANNUAL', 'BIENNIAL','')  DEFAULT '',
        orgs_upcoming_budget_status      VARCHAR(20) COMMENT 'values: APPROVED',  
        orgs_url                     VARCHAR(100) , 
        orgs_address                 VARCHAR(100) , 
        orgs_address2                 VARCHAR(100) , 
        orgs_city                     VARCHAR(100) , 
        orgs_state                     VARCHAR(40) , 
        orgs_zip                     VARCHAR(20) , 
        orgs_phone                     VARCHAR(20) , 
        orgs_fax                     VARCHAR(20) ,
        orgs_tmp_county_fips         VARCHAR(20) , --  temp field can be removed after k12  migration
        orgs_pk                            INT, CONSTRAINT unique_orgs_pk UNIQUE (orgs_pk ),
        CONSTRAINT orgs_unique_fips UNIQUE (orgs_fips_code ), 
        CONSTRAINT orgs_unique_entity_id UNIQUE (orgs_entity_id ),
        CONSTRAINT orgs_unique_entity_id UNIQUE (orgs_nces_code),
        CONSTRAINT orgs_unique_title UNIQUE (orgs_title, orgs_parent, orgs_nces_code),
        CONSTRAINT FOREIGN KEY (orgs_state_code) REFERENCES navigator.states (states_code),
        CONSTRAINT FOREIGN KEY (orgs_type_code) REFERENCES org_types (ot_code),
        CONSTRAINT FOREIGN KEY (orgs_functional_type_code) REFERENCES org_functional_types (oft_code),
        PRIMARY KEY (orgs_id)    
    ) engine InnoDB ; 


CREATE TABLE navigator.orgs_x_orgs    -- to link orgs
    (
        orgs_fid1    INT NOT NULL,  -- 'higher' in hierarchy: FED, STATE, DOE, CONSOLIDATED, COUNTY, CITY, AGENCY, UNIVERSITY, K12 
        orgs_fid2   INT NOT NULL,
        orgs_relation_code   VARCHAR(20),  -- do we need this
        CONSTRAINT FOREIGN KEY (orgs_fid1) REFERENCES orgs (orgs_id),
        CONSTRAINT FOREIGN KEY (orgs_fid2) REFERENCES orgs (orgs_id),
        PRIMARY KEY (orgs_fid1,orgs_fid2) 
     ) engine InnoDB ; 

CREATE TABLE navigator.gov_org_properties    -- extra data for "gov" org types
    (    
       orgs_fid                 INT NOT NULL, 
       population                INT,
       government_employees     INT,
       executive_departments    INT,
       boards_and_commissions   INT,
       judicial_departments     INT,
       legislative_departments  INT,
       funding_tier             INT,  -- for uasi
       CONSTRAINT FOREIGN KEY (orgs_fid) REFERENCES orgs (orgs_id),
       PRIMARY KEY(orgs_fid) 
    ) engine InnoDB ; 

CREATE TABLE navigator.ed_org_properties
    (    
       orgs_fid                      INT NOT NULL,   
       total_k12_spending            INT,
       enrollment                    INT,
       classroom_teachers            INT,
       spending_per_student         INT,
       students_per_computer_ratio     INT,
       school_districts                INT,
       CONSTRAINT FOREIGN KEY (orgs_fid) REFERENCES orgs (orgs_id),
       PRIMARY KEY(orgs_fid) 
    ) engine InnoDB ; 

CREATE TABLE navigator.budgets
    (    
       budgets_id             INT NOT NULL AUTO_INCREMENT,
       budgets_orgs_fid     INT NOT NULL,   -- foreign key into orgs
       budgets_amount        BIGINT,
       budgets_it_amount    BIGINT,
       budgets_type            VARCHAR(40) NOT NULL,   -- 
       budgets_fiscal_year    INT NOT NULL,
       budgets_status        VARCHAR(20) NOT NULL,
       CONSTRAINT FOREIGN KEY (budgets_orgs_fid) REFERENCES orgs (orgs_id),
       PRIMARY KEY(budgets_id) 
    ) engine InnoDB ; 
    
    

CREATE TABLE navigator.bids
    (  
       bids_id                     INT NOT NULL AUTO_INCREMENT,
       bids_title                  VARCHAR(200) NOT NULL,
       bids_project_code          VARCHAR(40) NOT NULL,       
       bids_description          TEXT NOT NULL,
       bids_contract_value      VARCHAR(40) NOT NULL,       
       bids_contract_value_estimated  BIT NOT NULL,       
       
       bids_due_date                 DATETIME,
       bids_pub_date                 DATETIME,
       bids_mod_date                 DATETIME,
       bids_create_date             DATETIME,   --  when it was entered into the system , internal use only
       bids_award_date                 DATETIME,
       
       bids_conference                 BIT,
       bids_conference_mandatory     BIT,
       bids_conference_location     VARCHAR(250),
       bids_conference_date         DATETIME,
       
       bids_preferred_contact_method VARCHAR(10),     
       
       bids_using_org_fid            INT,                    -- ???? do we always have a real using org ?
       bids_using_org_title          VARCHAR(200) NOT NULL,
       bids_using_org_type           VARCHAR(40) ,    --     org_functional_types.oft_code  
       
       bids_issuing_org_fid          INT NOT NULL,
       
       bids_pdf                     VARCHAR(250),
       bids_url                     VARCHAR(250),
       bids_type                    SET('PRE-RFP','RFP','AWARDED','ERATE','SOLE_SOURCE'),
       bids_fiscal_year             INT,
       
       bids_published                  BIT NOT NULL,
       bids_pushed_hourly             BIT NOT NULL,
       bids_pushed_dayly                 BIT NOT NULL,
       bids_pushed_weekly             BIT NOT NULL,
       
       bids_contact_name             VARCHAR(60) , 
       bids_contact_title             VARCHAR(60) , 
       bids_contact_email             VARCHAR(60) , 
       bids_address                 VARCHAR(100) , 
       bids_address2                 VARCHAR(100) , 
       bids_city                     VARCHAR(40) , 
       bids_state                     VARCHAR(10) , 
       bids_zip                      VARCHAR(20) , 
       bids_phone                     VARCHAR(20) , 
       bids_fax                       VARCHAR(20),
       bids_pk                        INT CONSTRAINT unique_bids_pk UNIQUE (bids_pk ),
       CONSTRAINT  FOREIGN KEY (bids_using_org_type) REFERENCES org_functional_types (oft_code),
       CONSTRAINT  FOREIGN KEY (bids_issuing_org_fid) REFERENCES orgs (orgs_id), 
       CONSTRAINT  FOREIGN KEY (bids_using_org_fid) REFERENCES orgs (orgs_id),   
      
       PRIMARY KEY (bids_id)        
    ) engine InnoDB ; 

    
CREATE TABLE navigator.bidcats
(     
        bidcats_id      INT NOT NULL AUTO_INCREMENT,
        bidcats_title    VARCHAR(40) NOT NULL, 
        bidcats_subtype VARCHAR(40) NOT NULL, 
        bidcats_em      BIT,
        bidcats_ed      BIT,
        bidcats_gov     BIT,    
        PRIMARY KEY (bidcats_id)          
) engine InnoDB  COMMENT 'bid categories';    

CREATE TABLE navigator.bids_x_bidcats
(
          bids_fid    INT NOT NULL ,
          bidcats_fid    INT NOT NULL ,
        CONSTRAINT  FOREIGN KEY (bids_fid)    REFERENCES bids (bids_id),  
        CONSTRAINT  FOREIGN KEY (bidcats_fid) REFERENCES bidcats (bidcats_id),           
        PRIMARY KEY (bidcats_fid, bids_fid)            
) engine InnoDB ;  

    


CREATE TABLE navigator.contacts
    (    
        contacts_id INT NOT NULL AUTO_INCREMENT,
        contacts_first_name VARCHAR(20),
        contacts_last_name VARCHAR(20),
        contacts_biographie TEXT,
         PRIMARY KEY (contacts_id)
    ) engine InnoDB ; 

    
CREATE TABLE navigator.divisions
    (    
        divisions_code  VARCHAR(40) NOT NULL,
        divisions_title VARCHAR(40),   
        PRIMARY KEY (divisions_code)
    ) engine InnoDB ; 

    
    
-- CREATE TABLE navigator.roletypes
--     (    
--         roletypes_code  VARCHAR(40) NOT NULL, -- HEAD, DEPUTY, SPECIALIST, MEMBER
--         roletypes_title VARCHAR(40),   
--         PRIMARY KEY (roletypes_code)
--     ) engine InnoDB ; 
 
    


-- drop table navigator.contact_roles ;
--  question : how to deal with retired people?  unlink them from the role ?   add an 'inactive' field to role
CREATE TABLE navigator.contact_roles     -- a link table between org and contact
    (    
       roles_contacts_fid   INT NOT NULL,
       roles_orgs_fid        INT NOT NULL,
       roles_position       SET('HEAD','DEPUTY','SPECIALIST') NOT NULL,   
       roles_divisions_code VARCHAR(40) NOT NULL,  -- IT, ENTIRE agency
       roles_title            VARCHAR(100) NOT NULL,
       roles_active         BIT,
       roles_use_org_address BIT,
       roles_email            VARCHAR(100),
       roles_address        VARCHAR(100),
       roles_address2        VARCHAR(100),
       roles_city            VARCHAR(40),
       roles_state            VARCHAR(20),
       roles_zip            VARCHAR(20),
       roles_phone            VARCHAR(20),
       roles_fax             VARCHAR(20),
       CONSTRAINT FOREIGN KEY (roles_contacts_fid) REFERENCES contacts (contacts_id),
       CONSTRAINT FOREIGN KEY (roles_orgs_fid) REFERENCES orgs (orgs_id),
       CONSTRAINT FOREIGN KEY (roles_divisions_code) REFERENCES divisions (divisions_code),
       PRIMARY KEY (roles_contacts_fid, roles_orgs_fid)
    ) engine InnoDB ; 
    
    
CREATE TABLE navigator.deals   
    (
        deals_id INT NOT NULL AUTO_INCREMENT,
        deals_details TEXT,        -- has wiki code 
        deals_body TEXT,
        deals_users_fid       INT NOT NULL COMMENT 'the author',
        CONSTRAINT FOREIGN KEY (deals_users_fid) REFERENCES users (users_id),
        PRIMARY KEY (deals_id)
    ) engine InnoDB;  

CREATE TABLE navigator.updates
    (
        updates_id            INT NOT NULL AUTO_INCREMENT,
        updates_deals_fid                 INT NOT NULL,
        updates_users_fid      INT NOT NULL COMMENT 'the author',
        updates_create_date DATETIME NOT NULL,
        CONSTRAINT FOREIGN KEY (updates_deals_fid) REFERENCES deals (deals_id),
        CONSTRAINT FOREIGN KEY (updates_users_fid) REFERENCES users (users_id),
        PRIMARY KEY (updates_id)
    )  engine InnoDB;      
    

CREATE TABLE navigator.deals_x_bids   
    (
        deals_fid     INT NOT NULL,
        bids_fid       INT NOT NULL,
        CONSTRAINT FOREIGN KEY (deals_fid) REFERENCES deals (deals_id),
        CONSTRAINT FOREIGN KEY (bids_fid)  REFERENCES bids (bids_id),
        PRIMARY KEY (deals_fid, bids_fid)
    ) engine InnoDB;  
    
CREATE TABLE navigator.hotlists
    (
        hotlists_users_fid INT NOT NULL,
        hotlists_item_fid INT NOT NULL,
        hotlists_item_table VARCHAR(60) NOT NULL,
        CONSTRAINT FOREIGN KEY (hotlists_users_fid) REFERENCES users (users_id),
        -- CONSTRAINT FOREIGN KEY (hotlists_item_table) REFERENCES information_schema.TABLES (TABLE_NAME),
        PRIMARY KEY (hotlists_users_fid, hotlists_item_fid, hotlists_item_table)
    )  engine InnoDB; 

CREATE TABLE  navigator.media
    (
        media_clk_id NUMERIC,
        media_id INT NOT NULL AUTO_INCREMENT,
        media_url VARCHAR(500)   NOT NULL,
        media_title VARCHAR(150)  ,                  --  for internal use
        media_display_title VARCHAR(150)  ,
        media_summary VARCHAR(500)  ,
        media_create_date DATETIME,
        media_credit VARCHAR(255)  ,
        media_size_kb INT,
        media_link VARCHAR(255)  ,                 -- in case this item has to be linked   
        media_new_window BIT DEFAULT 0 NOT NULL,
        media_type VARCHAR(20)   NOT NULL,      -- IMAGE, PDF, VIDEO
        media_alt_text VARCHAR(255)  ,                  --  alt text
        CONSTRAINT  media_clk_id_unique UNIQUE (media_clk_id),
        PRIMARY KEY (media_id)
    )engine InnoDB;

    
CREATE TABLE navigator.tags   
    (
        tags_code         VARCHAR(30) NOT NULL ,
        tags_group_code   VARCHAR(30) NOT NULL ,    --  used to be subtype
        tags_site         VARCHAR(10) NOT NULL COMMENT 'sitecode or ALL',            
        tags_description  VARCHAR(80) NOT NULL ,  
        tags_help_text     VARCHAR(255)  ,           -- explains where this tag is used     
        PRIMARY KEY (tags_code)
    ) engine InnoDB;      

CREATE TABLE navigator.taglinks   
    (
        taglinks_tags_code    VARCHAR(30) NOT NULL ,
        taglinks_table        VARCHAR(30) NOT NULL ,
        taglinks_fid        INT NOT NULL,
        CONSTRAINT FOREIGN KEY (taglinks_tags_code) REFERENCES tags (tags_code),
        PRIMARY KEY (taglinks_tags_code, taglinks_table, taglinks_fid)
    ) engine InnoDB;
    
 CREATE TABLE  navigator.histories
    (
        histories_users_fid INT NOT NULL,
        histories_table       VARCHAR(40) NOT NULL,
        histories_action      VARCHAR(20) NOT NULL,
        histories_timestamp     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        histories_record_fid  INT NOT NULL,
        CONSTRAINT FOREIGN KEY (histories_users_fid) REFERENCES users (users_id)     
    )engine InnoDB;

CREATE TABLE  navigator.bonus_hour_request
    (
        bhr_id                 INT NOT NULL AUTO_INCREMENT,
        dhr_accounts_fid    INT NOT NULL,
        dhr_requestor_fid    INT NOT NULL COMMENT 'the requesting user',
        dhr_site_code        SET('DGN','DEN','EMN') NOT NULL,  -- subtype in the old system
        bhr_create_date       DATETIME NOT NULL,
        bhr_close_date      DATETIME,
        bhr_hours_used        INT,
        bhr_description        VARCHAR(40) NOT NULL,
        bhr_sf_case_id        VARCHAR(20),
        bhr_sf_account_id    VARCHAR(20),
        bhr_sf_contact_id    VARCHAR(20),
        CONSTRAINT FOREIGN KEY (dhr_requestor_fid) REFERENCES users (users_id),   
        CONSTRAINT FOREIGN KEY (dhr_accounts_fid) REFERENCES accounts (accounts_id),        
        PRIMARY KEY (bhr_id)
    )engine InnoDB;
     
CREATE TABLE  navigator.collaborations
    (
        collaborations_id             INT NOT NULL AUTO_INCREMENT,        
        collaborations_users_fid    INT NOT NULL ,
        collaborations_name            VARCHAR(40) NOT NULL, 
        collaborations_email           VARCHAR(40) NOT NULL, 
        collaborations_provides      VARCHAR(100),
        collaborations_looking_for    VARCHAR(100),
        collaborations_create_date    DATETIME,
        collaborations_notes        TEXT,
        CONSTRAINT FOREIGN KEY (collaborations_users_fid) REFERENCES users (users_id),                
        PRIMARY KEY (collaborations_id)
    )engine InnoDB;
     
CREATE TABLE  navigator.interviews
    (
        interviews_id            INT NOT NULL AUTO_INCREMENT,    
        interviews_title        VARCHAR(200) NOT NULL, 
        interviews_contacts_fid    INT , 
        interviews_summary        VARCHAR(200) NOT NULL, 
        interviews_mod_date        DATETIME,
        interviews_type        VARCHAR(40) NOT NULL,
        interviews_published     BIT,
        interviews_ext_id        VARCHAR(40) ,
        interviews_event_date     DATETIME,
        interviews_eloqua_url    VARCHAR(200),
        interviews_site_code    SET('DGN','DEN','EMN'),
        CONSTRAINT FOREIGN KEY (interviews_contacts_fid) REFERENCES users (users_id),  
        PRIMARY KEY (interviews_id)
    )engine InnoDB;    

    
CREATE TABLE  navigator.user_preferences
    (
        up_users_fid            INT NOT NULL,
        up_site_code            SET('DGN','DEN','EMN') NOT NULL, -- VARCHAR(20) NOT NULL, 
        up_push_hourly            BIT DEFAULT 0,
        up_push_daily            BIT DEFAULT 0,
        up_push_weekly            BIT DEFAULT 0,
        up_push_rfps            BIT DEFAULT 0,
        up_push_erates            BIT DEFAULT 0,
        up_push_news            BIT DEFAULT 0,
        up_all_states            BIT DEFAULT 0,
        up_all_cats                BIT DEFAULT 0,
        up_all_levels            BIT DEFAULT 0,
        up_keywords                VARCHAR(40),
        up_hotlisted_clk_items  VARCHAR(1000),
        CONSTRAINT FOREIGN KEY (up_users_fid) REFERENCES users (users_id),  
        PRIMARY KEY (up_users_fid, up_site_code)
    )engine InnoDB;    
            
CREATE TABLE  navigator.searches
    (
        searches_id                INT NOT NULL AUTO_INCREMENT,    
        searches_contacts_fid    INT , 
        searches_uri            VARCHAR(2000) NOT NULL,
        searches_title            VARCHAR(40) NOT NULL,
        CONSTRAINT FOREIGN KEY (searches_contacts_fid) REFERENCES users (users_id),
        PRIMARY KEY (searches_id)
    ) engine InnoDB;   
     

     
   

insert into accounts(accounts_id,accounts_pk,accounts_title,accounts_sf_id) values(1,417102,'eRepublic','0013000000LtZ3U');


insert into usergroups(usergroups_code, usergroups_title, usergroups_summary) values('SUPER_ADMIN','Super Admin!','like a god');
insert into usergroups(usergroups_code, usergroups_title,usergroups_summary) values('ADMIN','Admin','Administrator');

-- creating the  administrator user 
insert into users (users_id, users_last_name, users_first_name, users_password, users_email, users_active, users_accounts_fid) 
        values(1,'administrator','','201f00b5ca5d65a1c118e5e32431514c','webmaster@erepublic.com',1,1);
insert into users_x_usergroups(users_fid,usergroups_fcode) values(1,'ADMIN');

insert into users (users_id, users_last_name, users_first_name, users_password, users_email, users_active, users_accounts_fid) 
        values(3,'Tel','Michael','201f00b5ca5d65a1c118e5e32431514c','mtel@erepublic.com',1,1);
insert into users_x_usergroups(users_fid,usergroups_fcode) values(3,'SUPER_ADMIN');

INSERT INTO licenses (licenses_accounts_fid, licenses_site_code, licenses_access_level,licenses_number_of_users,licenses_status,licenses_expiration,licenses_monthly_bonus_hours,licenses_account_rep_fid) 
                      VALUES(1,'DGN','STANDARD',3,'ACTIVE','2013-03-31',1,1 );        
INSERT INTO licenses (licenses_accounts_fid, licenses_site_code, licenses_access_level,licenses_number_of_users,licenses_status,licenses_expiration,licenses_monthly_bonus_hours,licenses_account_rep_fid) 
                      VALUES(1,'EMN','STANDARD',3,'ACTIVE','2013-03-31',1,1 ) ;       
INSERT INTO licenses (licenses_accounts_fid, licenses_site_code, licenses_access_level,licenses_number_of_users,licenses_status,licenses_expiration,licenses_monthly_bonus_hours,licenses_account_rep_fid) 
                      VALUES(1,'DEN','STANDARD',3,'ACTIVE','2013-03-31',1,1 ) ;       



INSERT INTO states (states_code,states_title,states_pk,states_fips_code, states_territories) VALUES('AS','American Samoa', 10001,60,1); 
INSERT INTO states (states_code,states_title,states_pk,states_fips_code, states_territories) VALUES('GU','Guam', 10002,66,1); 
INSERT INTO states (states_code,states_title,states_pk,states_fips_code, states_territories) VALUES('PR','Puerto Rico', 10003,72,1); 
INSERT INTO states (states_code,states_title,states_pk,states_fips_code, states_territories) VALUES('VI','Virgin Islands', 10004,78,1); 
INSERT INTO states (states_code,states_title,states_pk,states_fips_code, states_territories) VALUES('MP','Northern Marianas', 10005,69,1); 
--  other fips codes:  Bureau of Indian Education: 59 , DOD (Domestic): 61  , DOD (Overseas): 58

 
   

-- articles

-- cron_jobs
-- push_mail_log
-- salesforce_logs




        
