<?php

//update core-libraries
$target_dir = $ROOT_PATH."vendor".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR;
$file_name = basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . $file_name;
$uploadOk = 1;
$upload_error = '';
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
if(isset($_POST["upload_core"])) {
    $check = is_file ($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false && strpos($file_name, 'h5p-php-library') !== false) {
        //echo "File is valid - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $upload_error .= 'File is not valid. Download zip from: <a target="_blank" href="https://github.com/h5p/h5p-php-library">github.com/h5p/h5p-php-library</a></br>';
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file) && $uploadOk === 1) {
        $upload_error .= "Sorry, file already exists.</br>";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000 && $uploadOk === 1) {
        $upload_error .= "Sorry, your file is too large.</br>";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($fileType != "zip"  && $uploadOk === 1) {
        $upload_error .= "Sorry, only ZIP files are allowed.</br>";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $upload_error .= "Sorry, your file was not uploaded.</br>";
        echo "
                <script>
                    Swal.fire({
                        title: 'Update: Core-Libraries Error!',
                        html: '".$upload_error."',
                        position: 'top',
                        icon: 'error'
                    })
                </script>
            ";

        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $uploadOk = 1;
            //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        } else {
            echo "
                <script>
                    Swal.fire({
                        title: 'Update Core-Libraries: Error!',
                        text: 'Sorry, there was an error uploading your file.',
                        position: 'top',
                        icon: 'error'
                    })
                </script>
            ";
            $uploadOk = 0;
        }
    }

    if($uploadOk === 1){
        $zip = new ZipArchive;
        $res = $zip->open($target_file);
        if ($res === TRUE) {
            $zip->extractTo($target_dir);
            $zip->close();
            if (file_exists($target_dir.substr($file_name, 0, -4))){
                rename($target_dir.substr($file_name, 0, -4), $target_dir."h5p-core-new");
                unlink($target_file); // delete zip

                if (file_exists($target_dir."h5p-core-bak") ){
                    deleteDir($target_dir."h5p-core-bak");
                    //echo "deleted core backup";
                }
                rename($target_dir."h5p-core", $target_dir."h5p-core-bak");
                rename($target_dir."h5p-core-new", $target_dir."h5p-core");

                $version = str_replace(array("h5p-php-library-", ".zip") ,"", $file_name);
                if ($version != '') {
                    $f = fopen($ROOT_PATH.'modules/h5p/config.php', 'w') or die("can't open file");
                    fwrite($f, "<?php define('H5P_Version', '".$version."');");
                    fclose($f);
                }else{
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Update Core-Libraries: Error!',
                            text: 'Error updating version.',
                            position: 'top',
                            icon: 'error'
                        })
                    </script>
                ";
                }

                echo "
                    <script>
                        Swal.fire({
                            title: 'Update Core-Libraries: Success!',
                            text: 'Updated H5p-Core-Libraries to Version ".$version."',
                            position: 'top',
                            icon: 'success'
                        })
                    </script>
                ";
            }else{
                unlink($target_file); // delete zip
                echo "
                    <script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'Could not find core-libraries in zip.',
                            position: 'top',
                            icon: 'error'
                        })
                    </script>
                ";
            }
        } else {
            echo "
                    <script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'Could not update core-libraries.',
                            position: 'top',
                            icon: 'error'
                        })
                    </script>
                ";
        }
    }
}

if(isset($_POST["restore_core"])) {
    echo 'hello!';
    if (file_exists($target_dir."h5p-core-bak") ){
        deleteDir($ROOT_PATH."vendor/lib/h5p-core");
        rename($ROOT_PATH."vendor/lib/h5p-core-bak", $ROOT_PATH."/vendor/lib/h5p-core");
        echo "
                    <script>
                        Swal.fire({
                            title: 'Success!',
                            text: 'Restored core-libraries',
                            position: 'top',
                            icon: 'info'
                        })
                    </script>
                ";
    }else {
        echo "
                    <script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'Could not restore core-libraries',
                            position: 'top',
                            icon: 'warning'
                        })
                    </script>
                ";
    }
}

function deleteDir($dirPath) {
    if ($dirPath == '/' || empty($dirPath) || $dirPath == './')
        return false;
    if (!is_dir($dirPath))
        return false;
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/')
        $dirPath .= DIRECTORY_SEPARATOR;
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    if(file_exists($dirPath.'.gitignore')){
        unlink($dirPath.'.gitignore');
    }
    if (!rmdir($dirPath)){
        return false;
    }

    return true;
}
