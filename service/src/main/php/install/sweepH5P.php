<?php namespace h5p_install;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'conf.inc.php';

function sweep_h5p()
{
    /*
     * get h5p module id
     */
    $pdo = RsPDO::getInstance();
    $sql = 'SELECT "ESMODULE_ID" FROM "ESMODULE" WHERE "ESMODULE_NAME" = :name';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', 'h5p');
    $stmt->execute();
    $result = $stmt->fetchObject();
    $h5pId = $result->ESMODULE_ID;

    /*
     * delete h5p objects from esobject
     */
    $sql = 'DELETE FROM "ESOBJECT" WHERE "ESOBJECT_ESMODULE_ID" = :modid';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':modid', $h5pId);
    $stmt->execute();

    /*
     * delete h5p objects from estrack
     */
    $sql = 'DELETE FROM "ESTRACK" WHERE "ESTRACK_MODULE_ID" = :modid';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':modid', $h5pId);
    $stmt->execute();

    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object))
                        rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }

    /*
     * Delete h5p cache     *
     */
    rrmdir($CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p');
    if (!empty($CC_RENDER_PATH_SAFE))
        rrmdir($CC_RENDER_PATH_SAFE . DIRECTORY_SEPARATOR . 'h5p');
}
