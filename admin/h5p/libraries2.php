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

global $H5PFramework;
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

<?php
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'update_core.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'update_library.php';
?>


<h1>H5P-Admin-Backend - Libraries</h1>
<ul class="menu">
    <li><a href="index.php">H5P-Content</a></li>
    <li><a href="libraries2.php">H5P-Libraries</a></li>
    <li><a href="../index.php">Rendering-Service-Admin</a></li>
</ul>

<div class="update-core">
    <h3>Installed H5P-Version: <span class="version"><?php echo($H5PFramework->getPlatformInfo()['h5pVersion']);?></span></h3>
    <form class="file-upload" action="libraries.php" method="post" enctype="multipart/form-data">
        <h3>Upload new H5P-Core-Libraries:</h3>
        <input class="choose-core" type="file" name="fileToUpload" id="fileToUpload">
        <input class="btn" type="submit" value="Upload" name="upload_core">
    </form>
    <?php if (file_exists($ROOT_PATH."/vendor/lib/h5p-core-bak") ){
        echo '
            <form action="libraries.php" method="post">
                <input class="btn" type="submit" value="Restore Core" name="restore_core">
            </form>';
    } ?>


</div>



<div class="h5p-search">
    </br>
</div>



<table class="h5p-content">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Version</th>
        <th># Used by content</th>
        <th># Used by libraries</th>
        <th>Actions</th>
    </tr>
    <?php

    $not_cached = $H5PFramework->getNumNotFiltered();
    $libraries = $H5PFramework->loadLibraries();

    foreach ($libraries as $versions) {
        foreach ($versions as $library) {
            $usage = $H5PFramework->getLibraryUsage($library->id, $not_cached ? TRUE : FALSE);
            $upgrades = $H5PCore->getUpgrades($library, $versions);



            echo '<tr>';
                echo '<td>'.$library->id.'</td>';
                echo '<td><a class="library-link" href="library_detail.php?libraryId='.$library->id.'&libraryTitle='.$library->title.'">'.$library->title.'</a></td>';
                echo '<td>'.H5PCore::libraryVersion($library).'</td>';
                echo '<td>'.$usage['content'].'</td>';
                echo '<td>'.$usage['libraries'].'</td>';
                echo '<td>';
                if (!empty($upgrades)){
                    echo '<form action="libraries-update.php" method=post >
                        <input type=hidden value="'.$library->id.'"name=library_id />
                        <input class="btn" type=submit value="Update" />
                      </form>';
                }else{

                }
                echo '</td>';

            echo '</tr>';

        }
    }

     ?>
</table>


</body>
</html>
