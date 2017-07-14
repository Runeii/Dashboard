DROP TABLE IF EXISTS gb_dashboard.dates;
CREATE TABLE gb_dashboard.dates (
idDate INTEGER PRIMARY KEY, -- year10000+month100+day
fulldate DATE NOT NULL,
year INTEGER NOT NULL,
month INTEGER NOT NULL, -- 1 to 12
day INTEGER NOT NULL, -- 1 to 31
quarter INTEGER NOT NULL, -- 1 to 4
week INTEGER NOT NULL, -- 1 to 52/53
dayOfWeek INTEGER NOT NULL, -- 1 to 7
weekend INTEGER NOT NULL,
UNIQUE td_ymd_idx (year,month,day),
UNIQUE td_dbdate_idx (fulldate)

) Engine=innoDB;

DROP PROCEDURE IF EXISTS fill_date_dimension;
DELIMITER //
CREATE PROCEDURE fill_date_dimension(IN startdate DATE,IN stopdate DATE)
BEGIN
DECLARE currentdate DATE;
SET currentdate = startdate;
WHILE currentdate < stopdate DO
INSERT INTO gb_dashboard.dates VALUES (
YEAR(currentdate)*10000+MONTH(currentdate)*100 + DAY(currentdate),
currentdate,
YEAR(currentdate),
MONTH(currentdate),
DAY(currentdate),
QUARTER(currentdate),
WEEKOFYEAR(currentdate),

CASE DAYOFWEEK(currentdate)-1 WHEN 0 THEN 7 ELSE DAYOFWEEK(currentdate)-1 END ,
CASE DAYOFWEEK(currentdate)-1 WHEN 0 THEN 1 WHEN 6 then 1 ELSE 0 END);
SET currentdate = ADDDATE(currentdate,INTERVAL 1 DAY);
END WHILE;
END
//
DELIMITER ;

TRUNCATE TABLE gb_dashboard.dates;

CALL fill_date_dimension('2017-06-01','2018-12-31');
OPTIMIZE TABLE gb_dashboard.dates;
