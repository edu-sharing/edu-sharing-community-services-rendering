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
    ->addTranslation('fr', 'Error', 'Erreur')
	->addTranslation('fr', 'Missing parameter ":name".', 'Le paramètre ":name" est manquant.')
	->addTranslation('fr', 'Invalid parameter ":name".', 'Le paramètre ":name" est non valide.')
	->addTranslation('fr', 'Error loading configuration.', 'Il y avait une erreur de charger le fichier de configuration.')
	->addTranslation('fr', 'Error loading config for application ":app_id".', 'Il y avait une erreur de charger le fichier de configuration pour l`application ":app_id".')
	->addTranslation('fr', 'A network error occured.', 'Il y avait une erreur réseau.')
	->addTranslation('fr', 'You\'re not authorized to access this resource.', 'Vous n`êtes pas autorisé.')
	->addTranslation('fr', 'An internal server error occurred.', 'Une erreur interne est survenue.')
	->addTranslation('fr', 'The requested version of ":title" is corrupt or missing.', 'La version demandée de ":title" est endommagée ou manquante.')
    ->addTranslation('fr', 'The object to which this collection object refers is no longer present.', 'L`objet auquel cet objet de collection se réfère n`est plus présent.')
	->addTranslation('fr', 'authored_by', 'de')
	->addTranslation('fr', 'author', 'Auteur')
    ->addTranslation('fr', 'Resource is being converted for your view ...', 'La ressource sera convertie pour votre vue ...')
    ->addTranslation('fr', 'Loading player ...', 'Chargement lecteur ...')
    ->addTranslation('fr', 'No usage-information retrieved.', 'La ressource est indisponible.')
    ->addTranslation('fr', 'back', 'précédent')
    ->addTranslation('fr', 'print', 'imprimer')
    ->addTranslation('fr', 'saveToDisk', 'sauver')
    ->addTranslation('fr', 'Chapter', 'Section')
    ->addTranslation('fr', 'Object does not exist in repository', 'L`objet demandé n`a pas été trouvé.')
    ->addTranslation('fr', 'Error fetching object properties', 'Il y avait une erreur de charger les caractéristiques de l`objet')
    ->addTranslation('fr', 'Resource is waiting for conversion.', 'Ressource est prêt pour la conversion.')
    ->addTranslation('fr', 'Postition in queue', 'Position dans la file d`attente')
    ->addTranslation('fr', 'Video player cannot play back this video.', 'Le lecteur vidéo ne peut pas lire la vidéo.')
    ->addTranslation('fr', 'Fit image size to browser window (esc)', 'redimensionner l`image pour fenêtre de navigateur (esc)')
    ->addTranslation('fr', 'Show image in original size', 'Voir l`images dans sa taille originale')
    ->addTranslation('fr', 'published under a', 'publié sous')
    ->addTranslation('fr', 'custom license', 'licence personnalisée')
    ->addTranslation('fr', 'Token expired', 'Security Token expiré')
    ->addTranslation('fr', 'title', 'Titre')
    ->addTranslation('fr', 'showInformation', 'Montrer information')
    ->addTranslation('fr', 'hideInformation', 'Cacher information')
	->addTranslation('fr', 'cannotOpenObject', 'Ce matériel ne peut pas être affiché dans le navigateur.')
	->addTranslation('fr', 'cannotOpenObjectText', 'Pour utiliser le matériel, veuillez le télécharger.')
	->addTranslation('fr', 'goToOrigin', 'Aller à l`origine');