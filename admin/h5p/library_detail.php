<?php
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'header.php';

if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}
$no_of_records_per_page = 20;
$offset = ($pageno-1) * $no_of_records_per_page;

if ($_GET['libraryId']){
    $librayId = $_GET['libraryId'];
    try{
        $query_condition = "WHERE library_id = ".$librayId;

        $total_pages_sql = "SELECT COUNT(*) FROM h5p_contents_libraries ".$query_condition;
        $statement = $db -> query($total_pages_sql);
        $total_rows =  $statement->fetchColumn();
        $total_pages = ceil($total_rows / $no_of_records_per_page);

        $query = "SELECT content_id FROM h5p_contents_libraries ".$query_condition." LIMIT ".$offset.", ".$no_of_records_per_page;
        $statement = $db -> query($query);
        $results = $statement->fetchAll(\PDO::FETCH_OBJ);
        if(empty($results)){
            echo '<h3>Nothing found for: '.$librayId.'</h3>';
        }elseif(!$results){
            print_r($results);
        }
    }catch(Exception $e) {
        var_dump($e);
    }
}else{
    echo '<h3>Nothing found for: '.$_GET['libraryId'].'</h3>';
}

?>
<div>
    <h2 class="detail-header">Content that uses <?php echo $_GET['libraryTitle']; ?>:</h2>
    <table class="h5p-content">
        <tr>
            <th>ID</th>
            <th>Node-ID</th>
            <th>Title</th>
            <th>Last Change</th>
        </tr>
        <?php foreach ($results as $result){

            $query = "SELECT id, title, updated_at, description  FROM h5p_contents WHERE id = ".$result->content_id;
            $statement = $db -> query($query);
            $h5p_content = $statement->fetchAll(\PDO::FETCH_OBJ);

            if (isValidTimeStamp($h5p_content[0]->updated_at)){
                $last_update = date('Y-m-d H:i:s', intval($h5p_content[0]->updated_at));
            }else{
                $last_update = $h5p_content[0]->updated_at;
            }

            echo '<tr>';
            echo '<td>'.$result->content_id.'</td>';
            echo '<td>'.$h5p_content[0]->title.'</td>';
            echo '<td>'.$h5p_content[0]->description.'</td>';
            echo '<td>'.$last_update.'</td>';
            echo '</tr>';
        }

        function isValidTimeStamp($timestamp)
        {
            if (preg_match('/\D/', $timestamp)){
                return false;
            }else{
                return true;
            }
        }

        ?>
    </table>

    <ul class="pagination">
        <li><a href="?libraryId=<?php echo $librayId; ?>&librayTitle=<?php echo $_GET['libraryTitle']; ?>&pageno=1">&lt;&lt;First</a></li>
        <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?libraryId=".$librayId."&libraryTitle=".$_GET['libraryTitle']."&pageno=".($pageno - 1); } ?>">&lt;Prev</a>
        </li>
        <li> <?php echo 'Page '.$pageno.' of '.$total_pages;?> </li>
        <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?libraryId=".$librayId."&libraryTitle=".$_GET['libraryTitle']."&pageno=".($pageno + 1); } ?>">Next&gt;</a>
        </li>
        <li><a href="?libraryId=<?php echo $librayId; ?>&librayTitle=<?php echo $_GET['libraryTitle']; ?>&pageno=<?php echo $total_pages; ?>">Last&gt;&gt;</a></li>
    </ul>
</div>

</body>
</html>
