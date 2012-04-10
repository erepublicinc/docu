-- run this script on the db server with:  mysql --user=root --password=root < create_forms_db.sql 
drop schema forms;
create schema forms;

grant select on forms.* to 'web_sites'@'%';
grant select, insert, update, delete on forms.* to 'web_sites_admin'@'%';

CREATE TABLE forms.forms
    (
        forms_id            INT NOT NULL AUTO_INCREMENT,
        forms_tpl           VARCHAR(50),
        forms_title         VARCHAR(100),
        forms_display_title VARCHAR(100),
        forms_url_name      VARCHAR(100),
        forms_https         BIT DEFAULT 1,
        forms_start_date    DATETIME DEFAULT '2000-01-01',  
        forms_end_date      DATETIME DEFAULT '2100-01-01',  
        forms_css           VARCHAR(100),
        forms_site_code     VARCHAR(100) NOT NULL,
        forms_eloqua_formid VARCHAR(50),
        forms_xml_data      TEXT,
        forms_php_class     VARCHAR(100),
        forms_header        TEXT,
        forms_footer        TEXT,
        CONSTRAINT form_title_unique UNIQUE (forms_title),
        PRIMARY KEY (forms_id)
    )engine InnoDB;  
        
CREATE TABLE forms.fields
    (
        fields_id           INT NOT NULL AUTO_INCREMENT,
        forms_fid           INT NOT NULL,
                            
        fields_label        VARCHAR(100),        
        fields_html_name    VARCHAR(50) NOT NULL,
        fields_tpl          VARCHAR(50),
        fields_type         VARCHAR(50),
        fields_class        VARCHAR(50),
        fields_validation   VARCHAR(50),
        fields_required     BIT DEFAULT 1,
        fields_eloqua_name  VARCHAR(50),
        fields_values       VARCHAR(255),  --  small list of possible values, pipe delimited , for a select box.  
                                           --   Use [STATES_PROVINCES] for big lists)
        fields_locked       BIT DEFAULT 1, -- locked fields cannot change type, form_name, eloqua_name
        fields_order        INT NOT NULL,                                   
        PRIMARY KEY (fields_id),
       
        FOREIGN KEY (forms_fid) REFERENCES forms (forms_id)  ON DELETE CASCADE
    )engine InnoDB;  
        
CREATE TABLE forms.field_masters
    (
        field_masters_id    INT NOT NULL AUTO_INCREMENT,
        fields_label        VARCHAR(100) NOT NULL,
        fields_html_name    VARCHAR(50) NOT NULL,
        fields_tpl          VARCHAR(50),
        fields_type         VARCHAR(50) NOT NULL,
        fields_class        VARCHAR(50),
        fields_validation   VARCHAR(50),
        fields_required     BIT DEFAULT 1,
        fields_eloqua_name  VARCHAR(50),
        fields_values       VARCHAR(255),
        
        fields_locked       BIT DEFAULT 1, -- locked fields cannot change type, form_name, eloqua_name
        CONSTRAINT form_name_unique UNIQUE (fields_html_name),
        PRIMARY KEY (field_masters_id)
    )engine InnoDB;  
    
CREATE TABLE forms.form_all_submissions
    (
        submit_id       INT NOT NULL AUTO_INCREMENT,
        forms_fid       INT NOT NULL ,
        submit_date     TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
        submit_data     TEXT,
        PRIMARY KEY (submit_id),
        FOREIGN KEY (forms_fid) REFERENCES forms (forms_id)  ON DELETE CASCADE
    )engine InnoDB;  

--  CREATE forms.form_submission_XX   for a specific form

insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('First Name','first_name','text','alphanumeric',1, 'C_first_name') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Last Name','last_name','text','alphanumeric',1, 'C_last_name') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Title','title','text','alphanumeric',1, 'C_title') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Company','company','text','alphanumeric',1, 'C_company') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Address ','address','text','alphanumeric',1, 'C_address1') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Address 2','address2','text','alphanumeric',0, 'C_address2') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('City','city','text','alphanumeric',1, 'C_city') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_values ) 
                          VALUES('State/Province','state_province','select','',1, 'C_state_province','[STATES_PROVINCES]') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_values ) 
                          VALUES('Country','country','select','',1, 'C_country','USA|Canada') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Zip/Postal Code','zip','text','zip',1, 'C_zip') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Phone','phone','text','phone',1, 'C_phone') ;
                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Fax','fax','text','phone',0, 'C_fax') ;
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name ) 
                          VALUES('Email','email','text','email',1, 'C_email') ;

insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('extra 1','extra_field1','text','',0, 'C_extra1',0) ;
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('extra 2','extra_field2','text','',0, 'C_extra2',0) ;
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('extra 3','extra_field3','text','',0, 'C_extra3',0) ;
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('extra 4','extra_field4','text','',0, 'C_extra4',0) ;
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('extra 5','extra_field5','text','',0, 'C_extra5',0) ;
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('extra 6','extra_field6','text','',0, 'C_extra6',0) ;
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('extra 7','extra_field7','text','',0, 'C_extra7',0) ;
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('* html','html','html','',0, '',0) ;                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('* divider','divider','divider','',0, '',0) ;                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('* CreditCard','cc','credit_card','',0, '',0) ;                          
insert into forms.field_masters (fields_label, fields_html_name, fields_type, fields_validation, fields_required, fields_eloqua_name, fields_locked ) 
                          VALUES('Submit','submit','submit','',0, '',0) ;                               
