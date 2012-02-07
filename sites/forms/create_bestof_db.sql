-- run this script on the db server with:  mysql --user=root --password=root < create_bestof_db.sql 
CREATE TABLE forms.bestof_contests
    (
        contest_pk INT NOT NULL,
        contest_name VARCHAR(255) ,
        contest_URL VARCHAR(40) ,
        contest_judges VARCHAR(256) ,
        contest_startdate DATETIME,
        contest_closedate DATETIME,
        contest_enddate DATETIME,
        contest_desc1 TEXT ,
        contest_desc2 TEXT ,
        contest_desc3 TEXT ,
        contest_desc4 TEXT ,
        contest_desc5 TEXT ,
        contest_template VARCHAR(255) ,
        custom_header VARCHAR(255) ,
        limit_player BIT,
        PRIMARY KEY (contest_pk)
    )engine InnoDB;  

CREATE TABLE forms.bestof_players
    (
        player_pk INT NOT NULL,
        player_fname VARCHAR(60) ,
        player_lname VARCHAR(80) ,
        player_title VARCHAR(255) ,
        player_org VARCHAR(255) ,
        player_desc TEXT,
        player_role INT,
        player_password VARCHAR(40) ,
        player_addr1 VARCHAR(255) ,
        player_addr2 VARCHAR(255) ,
        player_city VARCHAR(100) ,
        player_state VARCHAR(80) ,
        player_zip VARCHAR(20) ,
        player_phone VARCHAR(60) ,
        player_fax VARCHAR(60) ,
        player_email VARCHAR(60) ,
        player_addlinfo VARCHAR(100) ,
        player_limit_event INT,
        PRIMARY KEY (player_pk)
    )engine InnoDB;     
    
    

drop table forms.bestof_categories;
CREATE TABLE forms.bestof_categories
    (
        category_pk int NOT NULL ,
        category_contestid INT,
        category_name VARCHAR(255) ,
        category_sortby INT DEFAULT 5,
        FOREIGN KEY (category_contestid) REFERENCES bestof_contests (contest_pk) ,
        INDEX category_contestid (category_contestid),
        PRIMARY KEY (category_pk)
    )engine InnoDB;  
    
drop table forms.bestof_entries;
CREATE TABLE    forms.bestof_entries
    (
        entry_pk INT NOT NULL ,
        entry_contestid INT,
        entry_playerid INT,
        entry_category INT,
        entry_title VARCHAR(255) ,
        entry_desc TEXT ,
        entry_lock BIT,
        entry_url VARCHAR(255) ,
        entry_date VARCHAR(64) ,
        FOREIGN KEY (entry_category)  REFERENCES bestof_categories (category_pk) ,
        FOREIGN KEY (entry_playerid)  REFERENCES bestof_players (player_pk) ,
        FOREIGN KEY (entry_contestid) REFERENCES bestof_contests (contest_pk) ,
        INDEX entry_category (entry_category),
        INDEX entry_playerid (entry_playerid),
        PRIMARY KEY (entry_pk)
    ) engine InnoDB;     
    
drop table forms.bestof_questions;
CREATE TABLE forms.bestof_questions
    (
        question_pk INT NOT NULL ,
        question_contestid INT,
        question_text TEXT ,
        question_order INT,
        question_category INT,
        FOREIGN KEY (question_category)  REFERENCES bestof_categories (category_pk) ,
        FOREIGN KEY (question_contestid) REFERENCES bestof_contests (contest_pk) ,
        INDEX question_category (question_category),
        INDEX question_contestid (question_contestid),
        PRIMARY KEY (question_pk)
    )engine InnoDB;  

drop table forms.bestof_answers;   
CREATE TABLE forms.bestof_answers
    (
        answer_pk INT NOT NULL ,
        answer_entryid INT,
        answer_playerid INT,
        answer_eventid INT,
        answer_questionid INT,
        answer_text TEXT ,
        answer_judge INT,
        answer_rating INT,
        answer_comments TEXT ,
        answer_category INT,
        FOREIGN KEY (answer_entryid)  REFERENCES bestof_entries  (entry_pk) ,
        FOREIGN KEY (answer_playerid)  REFERENCES bestof_players (player_pk) ,
        FOREIGN KEY (answer_questionid)  REFERENCES bestof_questions (question_pk) ,
        INDEX answer_questionid (answer_questionid),
        INDEX answer_entryid (answer_entryid),
        INDEX answer_playerid (answer_playerid),
        PRIMARY KEY (answer_pk)
    )engine InnoDB;  
    

drop table forms.bestof_scores;    
CREATE TABLE forms.bestof_scores
    (
        score_pk INT NOT NULL ,
        score_contestid INT,
        score_judge INT,
        score_entryid INT,
        score_category INT,
        score_questionid INT,
        score_answerid INT,
        score_value INT,
        score_comments TEXT ,
        FOREIGN KEY (score_contestid) REFERENCES bestof_contests (contest_pk) ,
        FOREIGN KEY (score_entryid) REFERENCES  bestof_entries (entry_pk) ,
        FOREIGN KEY (score_questionid) REFERENCES  bestof_questions (question_pk) ,
        FOREIGN KEY (score_judge) REFERENCES  bestof_players (player_pk) ,
        INDEX score_questionid (score_questionid),
        INDEX score_entryid (score_entryid),
        INDEX score_contestid (score_contestid),
        INDEX score_judge (score_judge),
        
        
        PRIMARY KEY (score_pk)
    )engine InnoDB;         

    
SELECT contest_pk, contest_name, entry_title , entry_contestid , player_fname, player_lname
FROM  (bestof_contests JOIN bestof_entries ON contest_pk  = entry_contestid )
      JOIN bestof_players ON player_pk = entry_playerid
      JOIN bestof_answers ON answer_entryid = entry_pk;
      
      
      