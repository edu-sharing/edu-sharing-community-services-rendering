<?php

require_once (dirname(__FILE__) . '/../../conf.inc.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p.classes.php');
require_once (dirname(__FILE__) . '/../../modules/h5p/H5PFramework.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-file-storage.interface.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-default-storage.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-development.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-event-base.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-metadata.class.php');
require_once dirname(__FILE__) . '/../locale/lang.php';

session_start();
if ($_SESSION['loggedin'] !== 1){
    echo 'Not logged in! <a href="../index.php">Login</a>';
    exit;
}

global $H5PFramework, $H5PCore;
$H5PFramework = new H5PFramework();
$H5PCore = new H5PCore($H5PFramework, $H5PFramework->get_h5p_path(), $H5PFramework->get_h5p_url(), mc_Request::fetch('language', 'CHAR', 'de'), false);
$H5PStorage = new H5PStorage($H5PFramework, $H5PCore);

global $MC_URL;
global $db;
$db = new PDO('sqlite:' . $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'.DIRECTORY_SEPARATOR . 'db');
$db -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


if(isset($_GET["action"])) {

    switch ($_GET["action"]) {
        case 'h5p_content_upgrade_progress':
            ajax_upgrade_progress();
            break;
    }
    switch ($_GET["action"]) {
        case 'h5p_content_upgrade_library':
            ajax_upgrade_library();
            break;
    }

}



/**
 * AJAX processing for content upgrade script.
 */
function ajax_upgrade_progress() {
    global $db, $H5PFramework;
    header('Cache-Control: no-cache');

   /* if (!wp_verify_nonce(filter_input(INPUT_POST, 'token'), 'h5p_content_upgrade')) {
        print ('Error, invalid security token!');
        exit;
    }*/

    $library_id = filter_input(INPUT_GET, 'id');
    if (!$library_id) {
        print ('Error, missing library!');
        exit;
    }

    // Get the library we're upgrading to
    $statement = $db -> prepare( "SELECT id, name, major_version, minor_version
        FROM h5p_libraries
        WHERE id = :libId");
    $statement->bindParam(':libId', filter_input(INPUT_POST, 'libraryId'));
    $statement->execute();
    $to_library = $statement->fetch(\PDO::FETCH_OBJ);

    if (!$to_library) {
        print ('Error, invalid library!');
        exit;
    }

    // Prepare response
    $out = new stdClass();
    $out->params = array();
    //$out->token = wp_create_nonce('h5p_content_upgrade');

    // Get updated params
    $params = filter_input(INPUT_POST, 'params');
    if ($params !== NULL) {
        // Update params.
        $params = json_decode($params);
        foreach ($params as $id => $param) {
            $upgraded = json_decode($param);
            $metadata = isset($upgraded->metadata) ? $upgraded->metadata : array();

            $timezone = new DateTimeZone( 'UTC' );
            $datetime = new DateTime( 'now', $timezone );
            $current_time = $datetime->format( 'Y-m-d H:i:s' );

            $db->query( "UPDATE h5p_contents SET
                                updated_at = " . $db->quote($current_time) . ",
                                parameters = " . $db->quote(json_encode($upgraded->params)) . ",
                                library_id = " . $db->quote($to_library->id) . ",
                                filtered = " . $db->quote('') . "
                                WHERE id = ". $db->quote($id)
            );

            $db->query( "UPDATE h5p_contents_libraries SET
                                library_id = " . $db->quote($to_library->id) . "
                                WHERE content_id = ". $db->quote($id)
            );

            $statement = $db -> prepare( "SELECT title FROM h5p_contents WHERE id = :id");
            $statement->bindParam(':id', $id);
            $statement->execute();
            $lib_title = $statement->fetch(\PDO::FETCH_OBJ);

            // Log content upgrade successful
            //new H5P_Event('content', 'upgrade', $id, $lib_title, $to_library->name, $to_library->major_version . '.' . $to_library->minor_version);

            error_log('Updated Content with id '.$id.' ('.$lib_title->title.') to '.$to_library->name.'-'.$to_library->major_version . '.' . $to_library->minor_version);
        }

        //remove duplicates from h5p_contents_libraries
        $db->query( " DELETE FROM h5p_contents_libraries
                                WHERE rowid NOT IN (
                                  SELECT MIN(rowid)  
                                  FROM h5p_contents_libraries 
                                  GROUP BY library_id, drop_css, content_id, dependency_type
                                )" );
    }

    // Determine if any content has been skipped during the process
    $skipped = filter_input(INPUT_POST, 'skipped');
    if ($skipped !== NULL) {
        $out->skipped = json_decode($skipped);

        // Clean up input, only numbers
        foreach ($out->skipped as $i => $id) {
            $out->skipped[$i] = intval($id);
        }
        $skipped = implode(',', $out->skipped);
    }
    else {
        $out->skipped = array();
    }

    // Prepare our interface
    //$plugin = H5P_Plugin::get_instance();
    $interface = $H5PFramework;

    // Get number of contents for this library
    $out->left = intval( $interface->getNumContent($library_id, $skipped) );

    if ($out->left) {
        $skip_query = empty($skipped) ? '' : " AND id NOT IN ($skipped)";

        // Find the 40 first contents using library and add to params
        $statement = $db -> prepare( "SELECT id, parameters AS params, title, license
           FROM h5p_contents
          WHERE library_id = :libId
          LIMIT 40");
        $statement->bindParam(':libId', $library_id);
        $statement->execute();
        $contents = $statement->fetchAll(\PDO::FETCH_OBJ);

        foreach ($contents as $content) {
            $out->params[$content->id] =
                '{"params":' . $content->params .
                ',"metadata":' . \H5PMetadata::toJSON($content) . '}';
        }
    }

    //error_log(print_r($out));
    header('Content-type: application/json');
    print json_encode($out);
    exit;
}

/**
 * AJAX loading of libraries for content upgrade script.
 *
 * @since 1.1.0
 * @param string $name
 * @param int $major
 * @param int $minor
 */
function ajax_upgrade_library() {
    global $H5PFramework, $H5PCore;

    header('Cache-Control: no-cache');

    $library_string = filter_input(INPUT_GET, 'library');
    if (!$library_string) {
        print ('Error, missing library!');
        exit;
    }

    $library_parts = explode('/', $library_string);
    if (count($library_parts) !== 4) {
        print ('Error, invalid library!');
        exit;
    }

    $library = (object) array(
        'name' => $library_parts[1],
        'version' => (object) array(
            'major' => $library_parts[2],
            'minor' => $library_parts[3]
        )
    );

    //$plugin = H5P_Plugin::get_instance();
    $core = $H5PCore;

    $library->semantics = $core->loadLibrarySemantics($library->name, $library->version->major, $library->version->minor);

    // TODO: Library development mode
//    if ($core->development_mode & H5PDevelopment::MODE_LIBRARY) {
//      $dev_lib = $core->h5pD->getLibrary($library->name, $library->version->major, $library->version->minor);
//    }

    if (isset($dev_lib)) {
        $upgrades_script_path = $upgrades_script_url = $dev_lib['path'] . '/upgrades.js';
    }
    else {
        $suffix = '/libraries/' . $library->name . '-' . $library->version->major . '.' . $library->version->minor . '/upgrades.js';
        $upgrades_script_path = $H5PFramework->get_h5p_path() . $suffix;
        $upgrades_script_url = $H5PFramework->get_h5p_url() . $suffix;
    }

    if (file_exists($upgrades_script_path)) {
        $library->upgradesScript = $upgrades_script_url;
    }

    header('Content-type: application/json');
    print json_encode($library);
    exit;
}



/**
 * Handle ajax request to restrict access to the given library.
 *
 * @since 1.2.0
 */
function ajax_restrict_access() {
    global $wpdb;

    $library_id = filter_input(INPUT_GET, 'id');
    $restricted = filter_input(INPUT_GET, 'restrict');
    $restrict = ($restricted === '1');

    $token_id = filter_input(INPUT_GET, 'token_id');
    if (!wp_verify_nonce(filter_input(INPUT_GET, 'token'), 'h5p_library_' . $token_id) || (!$restrict && $restricted !== '0')) {
        return;
    }

    $wpdb->update(
        $wpdb->prefix . 'h5p_libraries',
        array('restricted' => $restricted),
        array('id' => $library_id),
        array('%d'),
        array('%d')
    );

    header('Content-type: application/json');
    print json_encode(array(
        'url' => admin_url('admin-ajax.php?action=h5p_restrict_library' .
            '&id=' . $library_id .
            '&token=' . wp_create_nonce('h5p_library_' . $token_id) .
            '&token_id=' . $token_id .
            '&restrict=' . ($restrict ? 0 : 1)),
    ));
    exit;
}
