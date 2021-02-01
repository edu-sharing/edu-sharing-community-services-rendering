CREATE TABLE "ESOBJECT_CONVERSION" (
  "ESOBJECT_CONVERSION_ID" SERIAL,
  "ESOBJECT_CONVERSION_OBJECT_ID" integer default NULL,
  "ESOBJECT_CONVERSION_FORMAT" varchar(255) default NULL,
  "ESOBJECT_CONVERSION_RESOLUTION" integer default NULL,
  "ESOBJECT_CONVERSION_MIMETYPE" varchar(255) default NULL,
  "ESOBJECT_CONVERSION_FILENAME" varchar(255) default NULL,
  "ESOBJECT_CONVERSION_OUTPUT_FILENAME" varchar(255) default NULL,
  "ESOBJECT_CONVERSION_TIME" integer default NULL,
  "ESOBJECT_CONVERSION_STATUS" varchar(255) default NULL,
  PRIMARY KEY ("ESOBJECT_CONVERSION_ID")
);
