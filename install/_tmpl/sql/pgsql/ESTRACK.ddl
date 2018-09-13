CREATE TABLE "ESTRACK" (
  "ESTRACK_ID" SERIAL,
  "ESTRACK_ESOBJECT_ID" varchar(40) default NULL,
  "ESTRACK_APP_ID" varchar(40) default NULL,
  "ESTRACK_REP_ID" varchar(40) default NULL,
  "ESTRACK_LMS_COURSE_ID" varchar(40) default NULL,
  "ESTRACK_OBJECT_ID" varchar(40) default NULL,
  "ESTRACK_NAME" varchar(512) default NULL,
  "ESTRACK_MODUL_ID" varchar(512) default NULL,
  "ESTRACK_MODUL_NAME" varchar(20) default NULL,
  "ESTRACK_VERSION" varchar(20) default NULL,
  "ESTRACK_USER_NAME" varchar(40) default NULL,
  "ESTRACK_USER_ID" varchar(40) default NULL,
  "ESTRACK_TIME" timestamp NOT NULL default CURRENT_TIMESTAMP,
  "STATE" varchar(1) default 'Y',
  PRIMARY KEY  ("ESTRACK_ID")
);
