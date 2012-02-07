-- run this script on the db server with:  mysql --user=root --password=root < mysql-innodb.sql 

--  http://gitref.org/   http://progit.org/
--  git checkout -- mymessedupfile.php --force , will copy a fresh copy into the working directory
--  http://blog.jbrumond.me/archives/169

-- table name should be one word without underscores
-- pk should be tablename_id
-- foreign keys should be in the format:     []foreigntablename_id

-- drop table media__contents;
-- drop table targets;
-- drop table modules__pages;

-- drop table tags__contents;
-- drop table tags;

-- drop table comments;
-- drop table textfields;
-- drop table media;
-- drop table articles;
-- drop table pages;
-- drop table specs;
-- drop table contents;
-- drop table authors;
-- drop table roles;
-- drop table users;
-- drop table modules;

create user 'web_sites'@'%' identified by 'w3b_s1t3s';
create user 'web_sites_admin'@'%' identified by 'w3b_s1t3s_4dm1n';

create schema sessiondb;
grant select, insert, update, delete on sessiondb.* to 'web_sites'@'%';
grant select, insert, update, delete on sessiondb.* to 'web_sites_admin'@'%';

CREATE TABLE sessiondb.sessions
    (
        sessions_id VARCHAR(40) NOT NULL ,
        sessions_data VARCHAR(500)  ,
        sessions_expires DATETIME ,
        sessions_users_id int NOT NULL,
        sessions_site_code  VARCHAR(50),
        PRIMARY KEY (sessions_id)
    ) engine MyISAM ; 


create schema newgt;
grant select on newgt.* to 'web_sites'@'%';
grant select, insert, update, delete on newgt.* to 'web_sites_admin'@'%';

CREATE TABLE users
    (
        users_id INT NOT NULL AUTO_INCREMENT,
        users_last_name VARCHAR(20) ,
        users_first_name VARCHAR(20),
        users_password VARCHAR(40) ,
        users_email VARCHAR(70)   NOT NULL,
        users_ad_user VARCHAR(20) ,
        users_accounts_id INT NOT NULL,
        users_notes VARCHAR(255) ,
        users_active BIT DEFAULT 0 NOT NULL,
        users_internal BIT DEFAULT 0 NOT NULL,  -- if this is 0 it requires a subclassed user, like nav_user 
        users_type  VARCHAR(20)  NOT NULL,   --  EREPUBLIC, NAVIGATOR, BLOGGER
        PRIMARY KEY (users_id),
        CONSTRAINT users_email_unique UNIQUE (users_email)
    ) engine InnoDB ; 

CREATE TABLE accounts
    (
        accounts_id INT NOT NULL AUTO_INCREMENT,
        accounts_title VARCHAR(40) NOT NULL,
        accounts_sf_id VARCHAR(40) ,
        accounts_is_navigator BIT DEFAULT 0 NOT NULL,
        PRIMARY KEY (accounts_id)
    ) engine InnoDB ; 
    
    

CREATE TABLE logins
    (
        logins_users_id INT NOT NULL ,
        logins_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        logins_site_code VARCHAR(20),
        logins_accounts_id INT NOT NULL ,
        logins_method VARCHAR(20),
        logins_browser VARCHAR(100),
        logins_http_address VARCHAR(50),
        logins_http_port NUMERIC,
        PRIMARY KEY (users_fid, login_date),
        CONSTRAINT  FOREIGN KEY (logins_users_id) REFERENCES users (users_id),
        CONSTRAINT  FOREIGN KEY (logins_accounts_id) REFERENCES accounts (accounts_id)
    ) engine InnoDB;   
    

CREATE TABLE nav_accounts
    (
        nava_accounts_id INT NOT NULL ,
        nava_site_code VARCHAR(20) NOT NULL,
        nava_access_level
        nava_licenses
        nava_contract_status
        nava_contract_expiration
        nava_monthly_bonus_hours
        nava_account_rep_pk
        
        PRIMARY KEY (nava_accounts_id, nava_site_code)
    ) engine InnoDB ; 
    

    
CREATE TABLE authors
    (
        authors_clk_id NUMERIC,
        authors_id INT NOT NULL AUTO_INCREMENT,
        authors_users_id INT NOT NULL,            -- points to the user record
        authors_name VARCHAR(50)  NOT NULL,
        authors_display_name VARCHAR(50)  NOT NULL,
        authors_role_title VARCHAR(60),
        authors_summary VARCHAR(255),
        authors_bio TEXT,
        authors_public_email VARCHAR(60) ,
        authors_twitter_url VARCHAR(100) ,
        authors_googleplus_url VARCHAR(100) ,
        authors_active BIT DEFAULT 0 NOT NULL,
        CONSTRAINT  FOREIGN KEY (authors_users_id) REFERENCES users (users_id),
        CONSTRAINT authors_clk_id_unique UNIQUE (authors_clk_id),
        PRIMARY KEY (authors_id)
    ) engine InnoDB ; 
    

    



CREATE TABLE roles   
    (
        roles_users_id INT NOT NULL ,
        roles_code VARCHAR(20) NOT NULL ,
        CONSTRAINT  FOREIGN KEY (roles_users_id) REFERENCES users (users_id) ON DELETE CASCADE,      
        PRIMARY KEY (roles_users_id, roles_code)
    ) engine InnoDB;  
    

    
        

 
        

        
    
    
  
    
-- drop table contents;   
CREATE TABLE
    contents
    (
        contents_clk_id NUMERIC ,
        contents_id INT NOT NULL AUTO_INCREMENT, 
        contents_live_rev INT NOT NULL,
        contents_preview_rev INT ,    
        contents_latest_rev INT NOT NULL,    
        contents_url_name VARCHAR(500),
        contents_title VARCHAR(150) NOT NULL,
        contents_display_title VARCHAR(150),
        contents_summary VARCHAR(500),
        contents_pub_date DATETIME ,             --  can be changed by editorial staff
        contents_create_date DATETIME  NOT NULL,
        contents_mod_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        contents_mod_users_id INT NOT NULL,
        contents_type VARCHAR(20) NOT NULL,
        contents_extra_table VARCHAR(30) NOT NULL,
        contents_status VARCHAR(20) NOT NULL,
        contents_authors_id INT,
         
        PRIMARY KEY (contents_id),
        CONSTRAINT  contents_clk_id_unique UNIQUE (contents_clk_id),
        FOREIGN KEY (contents_authors_id) REFERENCES authors(authors_id),      
        FOREIGN KEY (contents_mod_users_id) REFERENCES users(users_id)      
    )engine InnoDB;

    
   
-- drop table articles;
CREATE TABLE articles   
    (
        --  make sure that these field names are differnt form the fields in the table: contents
        -- first 2 field are required for all content extension tables  
        contents_fid INT NOT NULL ,
        contents_rev INT  NOT NULL,       
        contents_rev_users_id INT,                -- author of latest change
        contents_rev_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        contents_rev_comment   VARCHAR(255) ,   --  commit comment
        contents_rev_status   VARCHAR(20) ,   
                  
        contents_article_type  VARCHAR(20),
        contents_article_body   TEXT , 
       
        PRIMARY KEY (contents_fid, contents_rev),
        FOREIGN KEY (contents_fid) REFERENCES contents(contents_id) ON DELETE CASCADE,  
        FOREIGN KEY (contents_rev_users_id) REFERENCES users(users_id)
    )engine InnoDB;

CREATE TABLE modules   
    (
        --  make sure that these field names are differnt form the fields in the table: contents
        -- first 2 field are required for all content extension tables  
        contents_fid INT NOT NULL ,
        contents_rev INT  NOT NULL,       
        contents_rev_users_id INT,                -- author of latest change
        contents_rev_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        contents_rev_comment   VARCHAR(255) ,   --  commit comment
        contents_rev_status   VARCHAR(20) ,   
                                    
        modules_php_class VARCHAR(40),
        modules_json_params  VARCHAR(255),
        modules_body TEXT,
        modules_site_code VARCHAR(20),                    -- COMMON, GT GOV etc
        
        PRIMARY KEY (contents_fid, contents_rev),
        FOREIGN KEY (contents_fid) REFERENCES contents(contents_id) ON DELETE CASCADE,  
        FOREIGN KEY (contents_rev_users_id) REFERENCES users(users_id)
    )engine InnoDB;

    
 
    
   
    
    
-- drop table docu_items   ;
CREATE TABLE specs   
    (
        -- first 3 field are required for all content extension tables  
        contents_fid INT NOT NULL ,
        contents_rev INT  NOT NULL,
        contents_users_id INT,                -- author of latest change
        contents_rev_status   VARCHAR(20) ,   
        
        contents_indexing VARCHAR(30),
        contents_specs_type  VARCHAR(20),
        contents_specs_user_docu   TEXT , 
        contents_specs_design_docu TEXT ,
        
        PRIMARY KEY (contents_id, contents_rev),
        FOREIGN KEY (contents_id) REFERENCES contents(contents_id) ON DELETE CASCADE,  
        FOREIGN KEY (contents_users_id) REFERENCES users(users_id)
    )engine InnoDB;
       
           
 CREATE TABLE comments
    (
        comments_id INT NOT NULL AUTO_INCREMENT,
        comments_parent_id INT NOT NULL,           --  for nested comments this is the parent comment, otherwise this is the same as comments_id
        comments_title  VARCHAR(100)   NOT NULL,
        comments_body   TEXT  ,
        comments_commenter VARCHAR(50)   NOT NULL,
        comments_email VARCHAR(50),
        comments_ranking INT default 0,
        comments_date  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        comments_flagged VARCHAR(50),
        comments_contents_id  INT NOT NULL,
        PRIMARY KEY (comments_id),
        FOREIGN KEY (comments_contents_id) REFERENCES contents(contents_id) ON DELETE CASCADE
    )ENGINE InnoDB;
           
 -- drop table media;        
CREATE TABLE  media
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

CREATE TABLE    media__contents
    (
        media_id INT NOT NULL,
        contents_id INT NOT NULL,
        link_type VARCHAR(30),
        link_order INT,
        PRIMARY KEY (media_id, contents_id),
        FOREIGN KEY (media_id)    REFERENCES media (media_id)      ON DELETE CASCADE,
        FOREIGN KEY (contents_id) REFERENCES contents (contents_id)ON DELETE CASCADE       
    ) ENGINE InnoDB ;
    
      

       
       
       
-- holds archived text fields from  ? and potentially other types
CREATE TABLE textfields   
    (
        -- first 3 field are required for all content extension tables  
        textfields_table VARCHAR(30) NOT NULL, --  tablename
        textfields_table_id INT NOT NULL,      --  the pk in that table
        textfields_field VARCHAR(30) NOT NULL, --  fieldname
        textfields_rev INT NOT NULL,       --  the rev 
        
        textfields_body TEXT NOT NULL,         --  the actual text
        textfields_author_id INT,             --  the author of the text
        textfields_date DATETIME,
        PRIMARY KEY (textfields_table, textfields_table_id, textfields_field, textfields_rev), 
        FOREIGN KEY (textfields_author_id) REFERENCES users(users_id)
    )engine InnoDB;
       
       
      

-- to see a rev that's not the live rev add ?rev=23&pw=govtech123 ( to see a rev that's not live you need to be logged in or have the pw param 
-- drop table pages;
CREATE TABLE pages   
    (
        pages_rev INT NOT NULL AUTO_INCREMENT,
        pages_id INT NOT NULL,               --  this id does not change with a new rev,  
               
        pages_rev_users_id INT NOT NULL,   --  the last person to edit this page 
        pages_rev_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        pages_rev_comment VARCHAR(255),

        pages_is_preview BIT,
        pages_is_live BIT,     
        pages_site_code VARCHAR(20),         -- GOV, GT, EM, CV, DC, PCIO
        pages_title VARCHAR(150) ,             --  for internal use
        pages_display_title VARCHAR(150) ,    
        pages_url     VARCHAR(50)   NOT NULL,    -- like: /workforce
        pages_type VARCHAR(50)   NOT NULL,     --  TOPIC
        pages_no_robots  BIT,         --  this should set the no_robots line in the <head>
        pages_password VARCHAR(20)  , --  for customer preview only
        pages_php_class VARCHAR(20) NOT NULL,    --  the class that renders this page        
        pages_body TEXT, 
        
        PRIMARY KEY (pages_rev),
        FOREIGN KEY (pages_rev_users_id) REFERENCES users(users_id)
    )  engine InnoDB ;

    
create index  pages_id on pages(pages_id);      



-- drop table targets; 
CREATE TABLE targets   
    (
        targets_pages_id INT NOT NULL ,
        targets_contents_id INT NOT NULL ,
        targets_pin_position  INT DEFAULT 0 NOT NULL,           --  normally  0, but if not null is is the pinned position (1 is first )  
        targets_is_home BIT,                                    --  this is the home target
        targets_placement VARCHAR(20),                          --  used by modules   to identify the column   
        targets_live_date DATETIME,                                      --  articles will only show between live and dead dates
        targets_archive_date DATETIME DEFAULT '1000-01-01',  
        targets_dead_date DATETIME DEFAULT '1000-01-01',
        PRIMARY KEY (targets_pages_id, targets_contents_id),
        FOREIGN KEY (targets_pages_id) REFERENCES pages (pages_id)  ON DELETE CASCADE,   
        FOREIGN KEY (targets_contents_id) REFERENCES contents (contents_id)   ON DELETE CASCADE        
    )engine InnoDB;   

    
    
    

CREATE TABLE tags   
    (
        tags_code VARCHAR(30) NOT NULL ,
        tags_group_code VARCHAR(30) NOT NULL ,
        tags_site VARCHAR(10) NOT NULL,           --  sitecode or 'ALL'  
        tags_description VARCHAR(80) NOT NULL ,  
        tags_help_text VARCHAR(255)  ,           -- explains where this tag is used
       
        PRIMARY KEY (tags_code)
    ) engine InnoDB;  

            
CREATE TABLE tags__contents    --  a link table   
    (
        tags_code VARCHAR(30) NOT NULL ,
        tags_contents_id INT NOT NULL,
        PRIMARY KEY (tags_code, tags_contents_id),
        FOREIGN KEY (tags_code) REFERENCES tags (tags_code)  ON DELETE CASCADE,  
        FOREIGN KEY (tags_contents_id) REFERENCES contents (contents_id)  ON DELETE CASCADE 
    ) engine InnoDB;  
        
        

CREATE TABLE contents__contents 
    (
        contents_id1 INT NOT NULL ,
        contents_id2 INT NOT NULL ,
        contents_type1 VARCHAR(20),   --  always in alphabetical order
        contents_type2 VARCHAR(20),
        link_order INT,
        link_type VARCHAR(20),
        PRIMARY KEY (contents_id1, contents_id2),
        FOREIGN KEY (contents_id1) REFERENCES contents (contents_id)   ON DELETE CASCADE ,
        FOREIGN KEY (contents_id2) REFERENCES contents (contents_id)   ON DELETE CASCADE 

    )  engine InnoDB ; 
         
       

CREATE TABLE modules__pages 
    (
        mp_contents_id INT NOT NULL ,
        mp_pages_rev INT NOT NULL ,
        mp_placement VARCHAR(30),
        mp_link_order INT,
        PRIMARY KEY (mp_pages_rev, mp_placement, mp_link_order),
        FOREIGN KEY (mp_contents_id) REFERENCES contents (contents_id)   ON DELETE CASCADE ,
        FOREIGN KEY (mp_pages_rev) REFERENCES pages (pages_rev)   ON DELETE CASCADE 
    )  engine InnoDB ; 

-- list of latest revisions per page        
CREATE VIEW max_page_revisions as
(
    SELECT pages_id AS mpr_pages_id, MAX(pages_rev) AS mpr_pages_rev 
    FROM pages GROUP BY pages_id
);

--  a list of the latest pages
create view current_pages as
(
     SELECT * FROM pages JOIN max_page_revisions ON mpr_pages_id = pages_id  AND  pages_rev = mpr_pages_rev 
);     

    
--  create an account
insert into accounts(accounts_title) values('eRepublic');

-- creating the  administrator user and system author
insert into users (users_last_name, users_first_name, users_password, users_email, users_active, users_accounts_id) values('administrator','','201f00b5ca5d65a1c118e5e32431514c','webmaster@erepublic.com',1,LAST_INSERT_ID());
insert into roles(roles_users_id,roles_code) values(LAST_INSERT_ID() ,'ADMIN');

insert into authors(authors_name, authors_display_name, authors_public_email, authors_active, authors_users_id) 
VALUES('system','system', 'system@erepublic.com',1, (select users_id from users where users_last_name = 'administrator'));

insert into users (users_last_name, users_first_name, users_password, users_email, users_active, users_accounts_id) values('Tel','Michael','201f00b5ca5d65a1c118e5e32431514c','mtel@erepublic.com',1,1);
insert into roles(roles_users_id,roles_code) values(LAST_INSERT_ID() ,'SUPER_ADMIN');

-- creating a few pages
insert into pages (pages_id,pages_is_live,pages_is_preview,pages_rev,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,
                   pages_body,pages_php_class,pages_rev_users_id,pages_rev_comment) 
    values(1,1,1,1,'GT','homepage','Government Technology','/','HOMEPAGE',' homepage ' ,'HomePage',1, 'this is the first rev');
    
insert into pages (pages_id,pages_is_live,pages_is_preview,pages_rev,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,
                   pages_body,pages_php_class,pages_rev_users_id,pages_rev_comment) 
    values(2,1,1,2,'GT','about','About Government Technology','/about','STATIC',' this is the about page ' ,'StaticPage',1, 'this is the first rev');
    
insert into pages (pages_id,pages_is_live,pages_is_preview,pages_rev,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,
                   pages_body,pages_php_class,pages_rev_users_id,pages_rev_comment) 
    values(3,1,1,3,'GT','e-government','E-Government','/e-government','CHANNEL','  ' ,'ChannelPage',1, 'this is the first rev');
insert into pages (pages_id,pages_is_live,pages_is_preview,pages_rev,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,
                   pages_body,pages_php_class,pages_rev_users_id,pages_rev_comment)  
    values(4,1,1,4,'GT','technology','Emerging and Sustainable Technology','/technology','CHANNEL','  ' ,'ChannelPage',1, 'this is the first rev');
-- ----------------------------------------------------------------------------------------





