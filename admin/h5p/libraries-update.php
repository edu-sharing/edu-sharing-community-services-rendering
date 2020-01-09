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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>H5P-Admin-Backend</title>

    <link rel="stylesheet" href="css/h5p.css" />
    <script src="js/sweetalert2.all.min.js"></script>
</head>
<body>

<h1>H5P-Admin-Backend - Libraries</h1>
<ul class="menu">
    <li><a href="index.php">H5P-Content</a></li>
    <li><a href="libraries2.php">H5P-Libraries</a></li>
    <li><a href="../index.php">Rendering-Service-Admin</a></li>
</ul>


<div class="wrap">
    <?php

    if ($id === NULL) {
        $id = filter_input(INPUT_POST, 'library_id', FILTER_SANITIZE_NUMBER_INT);
    }

    // Try to find content with $id.
    $statement = $db -> query("SELECT id, title, name, major_version, minor_version, patch_version, runnable, fullscreen
          FROM h5p_libraries
          WHERE id = ".$id);

    $library = $statement->fetch(\PDO::FETCH_OBJ);

    if (!$library) {
        error_log('Cannot find library with id:'.$id);
    }

    $settings = display_content_upgrades($library);
    print_settings($settings, 'H5PAdminIntegration');



      /**
   * JSON encode and print the given H5P JavaScript settings.
   *
   * @since 1.0.0
   * @param array $settings
   */
  function print_settings(&$settings, $obj_name = 'H5PIntegration') {
    static $printed;
    if (!empty($printed[$obj_name])) {
      return; // Avoid re-printing settings
    }

    $json_settings = json_encode($settings);
    if ($json_settings !== FALSE) {
      $printed[$obj_name] = TRUE;
      print '<script>' . $obj_name . ' = ' . $json_settings . ';</script>';
    }
  }

    /**
   * Display a list of all h5p content libraries.
   *
   * @since 1.1.0
   */
  function display_content_upgrades($library) {
    global $db, $H5PCore, $H5PFramework, $CC_RENDER_PATH;


    $statement = $db -> prepare("SELECT hl2.id, hl2.name, hl2.title, hl2.major_version, hl2.minor_version, hl2.patch_version
          FROM h5p_libraries hl1
          JOIN h5p_libraries hl2
            ON hl2.name = hl1.name
          WHERE hl1.id = :libId 
          ORDER BY hl2.title ASC, hl2.major_version ASC, hl2.minor_version ASC");
    $statement->bindParam(':libId', $library->id);
    $statement->execute();
    $versions = $statement->fetchAll(\PDO::FETCH_OBJ);


    foreach ($versions as $version) {
      if ($version->id === $library->id) {
        $upgrades = $H5PCore->getUpgrades($version, $versions);
        //error_log(print_r( $upgrades, true));
        break;
      }
    }

    if (count($versions) < 2) {
        echo "
            <script>
                Swal.fire({
                    title: 'Attention!',
                    text: 'There are no available upgrades for ".$library->title." ".$library->major_version . "." . $library->minor_version."." . $library->patch_version."',
                    position: 'center',
                    icon: 'warning'
                })
            </script>
        ";
      return NULL;
    }

    // Get num of contents that can be upgraded
    $contents = $H5PFramework->getNumContent($library->id);
    if (!$contents) {
        echo "
            <script>
                Swal.fire({
                    title: 'Attention!',
                    text: 'There\'s no content instances to upgrade for ".$library->title." ".$library->major_version . "." . $library->minor_version."." . $library->patch_version."',
                    position: 'center',
                    icon: 'warning'
                })
            </script>
        ";
      return NULL;
    }

    // Add JavaScript settings
    $settings = array(
      'containerSelector' => '#h5p-admin-container',
      'libraryInfo' => array(
        'message' => sprintf(('You are about to upgrade %s contents. Please select upgrade version.'), $contents),
        'inProgress' => ('Upgrading to %ver...'),
        'error' => ('An error occurred while processing parameters:'),
        'errorData' => ('Could not load data for library %lib.'),
        'errorContent' => ('Could not upgrade content %id:'),
        'errorScript' => ('Could not load upgrades script for %lib.'),
        'errorParamsBroken' => ('Parameters are broken.'),
        'errorLibrary' => ('Missing required library %lib.'),
        'errorTooHighVersion' => ('Parameters contain %used while only %supported or earlier are supported.'),
        'errorNotSupported' => ('Parameters contain %used which is not supported.'),
        'done' => sprintf(('You have successfully upgraded %s.'), $contents) . '<br/><a class="btn" href="libraries2.php">' . 'Return' . '</a>',
        'library' => array(
          'name' => $library->name,
          'version' => $library->major_version . '.' . $library->minor_version,
        ),
        //'libraryBaseUrl' => $CC_RENDER_PATH . 'h5p'. DIRECTORY_SEPARATOR . 'libraries',
        'libraryBaseUrl' => 'h5p_ajax.php?action=h5p_content_upgrade_library&library=',
        'scriptBaseUrl' => '../../vendor/lib/h5p-core/js/',
        'buster' => '?ver=' . $H5PFramework->getPlatformInfo()['h5pVersion'],
        'versions' => $upgrades,
        'contents' => $contents,
        'buttonLabel' => ('Upgrade'),
        'infoUrl' => 'h5p_ajax.php?action=h5p_content_upgrade_progress&id=' . $library->id,
        'total' => $contents,
        'token' => ''
      )
    );


    echo '
        <script type="text/javascript" src="../../vendor/lib/h5p-core/js/jquery.js"></script>
        <script type="text/javascript" src="../../vendor/lib/h5p-core/js/h5p-utils.js"></script>
        <script type="text/javascript" src="../../vendor/lib/h5p-core/js/h5p-version.js"></script>
        <script type="text/javascript" src="../../vendor/lib/h5p-core/js/h5p-content-upgrade.js"></script>
        
        <link rel="stylesheet" href="../../vendor/lib/h5p-core/styles/h5p.css">
        <link rel="stylesheet" href="../../vendor/lib/h5p-core/styles/h5p-admin.css">
    ';

    return $settings;
  }

    ?>


    <?php if ($library): ?>
        <h2><?php printf('Upgrade %s %d.%d.%d content', $library->title, $library->major_version, $library->minor_version, $library->patch_version); ?></h2>
    <?php endif; ?>
    <?php if ($settings): ?>
        <div id="h5p-admin-container"><?php echo 'Please enable JavaScript.'; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
