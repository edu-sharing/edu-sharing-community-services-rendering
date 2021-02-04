<?php
/*
* $McLicense$
*
* $Id$
*
*/

define("install_logo",  "_img/edulogo.png");
define("install_headline",  "edu-sharing rendering service ".$version_info . " installation");
//define("install_intro", "This version requires PHP 5.x / MySQL 5.x<br/>");
define("install_submit",      "Send");
define("install_save",      "Save");
define("install_continue",  "Continue");
define("install_save_continue", "Save and continue");
define("install_back",      "back");

define("install_label_01", "Server Settings");
define("install_label_03", "Renderservice URL");
define("install_label_04", "Database Settings");
define("install_label_05", "Host");
define("install_label_05a", "Port");
define("install_label_06", "User");
define("install_label_07", "Password");
define("install_label_08", "Name of database");
define("install_label_09", "Show detailed debug messages <br>during installation<br/>");
define("install_label_11", "Edu-sharing Settings");
define("install_label_12", "Select language / Sprache wählen");
//
define("install_label_14", "Please accept the terms of use.<br>You are not allowed to neither install nor use the product without accepting the terms of use before.");
define("install_label_15", "Yes, I have read the terms of use and agree to them.");
define("install_label_16", "Administrator login for plattform");
define("install_label_17", "Name");
define("install_label_18", "Password");
define("install_label_19", "Confirm password");
define("install_label_20", "Email address");
define("install_label_21", "renderservice BASEDIR");
define("install_label_22", "Home repository settings");
define("install_label_23", "Base URL");
define("install_label_24", "Data directory");
define("install_label_25", "Name");
define("install_label_26", "Password");

define("install_err_terms_not_accepted",        "You have not accepted the terms of use!<br>"
    ."In order to use this software you need to accept the terms of use first!");
define("install_err_content_dir_empty",     "'No content directory specified'");
define("install_err_docroot_match",     'The content directory "%s" is inside of document root. This is forbidden for security reasons.');
define("install_err_dir_access_denied",     "ACCESS ERROR!<p>Write access for '%s' denied.<p>"
    ."Please change the directories permissions to write access for PHP.");
define("install_err_dir_not_found",     "ACCESS ERROR!<p>Directory '%s' does not exist.<p>"
  ."Please create a directory named '%s' First or choose the name of an existing directory.");
define("install_err_db_access_denied",      "Connection to database server failed. "
    ."Please check the connection parameters.");
define("install_err_admin_name_empty", "Name for administrator login was empty. ");
define("install_err_admin_pass_empty", "Password for administrator was empty. ");
define("install_err_admin_pass_not_confirmed", "Password and password confirmation are not the same value! ");
define("install_err_admin_mail_empty", "Email address was empty. ");

define("install_msg_sql_safe_mode",     'SQL-Safemode is activated (<a href="http://de.php.net/manual/en/ini.core.php#ini.sql.safe-mode" target="_blank" style="color:blue;">-&gt;Information</a>) !<br>'
    .'The Server uses the following access data :');

define("install_step",          "Step");
define("install_debug_open_file",   "Open file");
define("install_debug_scan_file",   "Search file");
define("install_writing",       "Write file content");
define("install_load",          "Load");
define("install_create",        "Create");
define("install_make_db_backup",    "Backup database %s into file '%s' ...");
define("intall_err_no_localhost",   "External mysql server is not supported by installation tool (localhost only).<br>please copy the modificated mysql dump file ('%s') to your external mysql server and load the dump file manually (with 'mysql databasename < filename').");

define("install_scanning",      "search");
define("install_write",         "write");
define("install_of",            "out of");

define("install_not_updated",       "not changed");
define("install_updated",       "changed");
define("install_skipped",       "skipped");
define("install_found",         "found");
define("install_success",       "successful");
define("install_saved",         "saved");
define("install_done",          "done");
define("install_finished",      "finished");
define("install_failed",        "failed");

define("install_replacement",       "file token replacements");
define("install_file",          "file");
define("install_files_scanned",     "files searched");
define("install_files_updated",     "files changed");
define("install_err_no_file_content",   "File '%s' has no content or is not readable.");
define("install_err_no_access",     "No write access for directory '%s' !");
define("install_err_creating_file", "Creating file '%s' failed.");
define("install_err_writing_file",  "Writing file '%s' failed.");

define("install_all_replaced",      "All Tokens replaced");
define("install_conf_saved",        "Saving configuration file");
define("install_scan_all",      "Search all files for replaceable tokens");
define("install_open_db",       "Open database dump");
define("install_err_open_db",       "Opening database dump ('%s') failed.");
define("install_open_tmp",      "Create temporary copy of database");
define("install_err_open_tmp",      "Creating/Opening temporary copy of database ('%s') in write mode (+w) failed.");
define("install_write_dump",        "Search database dump for replaceable tokens, repleace tokens and write into database copy");
define("install_err_write_dump",    "Writing into temporary database copy ('%s') failed.");
define("install_write_pre",         "Error: Dump-prefix could not be written.");
define("install_write_line",        "Writing line into database copy failed");
define("install_write_suf",         "Error: Dump-suffix could not be written.");
define("install_load_dump_prepare", "Prepare loading of database dump ...<br>");
define("install_load_dump",         "Loading copy of database dump into mysql server.");
define("install_load_dump_err",     "Error: Loading copy of database dump into mysql server failed.");
define("install_dump_loaded",       "Database dump loaded and patched (settting first COURSE_ID to \"0\")");

define("install_err_msg",       "<p>Error in <strong>file '%s', line %s</strong><br>%s<br>Error: %s<hr>");
define("install_some_errors_msg",   "One or more error occured while processing  installation.");
define("install_complete",      " Installation finished (without errors).");
define("install_some_errors",       "Error while processing  installation.");
define("install_check_log",         "Please check the debug messages above for further error informations.");
define("installation_canceled",     "An error occured while processing the installation routine. Installation canceled.<br>(Please run installation with activated checkbox for more detailed debug information.)");

define("install_finish",        "Congratulations! Please rename the directory of the installation script (&quot;%s&quot;).<br>Login at &quot;%s&quot; as administrator (name/password: admin/admin). Have Fun! ;-)");


// step initdb
define("initdb_missing_form_data",      'No form data received!');
define("initdb_database_exists",          'Database "%s" already exists.');
define("initdb_database_created",       'Database "%s" created.');
define("initdb_database_failed",          'Creating database "%s" failed!');
//define("initdb_label_max_filesize",   'Maximum filesize for file upload');
define("install_msg_filedir_exists",   'Directory %s already exists and is writable.'); // filedirname
define("install_err_filedir_exist_fail",   'Directory %s already exists but is NOT WRITABLE!'); // filedirname
define("install_msg_filedir_need_write",   'You need to grant write access for PHP to this directory!');
define("install_msg_filedir_created",      'Directory %s successfully created.'); // filedirname
define("install_err_filedir_create_fail",  'Creating directory %s FAILED!'); // filedirname
define("install_err_filedir_parent_fail",   'Directory %s is NOT WRITABLE!'); // parentdirname
define("install_msg_filedir_parent_fail",   'You need to either create the directory "%s" by yourself or grant write access for PHP to directory "%s".'); // parentdirname
define("install_msg_db_selected",   'Database "%s" selected.'); // databasename
define("install_msg_table_count_skip",   'Creation of %s tables skipped (requested by user if table already exists).'); // skipcount
define("install_msg_table_count_drop",   '%s tables dropped (requested by user if table already exists).'); // dropcount
define("install_msg_table_count_create", '%s tables created.'); // createcount
define("install_msg_table_count_load_skip", '%s tables skipped (not created by current configuration process.'); // skipcount
define("install_msg_table_count_load_succeed", 'Content of %s tables loaded.'); // loadcount

define("db_driver_label", 'Database');

// step form
define("install_msg_check_phpext_req",      'Checking for required PHP extensions...');
define("install_msg_check_phpext_opt",      'Checking for optional PHP extensions...');
define("install_err_check_phpext_miss",     'Extension %s missed!'); // extname, extinfo
define("install_msg_check_phpext_has_req",  'All required extensions (%s) are installed.'); // extnamelist
define("install_msg_check_phpext_has_opt",  'All optional extensions (%s) are installed.'); // extnamelist
define("install_msg_check_apachemod_req",      'Checking for required Apache modules...');
define("install_msg_check_apachemod_opt",      'Checking for optional Apache modules...');
define("install_err_check_apachemod_miss",     'Apache module %s missed! %s'); // modname, modinfo
define("install_msg_check_apachemod_has_req",  'All required modules (%s) are installed.'); // modnamelist
define("install_msg_check_apachemod_has_opt",  'All optional modules %s are installed.'); // modnamelist

define("install_msg_admin_set", 'Set name, password and email address for admin account.');
define("install_msg_all_done",  'Please delete directory <b>%s</b> and navigate to <a href="%s">start page</a>');

define('module_config', 'Module config');

define('install_msg_directory_template_copied', 'Directory template %s successfully copied.');
define('install_msg_cannot_open_path','Cannot open path "%s".');
define('install_msg_dir_not_writeable','Folder "%s" is not writeable.');
define('install_msg_dir_nomk','Cannot create folder "%s".');

define('install_finish_button_text', 'Finish installation');


define('install_msg_save_props_homerep', 'Home repository properties successfully saved.');
define('install_msg_add_registry_repo', 'Home repository successfully added to registry.');

define('install_err_fetch_props_homerep', 'Error while fetching home repository properties.');
define('install_err_save_props_homerep', 'Could not save repository properties.');
define('install_err_add_registry_repo', 'Could not add home repository to registry.');
define('install_err_add_repotohomeconfig', 'Could not add home repository id to home config.');

define('install_config_success', 'Installation done.');
define('install_warning_ssl_keys', 'SSL keys could not be generated. Please insert theem manually into configurattion file.');