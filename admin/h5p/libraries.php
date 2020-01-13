<?php
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'header.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'update_core.php';
?>

<div class="update-core">
    <h3 class="version-wrap">Installed H5P-Version: <span class="version"><?php echo($H5PFramework->getPlatformInfo()['h5pVersion']);?></span></h3>
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
                }
                echo '</td>';
            echo '</tr>';
        }
    }
     ?>
</table>
<br>

</body>
</html>
