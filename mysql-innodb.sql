--  

-- table name should be one word without unserscores
-- pk should be tablename_pk
-- foreign keys should be in the format:     []foreigntablename_fk
drop table media__contents;
drop table targets;
drop table modules__pages;

drop table comments;
drop table textfields;
drop table media;
drop table articles;
drop table pages;
drop table specs;
drop table contents;
drop table roles;
drop table users;
drop table modules;

CREATE TABLE users
    (
        users_pk INT NOT NULL AUTO_INCREMENT,
        users_last_name VARCHAR(20) ,
        users_first_name VARCHAR(20),
        users_password VARCHAR(40) ,
        users_email VARCHAR(70)   NOT NULL,
        users_ad_user VARCHAR(20) ,
        users_active BIT DEFAULT 0 NOT NULL,
        PRIMARY KEY (users_pk),
        CONSTRAINT users_email_unique UNIQUE (users_email)
    ) engine InnoDB ; 

CREATE TABLE roles   
    (
        roles_users_fk INT NOT NULL ,
        roles_code VARCHAR(20) NOT NULL ,
        CONSTRAINT  FOREIGN KEY (roles_users_fk) REFERENCES users (users_pk) ON DELETE CASCADE,      
        PRIMARY KEY (roles_users_fk, roles_code)
    ) engine InnoDB;  
    
        

        
    
    
-- drop table contents;   
CREATE TABLE
    contents
    (
        contents_pk INT NOT NULL AUTO_INCREMENT, 
        contents_live_version INT NOT NULL,
        contents_preview_version INT ,    
        contents_latest_version INT NOT NULL,    
        contents_url_name VARCHAR(500),
        contents_title VARCHAR(150) NOT NULL,
        contents_display_title VARCHAR(150),
        contents_summary VARCHAR(500),
        contents_create_date DATETIME NOT NULL,
        contents_update_date DATETIME NOT NULL,
        contents_update_users_fk INT NOT NULL,
        contents_type VARCHAR(20) NOT NULL,
        contents_extra_table VARCHAR(30) NOT NULL,
        contents_status VARCHAR(20) NOT NULL,
        contents_main_author_fk INT NOT NULL,
        
  
        PRIMARY KEY (contents_pk),
        FOREIGN KEY (contents_main_author_fk) REFERENCES users(users_pk),      
        FOREIGN KEY (contents_update_users_fk) REFERENCES users(users_pk)      
    )engine InnoDB;

    
-- drop table articles;
CREATE TABLE articles   
    (
        --  make sure that these field names are differnt form the fields in the table: contents
        -- first 2 field are required for all content extension tables  
        contents_fk INT NOT NULL ,
        contents_version INT  NOT NULL,       
        contents_version_users_fk INT,                -- author of latest change
        contents_version_date DATETIME NOT NULL,
        contents_version_comment   VARCHAR(255) ,   --  commit comment
                  
        contents_article_type  VARCHAR(20),
        contents_article_body   TEXT , 
       
        PRIMARY KEY (contents_fk, contents_version),
        FOREIGN KEY (contents_fk) REFERENCES contents(contents_pk) ON DELETE CASCADE,  
        FOREIGN KEY (contents_version_users_fk) REFERENCES users(users_pk)
    )engine InnoDB;

CREATE TABLE modules   
    (
        --  make sure that these field names are differnt form the fields in the table: contents
        -- first 2 field are required for all content extension tables  
        contents_fk INT NOT NULL ,
        contents_version INT  NOT NULL,       
        contents_version_users_fk INT,                -- author of latest change
        contents_version_date DATETIME NOT NULL,
        contents_version_comment   VARCHAR(255) ,   --  commit comment
                                    
        modules_php_class VARCHAR(40),
        modules_json_params  VARCHAR(255),
        modules_body TEXT,
        modules_site_code VARCHAR(20),                    -- COMMON, GT GOV etc
        
        PRIMARY KEY (contents_fk, contents_version),
        FOREIGN KEY (contents_fk) REFERENCES contents(contents_pk) ON DELETE CASCADE,  
        FOREIGN KEY (contents_version_users_fk) REFERENCES users(users_pk)
    )engine InnoDB;

    
   
    
    
-- drop table docu_items   ;
CREATE TABLE specs   
    (
        -- first 3 field are required for all content extension tables  
        contents_fk INT NOT NULL ,
        contents_version INT  NOT NULL,
        contents_users_fk INT,                -- author of latest change
        
        contents_indexing VARCHAR(30),
        contents_specs_type  VARCHAR(20),
        contents_specs_user_docu   TEXT , 
        contents_specs_design_docu TEXT ,
        
        PRIMARY KEY (contents_fk, contents_version),
        FOREIGN KEY (contents_fk) REFERENCES contents(contents_pk) ON DELETE CASCADE,  
        FOREIGN KEY (contents_users_fk) REFERENCES users(users_pk)
    )engine InnoDB;
       
           
 CREATE TABLE comments
    (
        comments_pk INT NOT NULL AUTO_INCREMENT,
        comments_fk INT NOT NULL,
        comments_title  VARCHAR(100)   NOT NULL,
        comments_body   TEXT  ,
        comments_commenter VARCHAR(50)   NOT NULL,
        comments_email VARCHAR(50),
        comments_ranking INT default 0,
        comments_date   datetime NOT NULL,
        comments_flagged VARCHAR(50),
        comments_contents_fk  INT NOT NULL,
        PRIMARY KEY (comments_pk),
        FOREIGN KEY (comments_contents_fk) REFERENCES contents(contents_pk) ON DELETE CASCADE
    )ENGINE InnoDB;
           
 -- drop table media;        
CREATE TABLE  media
    (
        media_pk INT NOT NULL AUTO_INCREMENT,
        media_url VARCHAR(500)   NOT NULL,
        media_title VARCHAR(150)  ,                  --  for internal use
        media_display_title VARCHAR(150)  ,
        media_summary VARCHAR(500)  ,
        media_create_date DATETIME,
        media_credit VARCHAR(255)  ,
        media_link VARCHAR(255)  ,                 -- in case this item has to be linked   
        media_new_window BIT DEFAULT 0 NOT NULL,
        media_type VARCHAR(20)   NOT NULL,      -- IMAGE, PDF, VIDEO
        medi_alt_text VARCHAR(255)  ,                  --  alt text
        PRIMARY KEY (media_pk)
    )engine InnoDB;

CREATE TABLE    media__contents
    (
        media_fk INT NOT NULL,
        contents_fk INT NOT NULL,
        link_type VARCHAR(30),
        link_order INT,
        PRIMARY KEY (media_fk, contents_fk),
        FOREIGN KEY (media_fk)    REFERENCES media (media_pk)      ON DELETE CASCADE,
        FOREIGN KEY (contents_fk) REFERENCES contents (contents_pk)ON DELETE CASCADE       
    ) ENGINE InnoDB ;
    
      

       
       
       
-- holds archived text fields from  ? and potentially other types
CREATE TABLE textfields   
    (
        -- first 3 field are required for all content extension tables  
        textfields_table VARCHAR(30) NOT NULL, --  tablename
        textfields_table_fk INT NOT NULL,      --  the pk in that table
        textfields_field VARCHAR(30) NOT NULL, --  fieldname
        textfields_version INT NOT NULL,       --  the version 
        
        textfields_body TEXT NOT NULL,         --  the actual text
        textfields_author_fk INT,             --  the author of the text
        textfields_date DATETIME,
        PRIMARY KEY (textfields_table, textfields_table_fk, textfields_field, textfields_version), 
        FOREIGN KEY (textfields_author_fk) REFERENCES users(users_pk)
    )engine InnoDB;
       
       
      

-- to see a version that's not the live version add ?version=23&pw=govtech123 ( to see a version that's not live you need to be logged in or have the pw param 
-- drop table pages;
CREATE TABLE pages   
    (
        pages_pk INT NOT NULL AUTO_INCREMENT,
        pages_id INT NOT NULL,               --  this id does not change with a new version,  
        
        pages_version INT NOT NULL ,    
        pages_version_users_fk INT NOT NULL,   --  the last person to edit this page 
        pages_version_date DATETIME,
        pages_version_comment VARCHAR(255),

        pages_is_preview BIT,
        pages_is_live BIT,     
        pages_site_code VARCHAR(20),         -- GOV, GT, EM, CV, DC, PCIO
        pages_title VARCHAR(150) ,             --  for internal use
        pages_display_title VARCHAR(150) ,    
        pages_url     VARCHAR(50)   NOT NULL,    -- like: /workforce
        pages_type VARCHAR(50)   NOT NULL,     --  TOPIC
        pages_no_robots  BIT,         --  this should set the no_robots line in the <head>
        pages_password VARCHAR(20)  , --  for customer preview only
        pages_status VARCHAR(20),       -- like 'LIVE', 'TEST','OLD' (only 1 version should be live)
        pages_php_class VARCHAR(20) NOT NULL,    --  the class that renders this page        
        pages_body TEXT, 
        
        PRIMARY KEY (pages_pk),
        FOREIGN KEY (pages_version_users_fk) REFERENCES users(users_pk)
    )  engine InnoDB ;

    
create index  pages_id on pages(pages_id);            



-- drop table targets; 
CREATE TABLE targets   
    (
        targets_pages_id INT NOT NULL ,
        targets_contents_fk INT NOT NULL ,
        targets_pin_position  INT DEFAULT 0 NOT NULL,           --  normally  0, but if not null is is the pinned position (1 is first )  
        targets_is_home BIT,									--  this is the home target
        targets_placement VARCHAR(20),                          --  used by modules   to identify the column   
        targets_live_date DATETIME,                                      --  articles will only show between live and dead dates
        targets_archive_date DATETIME DEFAULT '1000-01-01',  
        targets_dead_date DATETIME DEFAULT '1000-01-01',
        PRIMARY KEY (targets_pages_id, targets_contents_fk),
        FOREIGN KEY (targets_pages_id) REFERENCES pages (pages_id)  ON DELETE CASCADE,   
        FOREIGN KEY (targets_contents_fk) REFERENCES contents (contents_pk)   ON DELETE CASCADE        
    )engine InnoDB;   

    
CREATE TABLE modules old   
    (
        modules_pk INT NOT NULL AUTO_INCREMENT,
        modules_id INT NOT NULL,
                       --  this id does not change with a new version,          
        modules_version INT NOT NULL ,    
        modules_version_users_fk INT NOT NULL,   --  the last person to edit this page 
        modules_version_date DATETIME,
        modules_version_comment VARCHAR(255),
        
        modules_is_preview BIT,
        modules_is_live BIT,     
        modules_site_code VARCHAR(20),         -- GOV, GT, EM, CV, DC, PCIO       
        modules_title VARCHAR(150),             --  for internal use
        modules_display_title VARCHAR(150),    
        modules_php_class VARCHAR(150),  
        modules_body TEXT, 
        
        modules_json_parameters VARCHAR(250),
        PRIMARY KEY ( modules_pk),
        FOREIGN KEY (modules_version_users_fk) REFERENCES users(users_pk)
    )  engine InnoDB ;

CREATE TABLE modules__pages 
    (
        contents_fk INT NOT NULL ,
        pages_fk INT NOT NULL ,
        placement VARCHAR(30),
        link_order INT,
        PRIMARY KEY (contents_fk, pages_fk),
        FOREIGN KEY (contents_fk) REFERENCES contents (contents_pk)   ON DELETE CASCADE ,
        FOREIGN KEY (pages_fk) REFERENCES pages (pages_pk)   ON DELETE CASCADE 
    )  engine InnoDB ; 
    
CREATE VIEW max_page_version as
(
SELECT pages_id AS mpv_pages_id, MAX(pages_version) AS mpv_pages_version FROM pages GROUP BY pages_id
);
create view current_pages as
(
 SELECT * FROM pages JOIN max_page_version ON mpv_pages_id = pages_id  AND  pages_version = mpv_pages_version 
);    

    


-- creteing the first user
insert into users (users_last_name, users_first_name, users_password, users_email, users_active) values('Tel','Michael','201f00b5ca5d65a1c118e5e32431514c','mtel@erepublic.com',1);
insert into roles(roles_users_fk,roles_code) values(LAST_INSERT_ID() ,'SUPER_ADMIN');

insert into pages (pages_id,pages_is_live,pages_is_preview,pages_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,
                   pages_body,pages_status,pages_php_class,pages_version_users_fk,pages_version_comment,pages_version_date) 
    values(1,1,1,1,'GT','homepage','Government Technology','/','HOMEPAGE',' homepage ' ,'LIVE','HomePage',1, 'this is the first version', NOW());
insert into pages (pages_id,pages_is_live,pages_is_preview,pages_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,
                   pages_body,pages_status,pages_php_class,pages_version_users_fk,pages_version_comment,pages_version_date) ) 
    values(2,1,1,1,'GT','about','About','/about','',' about gt ' ,'LIVE','StaticPage',1, 'a nice first version ', now());
    
insert into pages (pages_id,pages_is_live,pages_is_preview,pages_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,
                   pages_body,pages_status,pages_php_class,pages_version_users_fk,pages_version_comment,pages_version_date) 
    values(1,1,1,1,'GT','e-government','E-Government','/e-government','CHANNEL','  ' ,'LIVE','ChannelPage',1, 'this is the first version',now());
insert into pages (pages_id,pages_is_live,pages_is_preview,pages_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,
                   pages_body,pages_status,pages_php_class,pages_version_users_fk,pages_version_comment,pages_version_date)  
    values(1,1,1,1,'GT','technology','Emerging and Sustainable Technology','/technology','CHANNEL','  ' ,'LIVE','ChannelPage',1, 'this is the first version',now());
-- ----------------------------------------------------------------------------------------

-- get documentation 
select from contents c
join docu_items di
on c.pk = di.contents_fk
   
   
--  get articles for page        
select * 
from targets t
join contents c on t.content_fk = c.pk
where target.page_pk = 1232 --  our page
and c.status = 'LIVE'
and GETDATE() between t.live_date and t.dead_date
and c.subtype = 'ARTICLE'
order by t.pin_position + create_date

--  get the correct version and all data  of the textfield ( based on content.live_version) 
select top 1 *
from text_fields tf
where tf.content_fk = 1234
and   tf.version <= 123
and   tf.subtype = 'DESCRIPTION_1'
order by tf.version  desc


-- get all the text fields and the corrrect versions for an event
select tf.subtype, max(tf.version)
from text_fields
where tf.content_pf = 1234
and tf.version <= 123
group by tf.subtype
    
    
    
select content_id , max(version)  
from contents c  group by content_id 
join text_fields  tf 
on c.content_pk = tf.content_pk
where tf.revision <= c.published.revision  
     
   
  -- --------------------- examples
  SELECT @last_version := pages_version , @last_pk := pages_pk FROM pages WHERE pages_version  = (select max(pages_version) from pages where pages_id = 2   ) and  pages_id = 2
