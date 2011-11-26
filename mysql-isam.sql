

drop table users
CREATE TABLE users
    (
        pk INT NOT NULL AUTO_INCREMENT,
        last_name VARCHAR(20) ,
        first_name VARCHAR(20),
        password VARCHAR(40) ,
        email VARCHAR(70)   NOT NULL,
        ad_user VARCHAR(20) ,
        user_active BIT DEFAULT 0 NOT NULL,
        PRIMARY KEY (pk),
        CONSTRAINT users_email_unique UNIQUE (email)
    ); 

drop table roles;
CREATE TABLE roles   
    (
        users_fk INT NOT NULL ,
        role_code VARCHAR(20) NOT NULL ,
        CONSTRAINT  FOREIGN KEY (users_fk) REFERENCES users (users_fk),      
        PRIMARY KEY (users_fk, role_code)
    );  

drop table media;        
CREATE TABLE  media
    (
        pk INT NOT NULL AUTO_INCREMENT,
        url VARCHAR(500)   NOT NULL,
        title VARCHAR(150)  ,
        summary VARCHAR(500)  ,
        create_date DATETIME,
        credit VARCHAR(255)  ,
        link VARCHAR(255)  ,
        new_window BIT DEFAULT 0 NOT NULL,
        subtype VARCHAR(20)   NOT NULL,
        alt VARCHAR(255)  ,
        PRIMARY KEY (pk)
    );
    
drop table contents;   
CREATE TABLE
    contents
    (
        pk INT NOT NULL AUTO_INCREMENT,
   --     version INT NOT NULL,
        live_version INT NOT NULL,
        url_name VARCHAR(500),
        title VARCHAR(150) NOT NULL,
        display_title VARCHAR(150),
        summary VARCHAR(500),
        create_date DATETIME,
        subtype VARCHAR(20) NOT NULL,
        status VARCHAR(20),
        main_author_fk INT,
        
        PRIMARY KEY (pk, version),
        INDEX contents_fk1 (main_author_fk), 
        FOREIGN KEY (main_author_fk) REFERENCES users(pk),  
        INDEX contents_fk2 (author_fk),
        FOREIGN KEY (author_fk) REFERENCES users(pk)
    );
    
drop table docu_items;   
CREATE TABLE docu_items   
    (
        -- first 3 field are required for all content extension tables  
        contents_fk INT NOT NULL ,
        version INT  NOT NULL,
        author_fk INT,                -- author of latest change
        
        indexing VARCHAR(30),
        subtype VARCHAR(20),
        user_docu TEXT , 
        design_docu TEXT ,
        INDEX docu_items_fk (contents_fk),
        FOREIGN KEY (contents_fk) REFERENCES contents(pk)
        PRIMARY KEY (contents_fk, version)
        INDEX contents_fk2 (author_fk),
        FOREIGN KEY (author_fk) REFERENCES users(pk)
    );
        
       
CREATE TABLE text_fields   
    (
        pk INT NOT NULL AUTO_INCREMENT,
        version INT,     
        contents_fk INT NOT NULL ,
        zorder INT,
        subtype VARCHAR(50)  NOT NULL,        -- content types may have more than 1 texfield
        body TEXT , 
        CONSTRAINT  FOREIGN KEY (contents_fk) REFERENCES contents (contents_fk), 
        PRIMARY KEY (pk, version)
    );  


-- to see a version that's not the live version add ?version=23&pw=govtech123 ( to see a version that's not live you need to be logged in or have the pw param 
drop table pages;
CREATE TABLE pages   
    (
        pk INT NOT NULL AUTO_INCREMENT,
        version INT NOT NULL ,     
        site_code VARCHAR(20),         -- GOV, GT, EM, CV, DC, PCIO
        title VARCHAR(150) ,             --  for internal use
        display_title VARCHAR(150) ,    
        url     VARCHAR(50)   NOT NULL,    -- like: /workforce
        subtype VARCHAR(50)   NOT NULL,     --  ?
        body TEXT  ,                        --  for simple page like about
        no_robots  BIT,         --  this should set the no_robots line in the <head>
        password VARCHAR(20)  , --  for customer preview only
        status VARCHAR(20),       -- like 'LIVE', 'TEST','OLD' (only 1 version should be live)
        php_class VARCHAR(20),    --  the class that renders this page  
        
        listing_left_column_modules VARCHAR(500),     -- comma separated list of module_pk's in the right order like:   123,12331,42354,433
        listing_center_column_modules VARCHAR(500) ,
        listing_right_column_modules VARCHAR(500),
        detail_left_column_modules VARCHAR(500) ,
        detail_center_column_modules VARCHAR(500) ,
        detail_right_column_modules VARCHAR(500) ,
                
        PRIMARY KEY (pk, version)
    ) ;


drop table targets; 
CREATE TABLE targets   
    (
        pages_fk INT NOT NULL ,
        contents_fk INT NOT NULL ,
        pin_position  INT DEFAULT 0 NOT NULL,           --  normally  0, but if not null is is the pinned position (1 is first )     
        live_date DATETIME,                                      --  articles will only show between live and dead dates
        dead_date DATETIME,
        CONSTRAINT  FOREIGN KEY (pages_fk) REFERENCES pages (pages_fk), 
        CONSTRAINT  FOREIGN KEY (contents_fk) REFERENCES contents (contents_fk),         
        PRIMARY KEY (pages_fk, contents_fk)
    );   




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
     
   
  
