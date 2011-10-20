--  

-- table name should be one word without unserscores
-- pk should be tablename_pk
-- foreign keys should be in the format:     []foreigntablename_fk

drop table media;
drop table articles;
drop table targets;
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
    
-- drop table contents;   
CREATE TABLE
    contents
    (
        contents_pk INT NOT NULL AUTO_INCREMENT, 
        contents_live_version INT NOT NULL,
        contents_url_name VARCHAR(500),
        contents_title VARCHAR(150) NOT NULL,
        contents_display_title VARCHAR(150),
        contents_summary VARCHAR(500),
        contents_create_date TIMESTAMP,
        contents_type VARCHAR(20) NOT NULL,
        contents_status VARCHAR(20),
        contents_main_authors_fk INT,
  
        PRIMARY KEY (contents_pk),
        FOREIGN KEY (contents_main_authors_fk) REFERENCES users(users_pk)      
    )engine InnoDB;



    
-- drop table docu_items   ;
CREATE TABLE specs   
    (
        -- first 3 field are required for all content extension tables  
        specs_contents_fk INT NOT NULL ,
        specs_version INT  NOT NULL,
        specs_authors_fk INT,                -- author of latest change
        
        specs_indexing VARCHAR(30),
        specs_type  VARCHAR(20),
        specs_user_docu   TEXT , 
        specs_design_docu TEXT ,
        
        PRIMARY KEY (specs_contents_fk, specs_version),
        FOREIGN KEY (specs_contents_fk) REFERENCES contents(contents_pk) ON DELETE CASCADE,  
        FOREIGN KEY (specs_authors_fk) REFERENCES users(users_pk)
    )engine InnoDB;
       
-- drop table articles;
CREATE TABLE articles   
    (
        -- first 3 field are required for all content extension tables  
        articles_contents_fk INT NOT NULL ,
        articles_version INT  NOT NULL,
        articles_authors_fk INT,                -- author of latest change
        articles_update_date TIMESTAMP,
         
        articles_type  VARCHAR(20),
        articles_body   TEXT , 
     
        PRIMARY KEY (articles_contents_fk, articles_version),
        FOREIGN KEY (articles_contents_fk) REFERENCES contents(contents_pk) ON DELETE CASCADE,  
        FOREIGN KEY (articles_authors_fk) REFERENCES users(users_pk)
    )engine InnoDB;
       
       
CREATE TABLE modules   
    (
        modules_pk INT NOT NULL AUTO_INCREMENT,
        modules_title VARCHAR(150),             --  for internal use
        modules_display_title VARCHAR(150),    
        modules_php_class VARCHAR(150),  
        modules_json_parameters VARCHAR(250),
        PRIMARY KEY ( modules_pk)
    )  engine InnoDB ;

-- to see a version that's not the live version add ?version=23&pw=govtech123 ( to see a version that's not live you need to be logged in or have the pw param 
-- drop table pages;
CREATE TABLE pages   
    (
        pages_pk INT NOT NULL AUTO_INCREMENT,
        pages_version INT NOT NULL ,     
        pages_site_code VARCHAR(20),         -- GOV, GT, EM, CV, DC, PCIO
        pages_title VARCHAR(150) ,             --  for internal use
        pages_display_title VARCHAR(150) ,    
        pages_url     VARCHAR(50)   NOT NULL,    -- like: /workforce
        pages_type VARCHAR(50)   NOT NULL,     --  TOPIC
        pages_body TEXT  ,                        --  for simple page like about
        pages_no_robots  BIT,         --  this should set the no_robots line in the <head>
        pages_password VARCHAR(20)  , --  for customer preview only
        pages_status VARCHAR(20),       -- like 'LIVE', 'TEST','OLD' (only 1 version should be live)
        pages_php_class VARCHAR(20),    --  the class that renders this page  
        pages_authors_fk INT NOT NULL,   --  the last person to edit this page 
        
        
        PRIMARY KEY (pages_pk, pages_version)
    )  engine InnoDB ;


-- drop table targets; 
CREATE TABLE targets   
    (
        targets_pages_fk INT NOT NULL ,
        targets_contents_fk INT NOT NULL ,
        targets_pin_position  INT DEFAULT 0 NOT NULL,           --  normally  0, but if not null is is the pinned position (1 is first )     
        targets_live_date TIMESTAMP,                                      --  articles will only show between live and dead dates
        targets_dead_date TIMESTAMP,
        PRIMARY KEY (targets_pages_fk, targets_contents_fk),
        FOREIGN KEY (targets_pages_fk) REFERENCES pages (pages_pk)  ON DELETE CASCADE,   
        FOREIGN KEY (targets_contents_fk) REFERENCES contents (contents_pk)   ON DELETE CASCADE        
    )engine InnoDB;   

-- creteing the first user
insert into users (users_last_name, users_first_name, users_password, users_email, users_active) values('Tel','Michael','201f00b5ca5d65a1c118e5e32431514c','mtel@erepublic.com',1);
insert into roles(roles_users_fk,roles_code) values(LAST_INSERT_ID() ,'SUPER_ADMIN');

insert into pages (pages_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,pages_body,pages_status,pages_php_class,pages_authors_fk) 
    values(1,'GT','homepage','Government Technology','/','HOMEPAGE',' ' ,'LIVE','Homepage',3);
insert into pages (pages_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,pages_body,pages_status,pages_php_class,pages_authors_fk) 
    values(1,'GT','about','About','/about','',' ' ,'LIVE','StaticPage',3);

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
     
   
  
