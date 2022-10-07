<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'install/sweepH5P.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'version.php';

define ( 'UPDATEVERSION', RS_VERSION );
set_time_limit(18000);
ini_set('memory_limit', '2048M');

function run($installedVersion) {

    try {
        if (version_compare ( '3.0.0', $installedVersion ) > 0) {
            rename ( MC_ROOT_PATH . 'conf/system.conf.php', MC_ROOT_PATH . 'conf/bk_system.conf.php' );
            include (MC_ROOT_PATH . 'conf/bk_system.conf.php');
            $fileContents = file_get_contents ( MC_ROOT_PATH . 'admin/update/templates/system.conf.php' );
            $fileContents = str_replace ( array (
                '[[[TOKEN_URL]]]',
                '[[[TOKEN_DOCROOT]]]',
                '[[[TOKEN_DATA_DIR]]]'
            ), array (
                $MC_SCHEME . '://' . $MC_HOST . $MC_PATH,
                $MC_DOCROOT,
                $CC_RENDER_PATH
            ), $fileContents );
            file_put_contents ( MC_ROOT_PATH . 'conf/system.conf.php', $fileContents );

            $application = new ESApp ();
            $application->getApp ( 'esmain' );
            $hc = $application->getHomeConf ();
            $homeRepConf = file_get_contents ( MC_ROOT_PATH . 'conf/esmain/app-' . $hc->prop_array ['homerepid'] . '.properties.xml' );
            $homeRepConf = str_replace ( array (
                'services/authentication',
                'services/usage</entry>',
                'services/usage?wsdl'
            ), array (
                'services/authbyapp',
                'services/usage2</entry>',
                'services/usage2?wsdl'
            ), $homeRepConf );
            file_put_contents ( MC_ROOT_PATH . 'conf/esmain/app-' . $hc->prop_array ['homerepid'] . '.properties.xml', $homeRepConf );

            $pdo = RsPDO::getInstance ();

            $sql = 'SELECT "REL_ESMODULE_MIMETYPE_TYPE" FROM "REL_ESMODULE_MIMETYPE" WHERE "REL_ESMODULE_MIMETYPE_TYPE" = :mime';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':mime', 'video/webm' );
            $stmt->execute ();
            $result = $stmt->fetchObject ();
            if (! $result) {
                $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:modid, :mime)';
                $stmt = $pdo->prepare ( $sql );
                $stmt->bindValue ( ':modid', '9' );
                $stmt->bindValue ( ':mime', 'video/webm' );
                $stmt->execute ();
            }

            $sql = 'SELECT "REL_ESMODULE_MIMETYPE_TYPE" FROM "REL_ESMODULE_MIMETYPE" WHERE "REL_ESMODULE_MIMETYPE_TYPE" = :mime';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':mime', 'video/3gpp' );
            $stmt->execute ();
            $result = $stmt->fetchObject ();
            if (! $result) {
                $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:modid, :mime)';
                $stmt = $pdo->prepare ( $sql );
                $stmt->bindValue ( ':modid', '9' );
                $stmt->bindValue ( ':mime', 'video/3gpp' );
                $stmt->execute ();
            }

            $sql = 'SELECT "REL_ESMODULE_MIMETYPE_TYPE" FROM "REL_ESMODULE_MIMETYPE" WHERE "REL_ESMODULE_MIMETYPE_TYPE" = :mime';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':mime', 'video/3gpp2' );
            $stmt->execute ();
            $result = $stmt->fetchObject ();
            if (! $result) {
                $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:modid, :mime)';
                $stmt = $pdo->prepare ( $sql );
                $stmt->bindValue ( ':modid', '9' );
                $stmt->bindValue ( ':mime', 'video/3gpp2' );
                $stmt->execute ();
            }

            $sql = 'ALTER TABLE "ESTRACK" CHANGE "ESTRACK_PARENT_ID" "ESTRACK_ESOBJECT_ID" INTEGER';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            $sql = 'DELETE FROM "REL_ESMODULE_MIMETYPE" WHERE "REL_ESMODULE_MIMETYPE_TYPE" LIKE :tif';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':tif', '%image/tif%' );
            $stmt->execute ();

            $sql = 'DROP TABLE IF EXISTS "MIMETYPE"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            $sql = 'DROP TABLE IF EXISTS "ESVOTE"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            $sql = 'DROP TABLE IF EXISTS "ESRESULT"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            $sql = 'DROP TABLE IF EXISTS "ESAPPLICATION"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            $sql = 'ALTER TABLE "ESMODULE" DROP COLUMN "ESMODULE_TYPE", DROP COLUMN "ESMODULE_URI", DROP COLUMN "ESMODULE_DISPATCHER_URI", DROP COLUMN "ESMODULE_TMP_FILEPATH", DROP COLUMN "ESMODULE_CONF"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            $sql = 'ALTER TABLE "REL_ESMODULE_MIMETYPE" DROP COLUMN "REL_ESMODULE_MIMETYPE_MIMETYPE_IDENT"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            $sql = 'ALTER TABLE "ESOBJECT" DROP COLUMN "ESOBJECT_ALF_TIMESTAMP"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            $sql = 'ALTER TABLE "ESOBJECT" DROP COLUMN "ESOBJECT_TIMESTAMP"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();
        }

        if (version_compare ( '3.0.4', $installedVersion ) > 0) {
            if(file_exists(MC_ROOT_PATH . 'version.ini'))
                unlink ( MC_ROOT_PATH . 'version.ini' );

            if(file_exists(MC_ROOT_PATH . 'maintenance'))
                rrmdir ( MC_ROOT_PATH . 'maintenance' );

            if(file_exists(MC_ROOT_PATH . 'theme/default/module/picture/js'))
                rrmdir ( MC_ROOT_PATH . 'theme/default/module/picture/js' );

            if(file_exists(MC_ROOT_PATH . 'theme/default/module/picture/css/magnifier.css'))
                unlink ( MC_ROOT_PATH . 'theme/default/module/picture/css/magnifier.css');
        }

        if (version_compare ( '3.0.5', $installedVersion ) > 0) {

            $pdo = RsPDO::getInstance();

                $sql ='ALTER TABLE "ESTRACK" ALTER COLUMN "ESTRACK_ESOBJECT_ID" TYPE varchar(40)';
                $stmt = $pdo->prepare ( $sql );
                $stmt->execute ();

                $sql ='ALTER TABLE "ESTRACK" ALTER COLUMN "ESTRACK_MODUL_ID" TYPE varchar(40)';
                $stmt = $pdo->prepare ( $sql );
                $stmt->execute ();
        }


        if (version_compare ( '3.0.7', $installedVersion ) > 0) {

            $pdo = RsPDO::getInstance();

            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:modid, :mime)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', '9' );
            $stmt->bindValue ( ':mime', 'video/x-ms-asf' );
            $stmt->execute ();

            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:modid, :mime)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', '9' );
            $stmt->bindValue ( ':mime', 'video/x-matroska' );
            $stmt->execute ();

            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:modid, :mime)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', '9' );
            $stmt->bindValue ( ':mime', 'video/x-ogm' );
            $stmt->execute ();

            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:modid, :mime)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', '9' );
            $stmt->bindValue ( ':mime', 'video/ogg' );
            $stmt->execute ();

        }

        if (version_compare ( '3.1.0', $installedVersion ) > 0) {

            $pdo = RsPDO::getInstance();

            $sql = 'UPDATE "REL_ESMODULE_MIMETYPE" SET "REL_ESMODULE_MIMETYPE_ESMODULE_ID" = :modid WHERE "REL_ESMODULE_MIMETYPE_TYPE" LIKE :mime';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', '8' );
            $stmt->bindValue ( ':mime', 'audio/vorbis' );
            $stmt->execute ();

            $sql = 'UPDATE "REL_ESMODULE_MIMETYPE" SET "REL_ESMODULE_MIMETYPE_ESMODULE_ID" = :modid WHERE "REL_ESMODULE_MIMETYPE_TYPE" LIKE :mime';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', '8' );
            $stmt->bindValue ( ':mime', 'audio/x-aiff' );
            $stmt->execute ();
        }


        if (version_compare ( '3.2.0', $installedVersion ) > 0) {
            $mArr = 'DEFINE("DISPLAY_DYNAMIC_METADATA_KEYS", serialize(array(
				"{http://www.alfresco.org/model/content/1.0}creator",
				"{http://www.campuscontent.de/model/1.0}commonlicense_key",
				"{http://www.alfresco.org/model/content/1.0}versionLabel",
				"REPOSITORY_ID"
			)));';
            file_put_contents(MC_ROOT_PATH . 'conf/system.conf.php', $mArr, FILE_APPEND | LOCK_EX);

            if(file_exists(MC_ROOT_PATH . 'vendor/lib/wurfl'))
                rrmdir ( MC_ROOT_PATH . 'vendor/lib/wurfl' );

            if(file_exists(MC_ROOT_PATH . 'modules/moodle/edu-sharing'))
                rrmdir ( MC_ROOT_PATH . 'modules/moodle/edu-sharing' );

            if(file_exists(MC_ROOT_PATH . 'modules/moodle2'))
                rrmdir ( MC_ROOT_PATH . 'modules/moodle2' );

            if(file_exists(MC_ROOT_PATH . "/func/classes.new/ESRender/Module/Moodle1Base.php"))
                unlink ( MC_ROOT_PATH . "/func/classes.new/ESRender/Module/Moodle1Base.php");

            if(file_exists(MC_ROOT_PATH . "/func/classes.new/ESRender/Module/Moodle2Base.php"))
                unlink ( MC_ROOT_PATH . "/func/classes.new/ESRender/Module/Moodle2Base.php");

            if(file_exists(MC_ROOT_PATH . "/func/classes.new/ESRender/Module/MoodleBase.php"))
                unlink ( MC_ROOT_PATH . "/func/classes.new/ESRender/Module/MoodleBase.php");

            $pdo = RsPDO::getInstance();
            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:modid, :mime)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', '8' );
            $stmt->bindValue ( ':mime', 'audio/mp3' );
            $stmt->execute ();
        }

        if(version_compare ( '3.3', $installedVersion ) > 0) {

            $fileContents = file_get_contents ( MC_ROOT_PATH . 'conf/system.conf.php' );
            $fileContents = str_replace ('date_default_timezone_set', '#date_default_timezone_set', $fileContents );
            file_put_contents ( MC_ROOT_PATH . 'conf/system.conf.php', $fileContents );

            file_put_contents(MC_ROOT_PATH . 'conf/system.conf.php', '$INTERNAL_URL = "";', FILE_APPEND | LOCK_EX);
            file_put_contents(MC_ROOT_PATH . 'conf/defines.conf.php', 'define("INTERNAL_URL", $INTERNAL_URL);', FILE_APPEND | LOCK_EX);
        }

        if (version_compare ( '4.0.0', $installedVersion ) > 0) {

            $fileContents = file_get_contents ( MC_ROOT_PATH . 'conf/system.conf.php' );
            $fileContents = str_replace ('ENABLE_METADATA_RENDERING', 'ENABLE_METADATA_INLINE_RENDERING', $fileContents );
            file_put_contents ( MC_ROOT_PATH . 'conf/system.conf.php', $fileContents );

            $files = array();

            $directory = new \RecursiveDirectoryIterator(CC_RENDER_PATH . '/picture/');
            $iterator = new \RecursiveIteratorIterator($directory);
            foreach ($iterator as $info) {
                $parts = pathinfo($info->getPathname());
                if($parts['extension'] === 'jpg')
                    $files[] = $info->getPathname();
            }

            if(file_exists (CC_RENDER_PATH_SAFE . '/picture/')) {
                $directory = new \RecursiveDirectoryIterator(CC_RENDER_PATH_SAFE . '/picture/');
                $iterator = new \RecursiveIteratorIterator($directory);
                foreach ($iterator as $info) {
                    $parts = pathinfo($info->getPathname());
                    if($parts['extension'] === 'jpg')
                        $files[] = $info->getPathname();
                }
            }

            foreach($files as $file) {
                $image = imagecreatefromjpeg($file);
                imagepng($image, str_replace('.jpg', '.png', $file));
            }

            $pdo = RsPDO::getInstance();

            $sql = 'UPDATE "REL_ESMODULE_MIMETYPE" SET "REL_ESMODULE_MIMETYPE_ESMODULE_ID" = :modid WHERE "REL_ESMODULE_MIMETYPE_TYPE" LIKE :mime';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', '3' );
            $stmt->bindValue ( ':mime', 'text/plain' );
            $stmt->execute ();

            $sql = 'SELECT max("ESMODULE_ID") FROM "ESMODULE"';
            $stmt = $pdo -> prepare ( $sql );
            $stmt -> execute();
            $result = $stmt -> fetchObject();
            $maxPrimaryKey = $result->max;

            $sql = 'INSERT INTO "ESMODULE" ("ESMODULE_ID", "ESMODULE_NAME", "ESMODULE_DESC") VALUES (:modid, :modname, :moddesc)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', $maxPrimaryKey + 1 );
            $stmt->bindValue ( ':modname', 'learningapps' );
            $stmt->bindValue ( ':moddesc', 'learningapps' );
            $stmt->execute ();
        }

        if (version_compare ( '4.0.10', $installedVersion ) > 0) {
            $pdo = RsPDO::getInstance();
            $sql ='ALTER TABLE "ESTRACK" ALTER COLUMN "ESTRACK_NAME" TYPE varchar(512)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();
        }

        if(version_compare ( '4.1.0', $installedVersion ) > 0) {
            file_put_contents(MC_ROOT_PATH . 'modules/video/config.php', 'define(\'OPTION_THREADS\', 1);', FILE_APPEND | LOCK_EX);

            $pdo = RsPDO::getInstance();

            $sql = 'SELECT max("ESMODULE_ID") as max FROM "ESMODULE"';
            $stmt = $pdo -> prepare ( $sql );
            $stmt -> execute();
            $result = $stmt -> fetchObject();
            $maxPrimaryKey = $result->max;

            $sql = 'INSERT INTO "ESMODULE" ("ESMODULE_ID", "ESMODULE_NAME", "ESMODULE_DESC") VALUES (:modid, :modname, :moddesc)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', $maxPrimaryKey + 1 );
            $stmt->bindValue ( ':modname', 'h5p' );
            $stmt->bindValue ( ':moddesc', 'h5p' );
            $stmt->execute ();

            $sql = 'SELECT max("ESMODULE_ID") as max FROM "ESMODULE"';
            $stmt = $pdo -> prepare ( $sql );
            $stmt -> execute();
            $result = $stmt -> fetchObject();
            $maxPrimaryKey = $result->max;

            $pdo = RsPDO::getInstance();
            $sql = 'INSERT INTO "ESMODULE" ("ESMODULE_ID", "ESMODULE_NAME", "ESMODULE_DESC") VALUES (:modid, :modname, :moddesc)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', $maxPrimaryKey + 1 );
            $stmt->bindValue ( ':modname', 'lti' );
            $stmt->bindValue ( ':moddesc', 'lti' );
            $stmt->execute ();
        }

        if(version_compare ( '4.1.0.1', $installedVersion ) > 0) {

            $pdo = RsPDO::getInstance();

            $sql = 'DELETE FROM "ESMODULE" WHERE "ESMODULE_NAME" = :name';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':name', 'scorm12' );
            $stmt->execute ();

            $sql = 'DELETE FROM "ESMODULE" WHERE "ESMODULE_NAME" = :name';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':name', 'scorm2004' );
            $stmt->execute ();

            $sql ='SELECT max("ESMODULE_ID") as max FROM "ESMODULE"';
            $stmt = $pdo -> prepare ( $sql );
            $stmt -> execute();
            $result = $stmt -> fetchObject();
            $maxPrimaryKey = $result->max;

            $sql = 'INSERT INTO "ESMODULE" ("ESMODULE_ID", "ESMODULE_NAME", "ESMODULE_DESC") VALUES (:modid, :modname, :moddesc)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':modid', $maxPrimaryKey + 1 );
            $stmt->bindValue ( ':modname', 'scorm' );
            $stmt->bindValue ( ':moddesc', 'SCORM' );
            $stmt->execute ();
        }

        if(version_compare ( '4.1.7', $installedVersion ) > 0) {
            //fix 4.0.12
            $pdo = RsPDO::getInstance();
            $sql = 'ALTER TABLE "ESOBJECT" ALTER COLUMN "ESOBJECT_TITLE" TYPE TEXT';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $sql = 'ALTER TABLE "ESOBJECT" ALTER COLUMN "ESOBJECT_ALF_FILENAME" TYPE TEXT';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }

        if(version_compare ( '4.2.0', $installedVersion ) > 0) {
            $pdo = RsPDO::getInstance();

            $sql = 'SELECT max("REL_ESMODULE_MIMETYPE_ID") as max FROM "REL_ESMODULE_MIMETYPE"';
            $stmt = $pdo -> prepare ( $sql );
            $stmt -> execute();
            $result = $stmt -> fetchObject();
            $maxPrimaryKey = $result->max;

            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ID", "REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:id, :mod, :mimetype)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':id', $maxPrimaryKey + 1 );
            $stmt->bindValue ( ':mod', 9);
            $stmt->bindValue ( ':mimetype', 'video/avi');
            $stmt->execute ();
        }

        if(version_compare ( '5.1', $installedVersion ) > 0) {

            $pdo = RsPDO::getInstance();

            $sql = 'SELECT max("REL_ESMODULE_MIMETYPE_ID") as max FROM "REL_ESMODULE_MIMETYPE"';
            $stmt = $pdo -> prepare ( $sql );
            $stmt -> execute();
            $result = $stmt -> fetchObject();
            $maxPrimaryKey = $result->max;

            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ID", "REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:id, :modid, :mime)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':id', $maxPrimaryKey + 1 );
            $stmt->bindValue ( ':modid', '10' );
            $stmt->bindValue ( ':mime', 'image/svg' );
            $stmt->execute ();

            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ID", "REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:id, :modid, :mime)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':id', $maxPrimaryKey + 2 );
            $stmt->bindValue ( ':modid', '10' );
            $stmt->bindValue ( ':mime', 'image/svg+xml' );
            $stmt->execute ();

            $sql = 'INSERT INTO "REL_ESMODULE_MIMETYPE" ("REL_ESMODULE_MIMETYPE_ID", "REL_ESMODULE_MIMETYPE_ESMODULE_ID", "REL_ESMODULE_MIMETYPE_TYPE") VALUES (:id, :modid, :mime)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->bindValue ( ':id', $maxPrimaryKey + 3 );
            $stmt->bindValue ( ':modid', '10' );
            $stmt->bindValue ( ':mime', 'image/webp' );
            $stmt->execute ();

            $sql = 'ALTER TABLE "ESOBJECT_CONVERSION" ADD "ESOBJECT_CONVERSION_RESOLUTION" INTEGER';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $sql ='ALTER TABLE "ESOBJECT" ALTER COLUMN "ESOBJECT_OBJECT_VERSION" TYPE varchar(40)';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            /*
             * Because content management as well as image and video processing has massively changed in this release it is not wrong to clear cache
             */
            $sql = 'DELETE FROM "ESOBJECT"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();
            $sql = 'DELETE FROM "ESOBJECT_LOCK"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();
            $sql = 'DELETE FROM "ESTRACK"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();
            $sql = 'DELETE FROM "ESOBJECT_CONVERSION"';
            $stmt = $pdo->prepare ( $sql );
            $stmt->execute ();

            rrmdir(CC_RENDER_PATH, true);
            if(!empty(CC_RENDER_PATH_SAFE))
                rrmdir(CC_RENDER_PATH_SAFE, true);
        }

        // 6.0.99 migrates h5p from sqlite to mysql/postgresql
        if(version_compare ( '6.0.99', $installedVersion ) > 0) {
            $pdo = RsPDO::getInstance();
            $h5p_ddl = file_get_contents(dirname(__FILE__, 3). DIRECTORY_SEPARATOR. 'install' . DIRECTORY_SEPARATOR . '_tmpl' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'h5p.ddl');
            $stmt = $pdo->exec($h5p_ddl);

            if ($pdo -> getDriver() == 'mysql') {
                $alterTable = 'ALTER TABLE h5p_contents ALTER COLUMN parameters longtext;';
                $stm = $pdo->exec($alterTable);
            }

            // clear h5p cache !!!!!!!NOT WORKING YET
            //\h5p_install\sweep_h5p();

        }

    } catch ( Exception $e ) {
        error_log ( print_r ( $e, true ) );
        return false;
    }

    return true;
}

function rrmdir($dir, $keepRoot = false) {
    if (is_dir ( $dir )) {
        $objects = scandir ( $dir );
        foreach ( $objects as $object ) {
            if ($object != "." && $object != "..") {
                if (is_dir ( $dir . "/" . $object ))
                    rrmdir ( $dir . "/" . $object );
                else
                    unlink ( $dir . "/" . $object );
            }
        }
        if(!$keepRoot)
            rmdir ( $dir );
    }
}
