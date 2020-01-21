<?php
require_once(dirname(__FILE__).'/config.php');

class H5PFramework implements H5PFrameworkInterface {



    private $messages = array('error' => array(), 'info' => array());
    public $id;


    /**
     * Returns info for the current platform
     *
     * @return array
     *   An associative array containing:
     *   - name: The name of the platform, for instance "Wordpress"
     *   - version: The version of the platform, for instance "4.0"
     *   - h5pVersion: The version of the H5P plugin/module
     */
    public function getPlatformInfo()
    {
        return array('name' => 'edu-sharing', 'version' => '1.0', 'h5pVersion' => H5P_Version);
    }


    public function get_h5p_path() {
        global $CC_RENDER_PATH;
        return $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p';
    }

    public function get_h5p_url() {
        global $MC_URL;
        return $MC_URL . '/modules/cache/h5p';
    }

    /**
     * Convert datetime string to unix timestamp
     *
     * @param string $datetime
     * @return int unix timestamp
     */
    public static function dateTimeToTime($datetime) {
        $dt = new \DateTime($datetime);
        return $dt->getTimestamp();
    }

    /**
     * Fetches a file from a remote server using HTTP GET
     *
     * @param string $url Where you want to get or send data.
     * @param array $data Data to post to the URL.
     * @param bool $blocking Set to 'FALSE' to instantly time out (fire and forget).
     * @param string $stream Path to where the file should be saved.
     * @return string The content (response body). NULL if something went wrong
     */
    public function fetchExternalData($url, $data = NULL, $blocking = TRUE, $stream = NULL)
    {
        @set_time_limit(0);
        if ($data !== NULL) {
            // Post
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $response['body'] = file_get_contents($url, false, $context);
        }
        else {
            $response['body'] = file_get_contents($url, false);
            file_put_contents($stream, $response['body']);
        }
        return empty($response['body']) ? NULL : $response['body'];
    }

    /**
     * Load config for libraries
     *
     * @param array $libraries
     * @return array
     */
    public function getLibraryConfig($libraries = NULL) {
        return [];
    }

    /**
     * Load addon libraries
     *
     * @return array
     */
    public function loadAddons() {
        return [];
    }

    /**
     * Set the tutorial URL for a library. All versions of the library is set
     *
     * @param string $machineName
     * @param string $tutorialUrl
     */
    public function setLibraryTutorialUrl($machineName, $tutorialUrl)
    {
        return '';
    }

    /**
     * Show the user an error message
     *
     * @param string $message The error message
     * @param string $code An optional code
     */
    public function setErrorMessage($message, $code = NULL)
    {
        $this->messages['error'][] = (object)array(
            'code' => $code,
            'message' => $message
        );
    }

    /**
     * Show the user an information message
     *
     * @param string $message
     *  The error message
     */
    public function setInfoMessage($message)
    {
        $this->messages['info'][] = $message;
    }

    /**
     * Return messages
     *
     * @param string $type 'info' or 'error'
     * @return string[]
     */
    public function getMessages($type)
    {
        if (empty($this->messages[$type])) {
            return NULL;
        }
        $messages = $this->messages[$type];
        $this->messages[$type] = array();
        return $messages;
    }

    /**
     * Translation function
     *
     * @param string $message
     *  The english string to be translated.
     * @param array $replacements
     *   An associative array of replacements to make after translation. Incidences
     *   of any key in this array are replaced with the corresponding value. Based
     *   on the first character of the key, the value is escaped and/or themed:
     *    - !variable: inserted as is
     *    - @variable: escape plain text to HTML
     *    - %variable: escape text and theme as a placeholder for user-submitted
     *      content
     * @return string Translated string
     * Translated string
     */
    public function t($message, $replacements = array())
    {
        $message = preg_replace('/(!|@|%)[a-z0-9-]+/i', '%s', $message);
        return vsprintf($message, $replacements);
    }

    /**
     * Get URL to file in the specific library
     * @param string $libraryFolderName
     * @param string $fileName
     * @return string URL to file
     */
    public function getLibraryFileUrl($libraryFolderName, $fileName)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $libraryFolderName . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * Get the Path to the last uploaded h5p
     *
     * @return string
     *   Path to the folder where the last uploaded h5p for this session is located.
     */
    public function getUploadedH5pFolderPath() {
        return $this->uploadedH5pFolderPath;
    }

    /**
     * Get the path to the last uploaded h5p file
     *
     * @return string
     *   Path to the last uploaded h5p
     */
    public function getUploadedH5pPath() {
        return $this->uploadedH5pPath;
    }


    /**
     * Get a list of the current installed libraries
     *
     * @return array
     *   Associative array containing one entry per machine name.
     *   For each machineName there is a list of libraries(with different versions)
     */
    public function loadLibraries()
    {
        global $db;

        $query = "SELECT id, name, title, major_version, minor_version, patch_version, runnable, restricted
          FROM h5p_libraries
          ORDER BY title ASC, major_version ASC, minor_version ASC";

        $statement = $db -> query($query);

        $results = $statement->fetchAll(\PDO::FETCH_OBJ);

        $libraries = array();
        if(empty($results))
            return $libraries;

        foreach ($results as $library) {
            $libraries[$library->name][] = $library;
        }

        return $libraries;
    }

    /**
     * Returns the URL to the library admin page
     *
     * @return string
     *   URL to admin page
     */
    public function getAdminUrl()
    {
        return '';
    }

    /**
     * Get id to an existing library.
     * If version number is not specified, the newest version will be returned.
     *
     * @param string $machineName
     *   The librarys machine name
     * @param int $majorVersion
     *   Optional major version number for library
     * @param int $minorVersion
     *   Optional minor version number for library
     * @return int
     *   The id of the specified library or FALSE
     */
    public function getLibraryId($machineName, $majorVersion = NULL, $minorVersion = NULL)
    {
        global $db;

        // Look for specific library
        $sql_where = 'WHERE name = \''. $machineName . '\'';

        if ($majorVersion !== NULL) {
            // Look for major version
            $sql_where .= ' AND major_version = ' . $majorVersion;
            if ($minorVersion !== NULL) {
                // Look for minor version
                $sql_where .= ' AND minor_version = ' . $minorVersion;
            }
        }

        // Get the lastest version which matches the input parameters
        $statement = $db -> query('SELECT id FROM h5p_libraries ' . $sql_where . ' ORDER BY major_version DESC, minor_version DESC, patch_version DESC LIMIT 1');
        $row= $statement->fetch();
        return $row['id'] === NULL ? FALSE : $row['id'];
    }

    /**
     * Get file extension whitelist
     *
     * The default extension list is part of h5p, but admins should be allowed to modify it
     *
     * @param boolean $isLibrary
     *   TRUE if this is the whitelist for a library. FALSE if it is the whitelist
     *   for the content folder we are getting
     * @param string $defaultContentWhitelist
     *   A string of file extensions separated by whitespace
     * @param string $defaultLibraryWhitelist
     *   A string of file extensions separated by whitespace
     */
    public function getWhitelist($isLibrary, $defaultContentWhitelist, $defaultLibraryWhitelist)
    {
        return '';
    }

    /**
     * Is the library a patched version of an existing library?
     *
     * @param object $library
     *   An associative array containing:
     *   - machineName: The library machineName
     *   - majorVersion: The librarys majorVersion
     *   - minorVersion: The librarys minorVersion
     *   - patchVersion: The librarys patchVersion
     * @return boolean
     *   TRUE if the library is a patched version of an existing library
     *   FALSE otherwise
     */
    public function isPatchedLibrary($library)
    {
        global $db;

        $query = 'SELECT id 
          FROM h5p_libraries
          WHERE name = \'' . $library['machineName'] . '\'
          AND major_version = '. $library['majorVersion'] .'
          AND minor_version = '. $library['minorVersion'] .'
          AND patch_version < ' . $library['patchVersion'] ;

        $statement = $db -> query($query);
        return $statement->fetch() !== FALSE;
    }

    /**
     * Is H5P in development mode?
     *
     * @return boolean
     *  TRUE if H5P development mode is active
     *  FALSE otherwise
     */
    public function isInDevMode()
    {
        // TODO: Implement isInDevMode() method.
        return false;
    }

    /**
     * Is the current user allowed to update libraries?
     *
     * @return boolean
     *  TRUE if the user is allowed to update libraries
     *  FALSE if the user is not allowed to update libraries
     */
    public function mayUpdateLibraries()
    {
        return true;
    }

    /**
     * Store data about a library
     *
     * Also fills in the libraryId in the libraryData object if the object is new
     *
     * @param object $libraryData
     *   Associative array containing:
     *   - libraryId: The id of the library if it is an existing library.
     *   - title: The library's name
     *   - machineName: The library machineName
     *   - majorVersion: The library's majorVersion
     *   - minorVersion: The library's minorVersion
     *   - patchVersion: The library's patchVersion
     *   - runnable: 1 if the library is a content type, 0 otherwise
     *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
     *   - embedTypes(optional): list of supported embed types
     *   - preloadedJs(optional): list of associative arrays containing:
     *     - path: path to a js file relative to the library root folder
     *   - preloadedCss(optional): list of associative arrays containing:
     *     - path: path to css file relative to the library root folder
     *   - dropLibraryCss(optional): list of associative arrays containing:
     *     - machineName: machine name for the librarys that are to drop their css
     *   - semantics(optional): Json describing the content structure for the library
     *   - language(optional): associative array containing:
     *     - languageCode: Translation in json format
     * @param bool $new
     * @return
     */
    public function saveLibraryData(&$library, $new = TRUE)
    {
        global $db;

        $preloadedJs = $this->pathsToCsv($library, 'preloadedJs');
        $preloadedCss =  $this->pathsToCsv($library, 'preloadedCss');
        $dropLibraryCss = '';

        if (isset($library['dropLibraryCss'])) {
            $libs = array();
            foreach ($library['dropLibraryCss'] as $lib) {
                $libs[] = $lib['machineName'];
            }
            $dropLibraryCss = implode(', ', $libs);
        }

        $embedTypes = '';
        if (isset($library['embedTypes'])) {
            $embedTypes = implode(', ', $library['embedTypes']);
        }
        if (!isset($library['semantics'])) {
            $library['semantics'] = '';
        }
        if (!isset($library['fullscreen'])) {
            $library['fullscreen'] = 0;
        }
        if (!isset($library['hasIcon'])) {
            $library['hasIcon'] = 0;
        }
        $library['hasIcon'] ? $hasIcon = 1 : $hasIcon = 0;
        if ($new) {

            $db->query('INSERT INTO h5p_libraries '.
                '(name,title,major_version,minor_version,patch_version,runnable,fullscreen,embed_types,preloaded_js,'.
                    'preloaded_css,drop_library_css,semantics,tutorial_url,has_icon) '.
                'values ('. $db->quote($library['machineName']) .','
                .$db->quote($library['title']).','
                .$library['majorVersion'].','.$library['minorVersion'].','.$library['patchVersion'].','.$library['runnable'].','
                .$library['fullscreen'].','
                .$db->quote($embedTypes).','
                .$db->quote($preloadedJs).','
                .$db->quote($preloadedCss).','
                .$db->quote($dropLibraryCss).','
                .$db->quote($library['semantics']).','
                .$db->quote($library['tutorial_url']).','
                .$hasIcon .')');
            $library['libraryId'] = $db->lastInsertId();

        } else {

            $db->query('UPDATE h5p_libraries SET '.
                    'title = '. $db->quote($library['title']) .','.
                    'patch_version = '.$library['patchVersion'].','.
                'runnable = '.$library['runnable'].','.
                'fullscreen = '.$library['fullscreen'].','.
                'embed_types = '. $db->quote($embedTypes).','.
                'preloaded_js = '.$db->quote($preloadedJs).','.
                'preloaded_css= '. $db->quote($preloadedCss).','.
                'drop_library_css = '. $db->quote($dropLibraryCss).','.
                'semantics = ' . $db->quote($library['semantics']).','.
                'has_icon= '. $hasIcon.
                ' WHERE id = ' . $library['libraryId']);
            $this->deleteLibraryDependencies($library['libraryId']);
        }


        // Update languages
        $db->query('DELETE FROM h5p_libraries_languages WHERE library_id = ' . $library['libraryId']);

        if (isset($library['language'])) {
            foreach ($library['language'] as $languageCode => $translation) {
               $db -> query('INSERT INTO h5p_libraries_languages (library_id,language_code,translation)'.
                   'values('.$library['libraryId'].','.$db->quote($languageCode).','.$db->quote($translation).')');
            }
        }
    }


    private function pathsToCsv($library, $key) {
        if (isset($library[$key])) {
            $paths = array();
            foreach ($library[$key] as $file) {
                $paths[] = $file['path'];
            }
            return implode(', ', $paths);
        }
        return '';
    }

    /**
     * Insert new content.
     *
     * @param array $content
     *   An associative array containing:
     *   - id: The content id
     *   - params: The content in json format
     *   - library: An associative array containing:
     *     - libraryId: The id of the main library for this content
     * @param int $contentMainId
     *   Main id for the content if this is a system that supports versions
     */
    public function insertContent($content, $contentMainId = NULL)
    {
        return $this->updateContent($content);//as wordpress does
    }

    /**
     * Update old content.
     *
     * @param array $content
     *   An associative array containing:
     *   - id: The content id
     *   - params: The content in json format
     *   - library: An associative array containing:
     *     - libraryId: The id of the main library for this content
     * @param int $contentMainId
     *   Main id for the content if this is a system that supports versions
     */
    public function updateContent($content, $contentMainId = NULL)
    {
        global $db;

        if (!isset($content['id'])) {
            $db -> query('INSERT INTO h5p_contents (updated_at,title,parameters,embed_type,library_id,user_id,slug,filtered,disable)'.
                'values ('.time().','.$db->quote($content['title']).','.$db->quote($content['params']).', \'iframe\' ,'.$db->quote($content['library']['libraryId']).','.$db->quote('').','.$db->quote('').','.$db->quote('').','.$db->quote($content['disable']).')');

            $content['id'] = $this->id =  $db->lastInsertId();

        }
        else {
            $db -> query('UPDATE h5p_contents set updated_at='.time().' , title='.$db->quote($content['title']).', parameters='.$db->quote($content['params']).' ,embed_type=\'iframe\' ,library_id='.$content['library']['libraryId'].' ,filtered=\'\' ,disable='.$db->quote($content['disable']).' WHERE id='.$content['id']);
        }

        return $content['id'];
    }

    /**
     * Resets marked user data for the given content.
     *
     * @param int $contentId
     */
    public function resetContentUserData($contentId)
    {
        // TODO: Implement resetContentUserData() method.
    }

    /**
     * Save what libraries a library is depending on
     *
     * @param int $libraryId
     *   Library Id for the library we're saving dependencies for
     * @param array $dependencies
     *   List of dependencies as associative arrays containing:
     *   - machineName: The library machineName
     *   - majorVersion: The library's majorVersion
     *   - minorVersion: The library's minorVersion
     * @param string $dependency_type
     *   What type of dependency this is, the following values are allowed:
     *   - editor
     *   - preloaded
     *   - dynamic
     */
    public function saveLibraryDependencies($libraryId, $dependencies, $dependency_type)
    {
        global $db;
        $db->beginTransaction();
        foreach ($dependencies as $dependency) {
            $db->query('INSERT INTO h5p_libraries_libraries (library_id, required_library_id, dependency_type)
            SELECT '.$libraryId.', hl.id, '.$db->quote($dependency_type).'
            FROM h5p_libraries hl
            WHERE name = '.$db->quote($dependency['machineName']).'
                AND major_version = '.$dependency['majorVersion'].'
                AND minor_version = '.$dependency['minorVersion']);// ON CONFLICT(library_id) REPLACE SET dependency_type ='.$db->quote($dependency_type));
        }
        $db->commit();
    }

    /**
     * Give an H5P the same library dependencies as a given H5P
     *
     * @param int $contentId
     *   Id identifying the content
     * @param int $copyFromId
     *   Id identifying the content to be copied
     * @param int $contentMainId
     *   Main id for the content, typically used in frameworks
     *   That supports versions. (In this case the content id will typically be
     *   the version id, and the contentMainId will be the frameworks content id
     */
    public function copyLibraryUsage($contentId, $copyFromId, $contentMainId = NULL)
    {
      global $db;
        $db->query('INSERT INTO h5p_contents_libraries (content_id, library_id, dependency_type, weight, drop_css)
        SELECT '.$contentId.', hcl.library_id, hcl.dependency_type, hcl.weight, hcl.drop_css
          FROM h5p_contents_libraries hcl
          WHERE hcl.content_id ='.$copyFromId);
    }

    /**
     * Deletes content data
     *
     * @param int $contentId
     *   Id identifying the content
     */
    public function deleteContentData($contentId)
    {      global $db;
        $db->query('DELETE FROM h5p_contents WHERE id = ' . $contentId);
    }

    /**
     * Delete what libraries a content item is using
     *
     * @param int $contentId
     *   Content Id of the content we'll be deleting library usage for
     */
    public function deleteLibraryUsage($contentId)
    {
        global $db;
        $db->query('DELETE FROM h5p_contents_libraries WHERE content_id = ' . $contentId);
    }

    /**
     * Saves what libraries the content uses
     *
     * @param int $contentId
     *   Id identifying the content
     * @param array $librariesInUse
     *   List of libraries the content uses. Libraries consist of associative arrays with:
     *   - library: Associative array containing:
     *     - dropLibraryCss(optional): comma separated list of machineNames
     *     - machineName: Machine name for the library
     *     - libraryId: Id of the library
     *   - type: The dependency type. Allowed values:
     *     - editor
     *     - dynamic
     *     - preloaded
     */
    public function saveLibraryUsage($contentId, $librariesInUse)
    {
        global $db;
        $dropLibraryCssList = array();
        foreach ($librariesInUse as $dependency) {
            if (!empty($dependency['library']['dropLibraryCss'])) {
                $dropLibraryCssList = array_merge($dropLibraryCssList, explode(', ', $dependency['library']['dropLibraryCss']));
            }
        }

        $db->beginTransaction();
        foreach ($librariesInUse as $dependency) {
            $dropCss = in_array($dependency['library']['machineName'], $dropLibraryCssList) ? 1 : 0;
            $db ->query('INSERT INTO h5p_contents_libraries (content_id, library_id, dependency_type, drop_css, weight) '.
                'values('.$contentId.',\''.$dependency['library']['libraryId'].'\',\''.$dependency['type'].'\',\''.$dropCss.'\',\''.$dependency['weight'].'\')');
        }
        $db->commit();
    }

    /**
     * Get number of content/nodes using a library, and the number of
     * dependencies to other libraries
     *
     * @param int $libraryId
     *   Library identifier
     * @param boolean $skipContent
     *   Flag to indicate if content usage should be skipped
     * @return array
     *   Associative array containing:
     *   - content: Number of content using the library
     *   - libraries: Number of libraries depending on the library
     */
    public function getLibraryUsage($libraryId, $skipContent = FALSE)
    {
        global $db;


            $statement = $db -> query("SELECT COUNT(distinct c.id)
              FROM h5p_libraries l
              JOIN h5p_contents_libraries cl ON l.id = cl.library_id
              JOIN h5p_contents c ON cl.content_id = c.id
              WHERE l.id = ".$libraryId);

            $content = $statement->fetch();

            $statement = $db -> query("SELECT COUNT(*)
              FROM h5p_libraries_libraries
              WHERE required_library_id = ".$libraryId);

            $libraries = $statement->fetch();

            return array(
                //'content' => $skipContent ? -1 : $content[0],
                'content' => $content[0],
                'libraries' => $libraries[0]
            );



    }

    /**
     * Loads a library
     *
     * @param string $machineName
     *   The library's machine name
     * @param int $majorVersion
     *   The library's major version
     * @param int $minorVersion
     *   The library's minor version
     * @return array|FALSE
     *   FALSE if the library does not exist.
     *   Otherwise an associative array containing:
     *   - libraryId: The id of the library if it is an existing library.
     *   - title: The library's name
     *   - machineName: The library machineName
     *   - majorVersion: The library's majorVersion
     *   - minorVersion: The library's minorVersion
     *   - patchVersion: The library's patchVersion
     *   - runnable: 1 if the library is a content type, 0 otherwise
     *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
     *   - embedTypes(optional): list of supported embed types
     *   - preloadedJs(optional): comma separated string with js file paths
     *   - preloadedCss(optional): comma separated sting with css file paths
     *   - dropLibraryCss(optional): list of associative arrays containing:
     *     - machineName: machine name for the librarys that are to drop their css
     *   - semantics(optional): Json describing the content structure for the library
     *   - preloadedDependencies(optional): list of associative arrays containing:
     *     - machineName: Machine name for a library this library is depending on
     *     - majorVersion: Major version for a library this library is depending on
     *     - minorVersion: Minor for a library this library is depending on
     *   - dynamicDependencies(optional): list of associative arrays containing:
     *     - machineName: Machine name for a library this library is depending on
     *     - majorVersion: Major version for a library this library is depending on
     *     - minorVersion: Minor for a library this library is depending on
     *   - editorDependencies(optional): list of associative arrays containing:
     *     - machineName: Machine name for a library this library is depending on
     *     - majorVersion: Major version for a library this library is depending on
     *     - minorVersion: Minor for a library this library is depending on
     */
    public function loadLibrary($machineName, $majorVersion, $minorVersion)
    {
        global $db;
        $statement = $db->query ('SELECT id as libraryId, name as machineName, title, major_version as majorVersion, minor_version as minorVersion, patch_version as patchVersion,
          embed_types as embedTypes, preloaded_js as preloadedJs, preloaded_css as preloadedCss, drop_library_css as dropLibraryCss, fullscreen, runnable,
          semantics, has_icon as hasIcon
        FROM h5p_libraries
        WHERE name = '.$db->quote($machineName).'
        AND major_version = '.$majorVersion.'
        AND minor_version ='.$minorVersion);
        $library = $statement->fetch();

        $result = $db -> query ('SELECT hl.name as machineName, hl.major_version as majorVersion, hl.minor_version as minorVersion, hll.dependency_type as dependencyType
        FROM h5p_libraries_libraries hll
        JOIN h5p_libraries hl ON hll.required_library_id = hl.id
        WHERE hll.library_id = '.$library['libraryId']);

        $dependencies = $result->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($dependencies as $dependency) {
            $library[$dependency['dependencyType'] . 'Dependencies'][] = array(
                'machineName' => $dependency['machineName'],
                'majorVersion' => $dependency['majorVersion'],
                'minorVersion' => $dependency['minorVersion'],
            );
        }
        return $library;
    }

    /**
     * Loads library semantics.
     *
     * @param string $machineName
     *   Machine name for the library
     * @param int $majorVersion
     *   The library's major version
     * @param int $minorVersion
     *   The library's minor version
     * @return string
     *   The library's semantics as json
     */
    public function loadLibrarySemantics($machineName, $majorVersion, $minorVersion)
    {
        global $db;
        $prep = $db->prepare('SELECT semantics FROM h5p_libraries WHERE name = \'' .$machineName . '\' AND major_version='.$majorVersion.' AND minor_version='.$minorVersion);
        $prep->execute();
        $semantics = $prep->fetchColumn();
        return ($semantics === FALSE ? NULL : $semantics);
    }

    /**
     * Makes it possible to alter the semantics, adding custom fields, etc.
     *
     * @param array $semantics
     *   Associative array representing the semantics
     * @param string $machineName
     *   The library's machine name
     * @param int $majorVersion
     *   The library's major version
     * @param int $minorVersion
     *   The library's minor version
     */
    public function alterLibrarySemantics(&$semantics, $machineName, $majorVersion, $minorVersion)
    {
        // TODO: Implement alterLibrarySemantics() method.
    }

    /**
     * Delete all dependencies belonging to given library
     *
     * @param int $libraryId
     *   Library identifier
     */
    public function deleteLibraryDependencies($libraryId)
    {
        global $db;
        $db->query('DELETE FROM h5p_libraries_libraries WHERE library_id = ' . $libraryId);
    }

    /**
     * Start an atomic operation against the dependency storage
     */
    public function lockDependencyStorage()
    {
        // TODO: Implement lockDependencyStorage() method.
    }

    /**
     * Stops an atomic operation against the dependency storage
     */
    public function unlockDependencyStorage()
    {
        // TODO: Implement unlockDependencyStorage() method.
    }

    /**
     * Delete a library from database and file system
     *
     * @param stdClass $library
     *   Library object with id, name, major version and minor version.
     */
    public function deleteLibrary($library)
    {
        // TODO: Implement deleteLibrary() method.
    }

    /**
     * Load content.
     *
     * @param int $id
     *   Content identifier
     * @return array
     *   Associative array containing:
     *   - contentId: Identifier for the content
     *   - params: json content as string
     *   - embedType: csv of embed types
     *   - title: The contents title
     *   - language: Language code for the content
     *   - libraryId: Id for the main library
     *   - libraryName: The library machine name
     *   - libraryMajorVersion: The library's majorVersion
     *   - libraryMinorVersion: The library's minorVersion
     *   - libraryEmbedTypes: CSV of the main library's embed types
     *   - libraryFullscreen: 1 if fullscreen is supported. 0 otherwise.
     */
    public function loadContent($id)
    {
        global $db;

        $prep = $db->prepare(
            "SELECT hc.id
              , hc.title
              , hc.parameters AS params
              , hc.filtered
              , hc.slug AS slug
              , hc.user_id
              , hc.embed_type AS embedType
              , hc.disable
              , hl.id AS libraryId
              , hl.name AS libraryName
              , hl.major_version AS libraryMajorVersion
              , hl.minor_version AS libraryMinorVersion
              , hl.embed_types AS libraryEmbedTypes
              , hl.fullscreen AS libraryFullscreen
        FROM h5p_contents hc
        JOIN h5p_libraries hl ON hl.id = hc.library_id
        WHERE hc.id =".$id);

        $prep->execute();
        $content = $prep->fetch();
        $content['metadata'] = []; // @todo fetch this from content lib?
        return $content;
    }

    /**
     * Load dependencies for the given content of the given type.
     *
     * @param int $id
     *   Content identifier
     * @param int $type
     *   Dependency types. Allowed values:
     *   - editor
     *   - preloaded
     *   - dynamic
     * @return array
     *   List of associative arrays containing:
     *   - libraryId: The id of the library if it is an existing library.
     *   - machineName: The library machineName
     *   - majorVersion: The library's majorVersion
     *   - minorVersion: The library's minorVersion
     *   - patchVersion: The library's patchVersion
     *   - preloadedJs(optional): comma separated string with js file paths
     *   - preloadedCss(optional): comma separated sting with css file paths
     *   - dropCss(optional): csv of machine names
     */
    public function loadContentDependencies($id, $type = NULL)
    {
        global $db;
        $query = 'SELECT hl.id
              , hl.name AS machineName
              , hl.major_version AS majorVersion
              , hl.minor_version AS minorVersion
              , hl.patch_version AS patchVersion
              , hl.preloaded_css AS preloadedCss
              , hl.preloaded_js AS preloadedJs
              , hcl.drop_css AS dropCss
              , hcl.dependency_type AS dependencyType
        FROM h5p_contents_libraries hcl
        JOIN h5p_libraries hl ON hcl.library_id = hl.id
        WHERE hcl.content_id ='. $id;

        if ($type !== NULL) {
            $query .= ' AND hcl.dependency_type = \''. $type . '\'';
        }

        $query .= " ORDER BY hcl.weight";

        return $db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $results = $db -> query($query);
        $ret = array();
        while ($row = $results->fetchArray()) {
            $ret[]= $row;
        }
        return $ret;
    }

    /**
     * Get stored setting.
     *
     * @param string $name
     *   Identifier for the setting
     * @param string $default
     *   Optional default value if settings is not set
     * @return mixed
     *   Whatever has been stored as the setting
     */
    public function getOption($name, $default = NULL)
    {
        return true;
        // TODO: Implement getOption() method.
    }

    /**
     * Stores the given setting.
     * For example when did we last check h5p.org for updates to our libraries.
     *
     * @param string $name
     *   Identifier for the setting
     * @param mixed $value Data
     *   Whatever we want to store as the setting
     */
    public function setOption($name, $value)
    {
        // TODO: Implement setOption() method.
    }

    /**
     * This will update selected fields on the given content.
     *
     * @param int $id Content identifier
     * @param array $fields Content fields, e.g. filtered or slug.
     */
    public function updateContentFields($id, $fields)
    {
        // TODO: Implement updateContentFields() method.
    }

    /**
     * Will clear filtered params for all the content that uses the specified
     * library. This means that the content dependencies will have to be rebuilt,
     * and the parameters re-filtered.
     *
     * @param int $library_id
     */
    public function clearFilteredParameters($library_id)
    {
        // TODO: Implement clearFilteredParameters() method.
    }

    /**
     * Get number of contents that has to get their content dependencies rebuilt
     * and parameters re-filtered.
     *
     * @return int
     */
    public function getNumNotFiltered()
    {
        global $db;

        $statement = $db -> query( "SELECT COUNT(id)
                                                FROM h5p_contents
                                                WHERE filtered = ''");
        return intval($statement->fetchColumn());
    }

    /**
     * Get number of contents using library as main library.
     *
     * @param int $libraryId
     * @return int
     */
    public function getNumContent($libraryId, $skip = NULL)
    {
        global $db;
        //$skip_query = empty($skip) ? '' : " AND id NOT IN ($skip)";

        $statement = $db -> prepare( "SELECT COUNT(id)
                                                FROM h5p_contents
                                                WHERE library_id = :libId");
        $statement->bindParam(':libId', $libraryId);
        $statement->execute();
        return $statement->fetchColumn();

    }

    /**
     * Determines if content slug is used.
     *     * @param string $slug
     * @return boolean
     */
    public function isContentSlugAvailable($slug)
    {
        global $db;
        $st = $db->prepare('SELECT slug FROM h5p_contents WHERE slug = \'' . $slug . '\'');
        $st -> execute();
        return !$st->fetchColumn();
    }

    public function libraryHasUpgrade($library) {
        return false;
    }


    /**
     * Generates statistics from the event log per library
     *
     * @param string $type Type of event to generate stats for
     * @return array Number values indexed by library name and version
     */
    public function getLibraryStats($type)
    {
        // TODO: Implement getLibraryStats() method.
    }

    /**
     * Aggregate the current number of H5P authors
     * @return int
     */
    public function getNumAuthors()
    {
        // TODO: Implement getNumAuthors() method.
    }

    /**
     * Stores hash keys for cached assets, aggregated JavaScripts and
     * stylesheets, and connects it to libraries so that we know which cache file
     * to delete when a library is updated.
     *
     * @param string $key
     *  Hash key for the given libraries
     * @param array $libraries
     *  List of dependencies(libraries) used to create the key
     */
    public function saveCachedAssets($key, $libraries)
    {
        // TODO: Implement saveCachedAssets() method.
    }

    /**
     * Locate hash keys for given library and delete them.
     * Used when cache file are deleted.
     *
     * @param int $library_id
     *  Library identifier
     * @return array
     *  List of hash keys removed
     */
    public function deleteCachedAssets($library_id)
    {
        return array(); // at this time we delete everything everytime
        // TODO: Implement deleteCachedAssets() method.
    }

    /**
     * Get the amount of content items associated to a library
     * return int
     */
    public function getLibraryContentCount()
    {
        // TODO: Implement getLibraryContentCount() method.
    }

    /**
     * Will trigger after the export file is created.
     */
    public function afterExportCreated($content, $filename)
    {
        // TODO: Implement afterExportCreated() method.
    }

    /**
     * Check if user has permissions to an action
     *
     * @method hasPermission
     * @param  [H5PPermission] $permission Permission type, ref H5PPermission
     * @param  [int]           $id         Id need by platform to determine permission
     * @return boolean
     */
    public function hasPermission($permission, $id = NULL)
    {

        return true;
//todo implement this
        /*
        switch ($permission) {
            case H5PPermission::DOWNLOAD_H5P:
            case H5PPermission::EMBED_H5P:
                return self::currentUserCanEdit($contentUserId);

            case H5PPermission::CREATE_RESTRICTED:
            case H5PPermission::UPDATE_LIBRARIES:
                return current_user_can('manage_h5p_libraries');

            case H5PPermission::INSTALL_RECOMMENDED:
                current_user_can('install_recommended_h5p_libraries');

        }
        return FALSE;*/
    }

    /**
     * Replaces existing content type cache with the one passed in
     *
     * @param object $contentTypeCache Json with an array called 'libraries'
     *  containing the new content type cache that should replace the old one.
     */
    public function replaceContentTypeCache($contentTypeCache)
    {
        global $db;
        // Replace existing content type cache
        $db->query("TRUNCATE TABLE h5p_libraries_hub_cache");
        foreach ($contentTypeCache->contentTypes as $ct) {
            // Insert into db
              $quArr = array(
                'machine_name'      => $ct->id,
                'major_version'     => $ct->version->major,
                'minor_version'     => $ct->version->minor,
                'patch_version'     => $ct->version->patch,
                'h5p_major_version' => $ct->coreApiVersionNeeded->major,
                'h5p_minor_version' => $ct->coreApiVersionNeeded->minor,
                'title'             => $ct->title,
                'summary'           => $ct->summary,
                'description'       => $ct->description,
                'icon'              => $ct->icon,
                'created_at'        => self::dateTimeToTime($ct->createdAt),
                'updated_at'        => self::dateTimeToTime($ct->updatedAt),
                'is_recommended'    => $ct->isRecommended === TRUE ? 1 : 0,
                'popularity'        => $ct->popularity,
                'screenshots'       => json_encode($ct->screenshots),
                'license'           => json_encode(isset($ct->license) ? $ct->license : array()),
                'example'           => $ct->example,
                'tutorial'          => isset($ct->tutorial) ? $ct->tutorial : '',
                'keywords'          => json_encode(isset($ct->keywords) ? $ct->keywords : array()),
                'categories'        => json_encode(isset($ct->categories) ? $ct->categories : array()),
                'owner'             => $ct->owner
            );

            $query  = 'INSERT INTO h5p_libraries_hub_cache ';
            $ks = array();
            $vs = array();
            foreach ($quArr as $k => $v) {
                $ks[] = $k;
                $vs[] = $db->quote($v);
            }
            $query .= '('.implode(',', $ks).') values ('.implode(',', $vs).')';
            $db -> query($query);
        }
    }
}
