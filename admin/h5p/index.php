<?php
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'header.php';
?>

<div class="h5p-search">
    <form action="index.php"  method=post class=delete-h5p>
        <input value="<?php echo $_POST['search_h5p']; ?>" placeholder="ID, Node-ID, Title..." name=search_h5p />
        <input class="btn" type=submit value="Search" />
    </form>
</div>

<?php
if($_POST['delete_h5p']){
    $dirPath =  $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'. DIRECTORY_SEPARATOR . 'content'. DIRECTORY_SEPARATOR. $_POST['delete_h5p'];
    if (!removeDir($dirPath)){
        error_log('could not delete ' . $dirPath);
    }else{
        error_log('deleted ' . $dirPath);
    }

    $query_libraries = $db -> prepare("DELETE FROM h5p_contents_libraries WHERE content_id = :id");
    $query_libraries->bindParam(':id', $_POST['delete_h5p']);
    $results_libraries = $query_libraries->execute();

    $query = $db -> prepare("DELETE FROM h5p_contents WHERE id = :id");
    $query->bindParam(':id', $_POST['delete_h5p']);
    $results = $query->execute();
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
        $like_id = '%' . $_POST['search_h5p']. '%';
        $params = array(
            ':like_id'    =>  $like_id
        );

        if(is_numeric($_POST['search_h5p'])){
            $query_condition = "WHERE id=:id OR title LIKE :like_id OR description LIKE :like_id";
            $params[':id'] = $_POST['search_h5p'];
        }else{
            $query_condition = "WHERE title LIKE :like_id OR description LIKE :like_id";
        }

        $total_pages_sql = $db -> prepare("SELECT COUNT(*) FROM h5p_contents ".$query_condition);
        $total_pages_sql->execute($params);
        $total_rows =  $total_pages_sql->fetchColumn();
        $total_pages = ceil($total_rows / $no_of_records_per_page);

        $query = $db -> prepare("SELECT id, title, updated_at, description FROM h5p_contents ".$query_condition." LIMIT :offset, :no_of_records_per_page");
        $params[':offset'] = $offset;
        $params[':no_of_records_per_page'] = $no_of_records_per_page;
        $query->execute($params);
        $results = $query->fetchAll(\PDO::FETCH_OBJ);
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

    $query = $db -> prepare("SELECT id, title, updated_at, description  FROM h5p_contents LIMIT :offset, :no_of_records_per_page");
    $query->bindParam(':offset', $offset);
    $query->bindParam(':no_of_records_per_page', $no_of_records_per_page);
    $query->execute();
    $results = $query->fetchAll(\PDO::FETCH_OBJ);
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
