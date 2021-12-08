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
	->addTranslation('de', 'The requested version of ":title" is corrupt or missing.', 'Die angeforderte Version von ":title" ist beschädigt oder nicht vorhanden.')
    ->addTranslation('de', 'The object to which this collection object refers is no longer present.', 'Das Objekt, auf das sich dieses Sammlungsobjekt bezieht, ist nicht mehr vorhanden.')
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
	->addTranslation('de', 'toDownload', 'Herunterladen')
	->addTranslation('de', 'cannotOpenObject', 'Öffnen dieses Materials im Browser nicht möglich.')
	->addTranslation('de', 'cannotOpenObjectText', 'Laden Sie das Material herunter, um es zu benutzen.')
	->addTranslation('de', 'goToOrigin', 'Zur Originalseite springen')
    ->addTranslation('de', 'showDocument', 'Dokument anzeigen')
    ->addTranslation('de', 'showInLearningAppsOrg', 'In learningapps.org öffnen')
    ->addTranslation('de', 'hasNoContentLicense', 'Sie dürfen den Inhalt aufgrund von Lizenzbeschränkungen nicht verwenden.')
    ->addTranslation('de', 'startScorm', 'SCORM starten')
    ->addTranslation('de', 'Omega plugin error', 'Fehler im Omega Plugin')
    ->addTranslation('de', 'API respsonse is empty', 'Antwort der API ist leer')
    ->addTranslation('de', 'Wrong identifier', 'Antwort der API enthält falschen Identifier')
    ->addTranslation('de', 'urls empty', 'Antwort der API enthält leere streamURL und leere downloadURL')
    ->addTranslation('de', 'Property replicationsourceid is empty', 'Die Eigenschaft replicationsourceid des Objekts ist leer')
    ->addTranslation('de', 'given streamURL is invalid', 'streamURL ist ungültig - HTTP Status')
    ->addTranslation('de', 'jumpToDataProvider :dataProvider', 'Objekt beim Datengeber (:dataProvider) anzeigen')
    ->addTranslation('de', 'ltiGotoProvider', 'Direkt zum Provider gehen')
    ->addTranslation('de', 'dataProtectionRegulations1 :providerName', 'Ja, Inhalte von :providerName anzeigen')
    ->addTranslation('de', 'dataProtectionRegulations2 :providerName', 'Sie sind dabei Inhalte von :providerName zu laden und anzuzeigen. Dabei werden unter Umständen persönliche Daten an :providerName übermittelt und dort verarbeitet.')
    ->addTranslation('de', 'dataProtectionRegulations1default', 'Ja, Inhalte von externer Quelle anzeigen')
    ->addTranslation('de', 'dataProtectionRegulations2default', 'Sie sind dabei Inhalte von einer externen Quelle zu laden und anzuzeigen. Dabei werden unter Umständen persönliche Daten übermittelt und dort verarbeitet.')
    ->addTranslation('de', 'dataProtectionRegulationsHintDefault', 'Weitere Informationen finden Sie in den Datenschutzhinweisen des Anbieters.')
    ->addTranslation('de', 'dataProtectionRegulations3', 'Weitere Informationen finden Sie hier:')
    ->addTranslation('de', 'dataProtectionRegulations4', 'Zustimmen und fortfahren')
    ->addTranslation('de', 'dataProtectionRegulations', 'Datenschutzbestimmungen')
    ->addTranslation('de', 'abort', 'Abbrechen')
    ->addTranslation('de', 'of', 'von')
    ->addTranslation('de', 'h5p_ie_hint', 'Bei Darstellungsproblemen nutzen Sie bitte einen aktuellen Browser.')
    ->addTranslation('de', 'seqenceChildren :count', ':count weitere Materialien gehören dazu')
    ->addTranslation('de', 'seqenceViewChildren', 'Alle ansehen und herunterladen')
    ->addTranslation('de', 'directoryOpen', 'Ordner öffnen und herunterladen')
    ->addTranslation('de', 'createdBy', 'Erstellt von')
    ->addTranslation('de', 'goToCourse', 'Zum Kurs springen')
    ->addTranslation('de', 'inConversionQueue', 'Format wird konvertiert.')
    ->addTranslation('de', 'ViewerJS_Actual Size', 'Tatsächliche Größe')
    ->addTranslation('de', 'ViewerJS_Automatic', 'Automatisch')
    ->addTranslation('de', 'ViewerJS_Full Width', 'Volle Breite')
    ->addTranslation('de', 'ViewerJS_Next Page', 'Nächste Seite')
    ->addTranslation('de', 'ViewerJS_Previous Page', 'Vorherige Seite')
    ->addTranslation('de', 'ViewerJS_Zoom In', 'Hineinzoomen')
    ->addTranslation('de', 'ViewerJS_Zoom Out', 'Herauszoomen')
    ->addTranslation('de', 'imageDescriptionNotAvailable', 'Eine Beschreibung des Bildinhaltes ist leider nicht verfügbar.');

