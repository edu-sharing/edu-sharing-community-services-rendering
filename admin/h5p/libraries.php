<?php

require_once (dirname(__FILE__) . '/../../conf.inc.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p.classes.php');
require_once (dirname(__FILE__) . '/../../modules/h5p/H5PFramework.php');
require_once dirname(__FILE__) . '/../locale/lang.php';

session_start();
if ($_SESSION['loggedin'] !== 1){
    echo 'Not logged in! <a href="../index.php">Login</a>';
    exit;
}

global $H5PFramework;
$H5PFramework = new H5PFramework();

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
    <li><a href="libraries.php">H5P-Libraries</a></li>
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
    <form action="libraries.php" method=post class=delete-h5p>
        <input value="<?php echo $_POST['search_h5p']; ?>" placeholder="ID, Title..." name=search_h5p />
        <input class="btn" type=submit value="Search" />
    </form></td>
</div>



<?php

if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}
$no_of_records_per_page = 20;
$offset = ($pageno-1) * $no_of_records_per_page;



if ($_POST['search_h5p']){
    try{

        if(is_numeric($_POST['search_h5p'])){
            $query_condition = "WHERE id=".$_POST['search_h5p']." OR title LIKE '%".$_POST['search_h5p']."%' ORDER BY name ASC";
        }else{
            $query_condition = "WHERE title LIKE '%".$_POST['search_h5p']."%' ORDER BY name ASC";
        }

        $total_pages_sql = "SELECT COUNT(*) FROM h5p_libraries ".$query_condition;
        $statement = $db -> query($total_pages_sql);
        $total_rows =  $statement->fetchColumn();
        $total_pages = ceil($total_rows / $no_of_records_per_page);

        $query = "SELECT id, title, major_version, minor_version, patch_version  FROM h5p_libraries ".$query_condition." LIMIT ".$offset.", ".$no_of_records_per_page;
        $statement = $db -> query($query);
        $results = $statement->fetchAll(\PDO::FETCH_OBJ);
        if(empty($results)){
            echo '<h3>Nothing found for: '.$_POST['search_h5p'].'</h3>';
        }elseif(!$results){
            print_r($results);
        }
    }catch(Exception $e) {
        var_dump($e);
    }
}else{
    $total_pages_sql = "SELECT COUNT(*) FROM h5p_libraries";
    $statement = $db -> query($total_pages_sql);
    $total_rows =  $statement->fetchColumn();
    $total_pages = ceil($total_rows / $no_of_records_per_page);

    $query = "SELECT id, title, name, major_version, minor_version, patch_version FROM h5p_libraries ORDER BY title ASC LIMIT ".$offset.", ".$no_of_records_per_page;
    $statement = $db -> query($query);
    $results = $statement->fetchAll(\PDO::FETCH_OBJ);

}

?>

<table class="h5p-content">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Version</th>
        <th># Used by content</th>
        <th># Used by libraries</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($results as $result){

        try{  //get the number of contents that uses the library
            $lib_uses_sql = "SELECT COUNT(*) FROM h5p_contents_libraries WHERE library_id=".$result->id;
            $statement = $db -> query($lib_uses_sql);
            $lib_uses =  $statement->fetchColumn();
        }catch(Exception $e) {
            var_dump($e);
        }

        try{  //get the number of libraries that require the library
            $lib_req_sql = "SELECT COUNT(*) FROM h5p_libraries_libraries WHERE library_id=".$result->id;
            $statement = $db -> query($lib_req_sql);
            $lib_req =  $statement->fetchColumn();
        }catch(Exception $e) {
            var_dump($e);
        }

        try{
            $lib_version_sql = "SELECT id, major_version, minor_version, patch_version FROM h5p_libraries WHERE name='".$result->name."'";
            $statement = $db -> query($lib_version_sql);
            $lib_versions =  $statement->fetchAll(\PDO::FETCH_OBJ);
        }catch(Exception $e) {
            echo $lib_version_sql.'</br>';
            var_dump($e);
        }
        $newer_version = false;
        if (count($lib_versions) > 1){
            $current_version = $result->major_version.'.'.$result->minor_version.'.'.$result->patch_version;
            $new_version = $current_version;
            foreach ($lib_versions as $version){
                $tmp_version = $version->major_version.'.'.$version->minor_version.'.'.$version->patch_version;
                if (version_compare ($current_version,  $tmp_version) === -1){
                    $newer_version = true;
                    if (version_compare ($new_version,  $tmp_version) === -1){
                        $new_version = $tmp_version;
                        $new_version_id = $version->id;
                    }

                }
            }
        }



        echo '<tr>';
        echo '<td>'.$result->id.'</td>';
        echo '<td><a class="library-link" href="library_detail.php?libraryId='.$result->id.'&libraryTitle='.$result->title.'">'.$result->title.'</a></td>';
        echo '<td>'.$result->major_version.'.'.$result->minor_version.'.'.$result->patch_version.'</td>';
        echo '<td>'.$lib_uses.'</td>';
        echo '<td>'.$lib_req.'</td>';
        if ($lib_uses == 0){
            echo '<td>delete</td>';
        }elseif($newer_version){
            echo '<td><form action="libraries.php" method=post class=update_library>
                    <input type=hidden value="'.$result->id.'"name=update_library />
                    <input type=hidden value="'.$new_version_id.'"name=new_library_id />
                    <input class="btn" type=submit value="Update Library to '.$new_version.'" />
                  </form></td>';
        }else{
            echo '<td></td>';
        }
        echo '</tr>';
    } ?>
</table>

<ul class="pagination">
    <li><a href="?pageno=1">&lt;&lt;First</a></li>
    <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
        <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">&lt;Prev</a>
    </li>
    <li> <?php echo 'Page '.$pageno.' of '.$total_pages;?> </li>
    <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
        <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next&gt;</a>
    </li>
    <li><a href="?pageno=<?php echo $total_pages; ?>">Last&gt;&gt;</a></li>
</ul>

</body>
</html>
