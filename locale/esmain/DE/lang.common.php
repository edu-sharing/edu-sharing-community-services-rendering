<?php
// CC_APP
			define('comment',  'Kommentar');
			define('trustedclient',  'vertrauenswürdig [true|false]');
			define('type',  'Typ [REPOSITORY|SERVICE|LMS]');
			define('authenticationwebservice',  'Auth.WS');
			define('host',  'IP-Adresse');
			define('port',  'Port');
			define('wspath',  'rel. path');
			define('alfrescocontext',  'Alfresco Context');
			define('searchclass',  'SuchKlasse');
			define('contenturl',  'RS Content Url');
			define('previewurl',  'RS Vorschau Url');
			define('nodeid_key',  'Node ID Key');
			define('is_home_node',  'Home Knoten[true|false]');
			define('appcaption',  'Bezeichnung');
			define('appid',  'Applikations ID');
			define('username',  'ES-Admin Benutzername');
			define('password',  'ES-Passwort');
			define('authenticationwebservice_wsdl',  'wsdl');
			define('ccusagewebservice',  'usage service url');

global $Translate;

$Translate
    ->addTranslation('de', 'Error', 'Fehler')
	->addTranslation('de', 'Missing parameter ":name".', 'Der Parameter ":name" fehlt.')
	->addTranslation('de', 'Invalid parameter ":name".', 'Der Parameter ":name" ist fehlerhaft.')
	->addTranslation('de', 'Error loading configuration.', 'Fehler beim Laden der Konfigurationsdatei.')
	->addTranslation('de', 'Error loading config for application ":app_id".', 'Fehler beim Laden der Konfiguration für Applikation ":app_id".')
	->addTranslation('de', 'A network error occured.', 'Ein Netzwerkfehler ist aufgetreten.')
	->addTranslation('de', 'You\'re not authorized to access this resource.', 'Sie sind nicht authorisiert.')
	->addTranslation('de', 'An internal server error occurred.', 'Ein interner Fehler ist aufgetreten.')

	->addTranslation('de', 'authored_by', 'von')
	->addTranslation('de', 'author', 'Author')
    ->addTranslation('de', 'Resource is being converted for your view ...', 'Die Ressource wird für Ihre Ansicht konvertiert ...')
    ->addTranslation('de', 'Loading player ...', 'Player wird geladen ...')
    ->addTranslation('de', 'No usage-information retrieved.', 'Die Ressource ist nicht verfügbar.')
    ->addTranslation('de', 'back', 'Zurück')
    ->addTranslation('de', 'print', 'Drucken')
    ->addTranslation('de', 'saveToDisk', 'Sichern')
    ->addTranslation('de', 'Chapter', 'Kapitel')
    ->addTranslation('de', 'Object does not exist in repository', 'Das angeforderte Objekt konnte nicht gefunden werden.')
    ->addTranslation('de', 'Error fetching object properties', 'Fehler beim Laden der Objekteigenschaften')
    ->addTranslation('de', 'Resource is waiting for conversion.', 'Ressource wartet auf Konvertierung.')
    ->addTranslation('de', 'Postition in queue', 'Position in Warteschlange')
    ->addTranslation('de', 'Video player cannot play back this video.', 'Der Videoplayer kann dieses Video nicht wiedergeben.')
    ->addTranslation('de', 'Fit image size to browser window (esc)', 'Bildgröße an Browserfenster anpassen (esc)')
    ->addTranslation('de', 'Show image in original size', 'Bild in Originalgröße anzeigen')
    ->addTranslation('de', 'published under a', 'veröffentlicht unter einer')
    ->addTranslation('de', 'custom license', 'eigenen Lizenz')
    ->addTranslation('de', 'Token expired', 'Sicherheitstoken abgelaufen')
	->addTranslation('de', 'title', 'Titel')
	->addTranslation('de', 'showMetadata', 'Metadaten einblenden')
	->addTranslation('de', 'hideMetadata', 'Metadaten ausblenden')
	->addTranslation('de', 'showInformation', 'Informationen einblenden')
	->addTranslation('de', 'hideInformation', 'Informationen ausblenden')
	->addTranslation('de', 'toDownload', 'Herunterladen')
	->addTranslation('de', 'cannotOpenObject', 'Öffnen dieses Materials im Browser nicht möglich.')
	->addTranslation('de', 'cannotOpenObjectText', 'Laden Sie das Material herunter, um es zu benutzen.')
	->addTranslation('de', 'goToOrigin', 'Zur Originalseite springen')
	->addTranslation('de', 'meta_general', 'Basis-Informationen')
	->addTranslation('de', 'meta_contributors', 'Beteiligte')
	->addTranslation('de', 'meta_didactics', 'Didaktik');
