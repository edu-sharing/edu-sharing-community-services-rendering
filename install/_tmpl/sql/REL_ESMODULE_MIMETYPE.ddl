CREATE TABLE "REL_ESMODULE_MIMETYPE" (
  "REL_ESMODULE_MIMETYPE_ID" SERIAL,
  "REL_ESMODULE_MIMETYPE_ESMODULE_ID" integer default NULL,
  "REL_ESMODULE_MIMETYPE_TYPE" varchar(200) default NULL,
  PRIMARY KEY  ("REL_ESMODULE_MIMETYPE_ID")
);