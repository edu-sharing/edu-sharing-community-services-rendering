CREATE TABLE "ESOBJECT" (
  "ESOBJECT_ID" SERIAL,
  "ESOBJECT_ESMODULE_ID" integer default NULL,
  "ESOBJECT_TITLE" varchar(512) default NULL,
  "ESOBJECT_ALF_FILENAME" varchar(100) default NULL,
  "ESOBJECT_REP_ID" varchar(100) default NULL,
  "ESOBJECT_OBJECT_ID" varchar(100) default NULL,
  "ESOBJECT_OBJECT_VERSION" varchar(5) default NULL,
  "ESOBJECT_MIMETYPE" varchar(100) default NULL,
  "ESOBJECT_RESOURCE_TYPE" varchar(40) default NULL,
  "ESOBJECT_RESOURCE_VERSION" varchar(40) default NULL,
  "ESOBJECT_PATH" varchar(155) default NULL,
  "ESOBJECT_CONTENT_HASH" varchar(255) default NULL,
  PRIMARY KEY  ("ESOBJECT_ID")
);
