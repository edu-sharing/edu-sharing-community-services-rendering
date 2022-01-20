<?php
/*
* $McLicense$
*
* $Id$
*
*/


define("install_logo",  "_img/edulogo.png");
define("install_headline",  "edu-sharing Renderingservice Installation");
//define("install_intro", "Diese Version unterstützt PHP 5.x / MySQL 5.x.<br>");
define("install_submit",      "Senden");
define("install_save",      "Speichern");
define("install_continue",  "Weiter");
define("install_save_continue", "Speichern und weiter");
define("install_back",      "zurück");

define("install_label_01", "Server-Einstellungen");
define("install_label_03", "Renderingservice URL");
define("install_label_04", "Datenbank-Einstellungen");
define("install_label_05", "Host");
define("install_label_05a", "Port");
define("install_label_06", "Benutzer");
define("install_label_07", "Passwort");
define("install_label_08", "Name der Datenbank");
define("install_label_09", "Debug-Nachrichten während der Installation anzeigen<br>");
define("install_label_11", "Einstellungen");
define("install_label_12", "Select language / Sprache wählen");

define("install_label_14", "Bitte beachten Sie die Nutzungsbedingungen.<br>Sie dürfen das Produkt weder installieren noch benutzen, wenn Sie nicht zuvor die Nutzungsbedingungen akzeptiert haben.");
define("install_label_15", "Ja, ich habe die Nutzungsbedingungen gelesen und akzeptiert.");
define("install_label_16", "Administrator Login f&uuml;r Plattform");
define("install_label_17", "Name");
define("install_label_18", "Passwort");
define("install_label_19", "Passwortwiederholung");
define("install_label_20", "E-Mail-Adresse");
define("install_label_21", "Basedir");
define("install_label_22", "Heimrepositorium Einstellungen");
define("install_label_23", "Basis-URL");
define("install_label_24", "Datenverzeichnis");
define("install_label_25", "Name");
define("install_label_26", "Passwort");

define("install_err_terms_not_accepted",        "Sie haben die Nutzungsbedingungen nicht akzeptiert!<br>"
    ."Sie müssen zuerst die Nutzungsbedingungen akzeptieren, um die Software installieren und nutzen zu dürfen!");
define("install_err_content_dir_empty",     "Es wurde kein Verzeichnis für Inhalte angegeben.");
define("install_err_docroot_match",     'Das Inhalteverzeichnis "%s" liegt innerhalb des Document Root. Dies ist aus Sicherheitsgründen nicht gestattet!');
define("install_err_dir_access_denied",     "ZUGRIFFSFEHLER!<p>Kein Schreibzugriff auf Verzeichnis '%s'.<p>"
  ."Bitte ändern Sie die Zugriffsrechte so, dass PHP Schreibzugriff auf das Verzeichnis hat.");
define("install_err_dir_not_found",     "ZUGRIFFSFEHLER!<p>Das Verzeichnis '%s' wurde nicht gefunden.<p>"
  ."Bitte legen sie das Verzeichnis '%s' an oder wählen sie den Namen eines existierenden Verzeichnisses.");
define("install_err_db_access_denied",      "Es konnte keine Verbindung zum Datenbankserver aufgebaut werden. "
    ."Bitte überprüfen Sie die Verbindungsparameter.");
define("install_err_admin_name_empty", "Sie haben keinen Loginnamen für den Administrator-Account angegeben. ");
define("install_err_admin_pass_empty", "Sie haben kein Passwort für den Administrator-Account angegeben. ");
define("install_err_admin_pass_not_confirmed", "Die Passwort und Passwortwiederholung stimmten nicht überein! ");
define("install_err_admin_mail_empty", "Es wurde keine Emailadresse angegeben! ");

define("install_msg_sql_safe_mode",     'SQL-Safemode ist aktiv (<a href="http://de.php.net/manual/en/ini.core.php#ini.sql.safe-mode" target="_blank" style="color:blue;">-&gt;Informationen</a>) !<br>'
    .'Der Server verwendet folgende Zugangsdaten :');

define("install_step",          "Schritt");
define("install_debug_open_file",   "Öffne Datei");
define("install_debug_scan_file",   "Durchsuchen der Datei");
define("install_writing",       "Dateiinhalt schreiben");
define("install_load",          "Laden von");
define("install_create",        "Anlegen von");
define("install_make_db_backup",    "Backup: Sicherungskopie der Datenbank %s in Datei '%s' ausführen ...");
define("intall_err_no_localhost",   "Automatisches Einspielen der Datenbank auf einen externen mysql-Server wird nicht unterstützt (findet nur auf localhost statt).<br>"
  ."Bitte kopieren sie die modifizierte Datenbank-Datei ('%s') auf den externen mysql-Server und spielen sie die Datei manuell auf den Server auf "
  ."(mit 'mysql datenbankname < dateiname').");

define("install_scanning",      "durchsuche");
define("install_write",         "schreibe");
define("install_of",            "von");

define("install_not_updated",       "nicht geändert");
define("install_updated",       "geändert");
define("install_skipped",       "übersprungen");
define("install_found",         "gefunden");
define("install_success",       "erfolgreich");
define("install_saved",         "gespeichert");
define("install_done",          "fertig");
define("install_finished",      "abgeschlossen");
define("install_failed",        "fehlgeschlagen");

define("install_replacement",       "Platzhalter-Ersetzungen in Dateien");
define("install_file",          "Datei");
define("install_files_scanned",     "Dateien durchsucht");
define("install_files_updated",     "Dateien geändert");
define("install_err_no_file_content",   "Datei '%s' hat keinen Inhalt oder ist nicht lesbar.");
define("install_err_no_access",     "Keine Schreibrechte f&uuml;r Verzeichnis '%s' !");
define("install_err_creating_file", "Anlegen von Datei '%s' fehlgeschlagen.");
define("install_err_writing_file",  "Schreiben in Datei '%s' fehlgeschlagen.");

define("install_all_replaced",      "All Platzhalter wurden ersetzt");
define("install_conf_saved",        "Speichern der Konfigurationsdatei");
define("install_scan_all",      "Alle Dateien nach Platzhaltern durchsuchen");
define("install_open_db",       "Datenbank-Dump wird geöffnet");
define("install_err_open_db",       "Öffnen des Datenbank Dumps ('%s') fehlgeschlagen.");
define("install_open_tmp",      "Temporäre Datenbankkopie anlegen");
define("install_err_open_tmp",      "Anlegen/Öffnen der temporÃ¤ren Datenbankkopie ('%s') mit Schreibzugriff (+w) ist fehlgeschlagen.");
define("install_write_dump",        "Datenbank-Dump nach Platzhaltern durchsuchen, Ersetzungen vornehmen und in temporäre Datei schreiben");
define("install_err_write_dump",    "Schreiben in die temporäre Datenbankkopie ('%s') fehlgeschlagen.");
define("install_write_pre",         "Fehler: Dump-Präfix konnte nicht geschrieben werden");
define("install_write_line",        "Fehler beim Schreiben von Dump-Zeile ");
define("install_write_suf",         "Fehler: Dump-Suffix konnte nicht geschrieben werden");
define("install_load_dump_prepare", "Einspielen der Datenbank vorbereitet ...<br>");
define("install_load_dump",         "Datenbank-Dump in mysql Datenbank einspielen");
define("install_load_dump_err",     "Fehler beim Einspielen des Datenbank-Dumps");
define("install_dump_loaded",       "Datenbank-Dump eingespielt und gepatcht (COURSE_ID auf \"0\" gesetzt)");

define("install_err_msg",       "<p>Fehler in <strong>Datei '%s', Zeile %s</strong><br>%s<br>Error: %s<hr>");
define("install_some_errors_msg",   "Es traten einer oder mehrere Fehler während der Installation auf.");
define("install_complete",      "Installation abgeschlossen (Es traten keine Fehler auf).");
define("install_some_errors",       "Während der Installation sind Fehler aufgetreten.");
define("install_check_log",         "Bitte überprüfen Sie obenstehendes Installations-Log auf nähere Fehlerinformationen.");
define("installation_canceled",     "Es ist ein Fehler aufgetreten, die Installation wurde abgebrochen.");

define("install_finish",        "Benennen Sie das Verzeichnis &quot;%s&quot; um.<br>"
  ."Loggen Sie sich dann unter &quot;%s&quot; als Administrator ein (Name/Passwort: admin/admin). Glück auf! ;-)");


// step initdb
define("initdb_missing_form_data",      'Es wurden keine Formulardaten empfangen!');
define("initdb_database_exists",          'Datenbank "%s" existiert bereits.');
define("initdb_database_created",       'Datenbank "%s" wurde angelegt.');
define("initdb_database_failed",          'Anlegen der Datenbank "%s" schlug fehl!');
//define("initdb_label_max_filesize",   'Maximal erlaubte Gr&ouml;&szlig;e f&uuml;r Dateiupload');
define("install_msg_filedir_exists",   'Verzeichnis %s existiert bereits und ist beschreibbar.');
define("install_err_filedir_exist_fail",   'Verzeichnis %s existiert bereits, ist aber NICHT beschreibbar!'); // filedirname
define("install_msg_filedir_need_write",   'Sie m&uuml;ssen PHP die Schreibberechtigung f&uuml;r das Verzeichnis erteilen!');
define("install_msg_filedir_created",      'Verzeichnis %s wurde angelegt.'); // filedirname
define("install_err_filedir_create_fail",  'Das Anlegen von Verzeichnis %s ist fehlgeschlagen!'); // filedirname
define("install_err_filedir_parent_fail",   'Verzeichnis %s ist NICHT beschreibbar!'); // parentdirname
define("install_msg_filedir_parent_fail",   'Sie m&uuml;ssen entweder das Verzeichnis "%s" selbst anlegen oder PHP Schreibzugriff f&uuml;r das dar&uuml;berliegende Verzeichnis "%s" erteilen.'); // parentdirname
define("install_msg_db_selected",   'Datenbank "%s" wurde ausgew&auml;hlt.'); // databasename
define("install_msg_table_count_skip",   '%s Tabellen wurden nicht angelegt, da sie schon existierten  (Benutzereinstellung).'); // skipcount
define("install_msg_table_count_drop",   '%s Tabellen wurden gel&ouml;scht, da sie schon existierten  (Benutzereinstellung).'); // dropcount
define("install_msg_table_count_create", '%s Tabellen wurden angelegt.'); // createcount
define("install_msg_table_count_load_skip", '%s Tabellen wurden &uuml;bersprungen.'); // skipcount
define("install_msg_table_count_load_succeed", 'Der Inhalt von %s Tabellen wurde geladen.'); // loadcount
define("db_driver_label", 'Datenbank');


// step form
define("install_msg_check_phpext_req",      'Suche nach erforderlichen PHP-Erweiterungen...');
define("install_msg_check_phpext_opt",      'Suche nach optionalen PHP-Erweiterungen...');
define("install_err_check_phpext_miss",     'Erweiterung %s fehlt!'); // extname, extinfo
define("install_msg_check_phpext_has_req",  'Alle erforderlichen Erweiterungen (%s) sind installiert.'); // extnamelist
define("install_msg_check_phpext_has_opt",  'Alle optionalen Erweiterungen (%s) sind installiert.'); // extnamelist
define("install_msg_check_apachemod_req",      'Suche nach erforderlichen Apache-Modulen...');
define("install_msg_check_apachemod_opt",      'Suche nach optionalen Apache-Modulen...');
define("install_err_check_apachemod_miss",     'Apache-Modul %s fehlt! %s'); // modname, modinfo
define("install_msg_check_apachemod_has_req",  'Alle erforderlichen Module (%s) sind installiert.'); // modnamelist
define("install_msg_check_apachemod_has_opt",  'Alle optionalen Module %s sind installiert.'); // modnamelist

// step execute
define("install_msg_admin_set", 'Name, Passwort und E-Mail-Adresse f&uuml;r den Admin-Account wurde gesetzt.');
define("install_msg_all_done",  'Bitte l&ouml;schen Sie das Verzeichnis <b>%s</b> und folgen Sie dann dem <a href="%s">Link zur Startseite</a>');

define('module_config', 'Modul-Einstellungen');

define('install_msg_directory_template_copied', 'Ordner %s erfolgreich kopiert.');
define('install_msg_cannot_open_path','Pfad "%s" kann nicht geöffnet werden.');
define('install_msg_dir_not_writeable','Im Ordner "%s" kann nicht geschrieben werden.');
define('install_msg_dir_nomk','Ordner "%s" kann nicht erstellt werden.');

define('install_finish_button_text', 'Installation abschließen');

define('install_msg_save_props_homerep', 'Heimrepositorium erfolgreich gespeichert.');
define('install_msg_add_registry_repo', 'Heimrepositorium erfolgreich registriert.');

define('install_err_fetch_props_homerep', 'Heimrepositorium konnte nicht geladen werden.');
define('install_err_save_props_homerep', 'Heimrepositorium konnte nicht gespeichert werden.');
define('install_err_add_registry_repo', 'Heimrepositorium konnte nicht registriert werden.');
define('install_err_add_repotohomeconfig', 'Heimrepositorium konnte nicht in die Konfiguration übernommen werden.');

define('install_config_success', 'Installation abgeschlossen.');
define('install_warning_ssl_keys', 'SSL-Schlüssel konnten nicht erzeugt werden. Bitte fügen Sie diese manuell in die Konfigurationsdatei ein.');