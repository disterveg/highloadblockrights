CREATE TABLE IF NOT EXISTS `dg_hlblock_element_rights` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ELEMENT_ID` int(11) unsigned NOT NULL,
  `HL_ID` int(11) unsigned NOT NULL,
  `TASK_ID` int(11) unsigned NOT NULL,
  `ACCESS_CODE` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
);