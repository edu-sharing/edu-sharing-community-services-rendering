<?php

class mod_directory extends ESRender_Module_NonContentNode_Abstract {


    public function inline() {
        $children = array();
        $i = 0;
        $childrenItems = $this -> esObject->getData()->children;
        if($childrenItems) {
            if(!is_array($childrenItems))
                $childrenItems = array($childrenItems);
                foreach($childrenItems as $child) {
                    $children[$i]['iconUrl'] = $child -> iconURL;
                    $children[$i]['name'] = $child -> name;
                    $children[$i]['NodeID'] = $child -> ref -> id;
                    $i++;
                }
        }

        $creator = $this -> esObject -> getNodeProperty('createdBy') -> firstname . ' ' . $this -> esObject -> getNodeProperty('createdBy') -> lastName;
        if(strpos(strtolower($creator), 'administrator') !== false || strpos(strtolower($creator), 'unknown') !== false)
            $creator = '';
        $data = array('title' => htmlentities($this -> esObject->getTitle()), 'children' => $children, 'parentUrl'=> $this->lmsInlineHelper(), 'folderUrl' => Config::get('homeRepository')->url . '/components/workspace?' . $this -> esObject -> getObjectID(), 'creator' => $creator);
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/directory/inline', $data);
        return true;
    }

    public function instanceExists() {
        $Logger = $this -> getLogger();

        $pdo = RsPDO::getInstance();
        $hasVersion = !empty($this->esObject -> getVersion());

        try {
            $sql = 'SELECT * FROM "ESOBJECT" ' .
                'WHERE "ESOBJECT_REP_ID" = :repid ' .
                'AND "ESOBJECT_CONTENT_HASH" = :contenthash ' .
                'AND "ESOBJECT_OBJECT_ID" = :objectid ';

            if ($hasVersion) {
                $sql .= 'AND "ESOBJECT_OBJECT_VERSION" = :version';
            }

            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $this -> esObject -> getRepId());
            $stmt -> bindValue(':contenthash', $this -> esObject -> getContentHash());
            $stmt -> bindValue(':objectid', $this -> esObject -> getObjectID());
            $hasVersion && $stmt -> bindValue(':version', $this->esObject -> getVersion());
            $stmt -> execute();

            $result = $stmt -> fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $this -> esObject -> setInstanceData($result);

                // check if cache exists
                global $CC_RENDER_PATH;
                $module = $this -> esObject -> getModule();
                $src_file =  $CC_RENDER_PATH . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . $this->esObject->getSubUri_file();
                $src_file .= DIRECTORY_SEPARATOR . $this->esObject->getObjectIdVersion();
                if ((is_file($src_file)) || (is_readable($src_file))) {
                    $Logger -> debug('Instance exists.');
                    return true;
                }else{
                    $Logger -> debug('No cache, deleting from DB...');
                    try {
                        $this->esObject->deleteFromDb();
                    } catch (Exception $e) {
                        $Logger -> debug('Could not delete from DB: ' . $e);
                    }
                    return false;
                }
            }

            $Logger -> debug('Instance does not exist.');
            return false;
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage());
        }
    }

}
