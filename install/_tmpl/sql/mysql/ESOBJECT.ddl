CREATE TABLE `ESOBJECT` (
  `ESOBJECT_ID` int(11) NOT NULL auto_increment,
  `ESOBJECT_ESMODULE_ID` int(11) default NULL,
  `ESOBJECT_TITLE` varchar(150) default NULL,
  `ESOBJECT_ALF_FILENAME` varchar(100) default NULL,
  `ESOBJECT_REP_ID` varchar(100) default NULL,
  `ESOBJECT_OBJECT_ID` varchar(100) default NULL,
  `ESOBJECT_OBJECT_VERSION` varchar(5) default NULL,
  `ESOBJECT_MIMETYPE` varchar(100) default NULL,
  `ESOBJECT_RESOURCE_TYPE` varchar(40) default NULL,
  `ESOBJECT_RESOURCE_VERSION` varchar(40) default NULL,
  `ESOBJECT_PATH` varchar(155) default NULL,
  `ESOBJECT_CONTENT_HASH` varchar(255) default NULL,
  PRIMARY KEY  (`ESOBJECT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
