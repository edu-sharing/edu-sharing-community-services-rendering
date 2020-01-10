<?php

require_once dirname(__FILE__) . '/../locale/lang.php';
require_once dirname(__FILE__) . '/../../conf.inc.php';

session_start();
if ($_SESSION['loggedin'] !== 1){
    echo 'Not logged in! <a href="../index.php">Login</a>';
    exit;
}

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

<h1>H5P-Admin-Backend - Content</h1>
<ul class="menu">
    <li><a href="index.php">H5P-Content</a></li>
    <li><a href="libraries.php">H5P-Libraries</a></li>
    <li><a href="../index.php">Rendering-Service-Admin</a></li>
</ul>

<div class="h5p-search">
    <form action="index.php"  method=post class=delete-h5p>
        <input value="<?php echo $_POST['search_h5p']; ?>" placeholder="ID, Node-ID, Title..." name=search_h5p />
        <input class="btn" type=submit value="Search" />
    </form></td>
</div>

<?php

require_once (dirname(__FILE__) . '/../../conf.inc.php');

global $db;
$db = new PDO('sqlite:' . $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'.DIRECTORY_SEPARATOR . 'db');
$db -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );



if($_POST['delete_h5p']){
    $dirPath =  $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'. DIRECTORY_SEPARATOR . 'content'. DIRECTORY_SEPARATOR. $_POST['delete_h5p'];
    if (!removeDir($dirPath)){
        error_log('could not delete ' . $dirPath);
    }else{
        error_log('deleted ' . $dirPath);
    }

    $query_libraries = "DELETE FROM h5p_contents_libraries WHERE content_id = ".$_POST['delete_h5p'];
    $statement_libraries = $db -> query($query_libraries);
    $results_libraries = $statement_libraries->execute();

    $query = "DELETE FROM h5p_contents WHERE id = ".$_POST['delete_h5p'];
    $statement = $db -> query($query);
    $results = $statement->execute();
    if($results){
        echo "
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Deleted H5P-Content with ID ".$_POST['delete_h5p']."',
                    position: 'top',
                    icon: 'success'
                })
            </script>
        ";
    }else{
        echo "
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Could not delete H5P-Content with ID ".$_POST['delete_h5p']."',
                    text: '".print_r($results, true)."',
                    position: 'top',
                    icon: 'error'
                })
            </script>
        ";
    }
}

function removeDir($dirPath) {
    if ($dirPath == '/' || empty($dirPath) || $dirPath == './')
        return false;
    if (!is_dir($dirPath))
        return false;
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/')
        $dirPath .= '/';
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            removeDir($file);
        } else {
            unlink($file);
        }
    }
    if (!rmdir($dirPath))
        return false;
    return true;
}

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
            $query_condition = "WHERE id=".$_POST['search_h5p']." OR title LIKE '%".$_POST['search_h5p']."%' OR description LIKE '%".$_POST['search_h5p']."%'";
        }else{
            $query_condition = "WHERE title LIKE '%".$_POST['search_h5p']."%' OR description LIKE '%".$_POST['search_h5p']."%'";
        }

        $total_pages_sql = "SELECT COUNT(*) FROM h5p_contents ".$query_condition;
        $statement = $db -> query($total_pages_sql);
        $total_rows =  $statement->fetchColumn();
        $total_pages = ceil($total_rows / $no_of_records_per_page);

        $query = "SELECT id, title, updated_at, description FROM h5p_contents ".$query_condition." LIMIT ".$offset.", ".$no_of_records_per_page;
        $statement = $db -> query($query);
        $results = $statement->fetchAll(\PDO::FETCH_OBJ);
        if(empty($results)){
            echo "
            <script>
                Swal.fire({
                    text: 'Nothing found for: ".$_POST['search_h5p']."',
                    position: 'top',
                    icon: 'warning'
                })
            </script>
        ";
        }elseif(!$results){
            print_r($results);
        }
    }catch(Exception $e) {
        var_dump($e);
    }
}else{
    $total_pages_sql = "SELECT COUNT(*) FROM h5p_contents";
    $statement = $db -> query($total_pages_sql);
    $total_rows =  $statement->fetchColumn();
    $total_pages = ceil($total_rows / $no_of_records_per_page);

    $query = "SELECT id, title, updated_at, description  FROM h5p_contents LIMIT ".$offset.", ".$no_of_records_per_page;
    $statement = $db -> query($query);
    $results = $statement->fetchAll(\PDO::FETCH_OBJ);
}
?>

<table class="h5p-content">
    <tr>
        <th>ID</th>
        <th>Node-ID</th>
        <th>Title</th>
        <th>Last Change</th>
        <th>Delete</th>
    </tr>
    <?php foreach ($results as $result){
            echo '<tr>';
            echo '<td>'.$result->id.'</td>';
            echo '<td>'.$result->title.'</td>';
            echo '<td>'.$result->description.'</td>';
            echo '<td>'.date('H:i:s - d.m.Y', intval($result->updated_at)).'</td>';
            echo '<td><form action="index.php" method=post class=delete-h5p>
                    <input type=hidden value="'.$result->id.'"name=delete_h5p />
                    <input class="btn" type=submit value="delete" />
                  </form></td>';
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
