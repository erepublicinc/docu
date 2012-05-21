
CREATE PROCEDURE `navigator`.`add_bidcatlink`(IN pk INT, IN code VARCHAR(40) )
    MODIFIES SQL DATA
BEGIN
  
  DECLARE id INT;
  SET id = (select bids_id from bids where bids_pk = pk); 
  IF id IS NOT NULL THEN
     INSERT INTO bids_x_bidcats (bids_fid, bidcats_code)  VALUES(id, code);
  END IF;
 
END;


CREATE PROCEDURE `navigator`.`add_bid_doc`(IN pk INT, IN url VARCHAR(200), IN title VARCHAR(100), IN subtype VARCHAR(40) )
    MODIFIES SQL DATA
BEGIN
  DECLARE id INT;
  SET id = (select bids_id from bids where bids_pk = pk); 
  IF id IS NOT NULL THEN
     INSERT INTO bid_links (bid_links_fid, bid_links_title, bid_links_url, bid_links_type) 
     VALUES(id, title, url, subtype);
  END IF;
 
END;     

CREATE PROCEDURE `navigator`.`add_org_doc`(IN pk INT, IN txt TEXT, IN title VARCHAR(100), IN subtype VARCHAR(40), IN mod_date DATETIME )
    MODIFIES SQL DATA
BEGIN
  DECLARE id INT;
  SET id = (select orgs_id from orgs where orgs_pk = pk); 
  IF id IS NOT NULL THEN
     INSERT INTO docs (docs_type, docs_title, docs_mod_date, docs_text) VALUES(subtype, title, mod_date, txt);
     INSERT INTO org_links (org_links_fid, org_links_title,  org_links_type, org_links_docs_fid) 
     VALUES(id, title,  subtype, LAST_INSERT_ID());
  END IF;
 
END;

CREATE PROCEDURE `navigator`.`add_org_link`(IN pk INT, IN url VARCHAR(200), IN title VARCHAR(100), IN subtype VARCHAR(40), IN docs_fid INT )
    MODIFIES SQL DATA
BEGIN
  DECLARE id INT;
  SET id = (select orgs_id from orgs where orgs_pk = pk); 
  IF id IS NOT NULL THEN
     INSERT IGNORE INTO org_links (org_links_fid, org_links_title, org_links_url, org_links_type, org_links_docs_fid) 
     VALUES(id, title, url, subtype, docs_fid);
  END IF;
 
END;
