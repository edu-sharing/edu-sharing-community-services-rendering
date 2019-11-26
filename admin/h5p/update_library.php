<?php


if(isset($_POST["update_library"])) {

    $old_library_id = $_POST["update_library"];
    $new_library_id = $_POST["new_library_id"];

    echo $new_library_id;

    $update_lib_sql = "UPDATE h5p_contents_libraries SET library_id=".$new_library_id." WHERE library_id=".$old_library_id;
    $statement = $db -> query($update_lib_sql);
    $update_libs =  $statement->execute();

    $update_content_sql = "UPDATE h5p_contents SET library_id=".$new_library_id." WHERE library_id=".$old_library_id;
    $statement = $db -> query($update_content_sql);
    $update_content =  $statement->execute();

    if ($update_content && $update_libs){
        echo "
                    <script>
                        Swal.fire({
                            title: 'Update Library: Success!',
                            text: 'Updated Library.',
                            position: 'top',
                            icon: 'success'
                        })
                    </script>
                ";
    }else{
        echo "
                <script>
                    Swal.fire({
                        title: 'Update Library Error!',
                        html: 'Could not update the Library',
                        position: 'top',
                        icon: 'error'
                    })
                </script>
            ";
    }



}
