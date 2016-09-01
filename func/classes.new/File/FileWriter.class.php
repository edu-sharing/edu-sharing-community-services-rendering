<?php
/**
 * This product Copyright 2010 metaVentis GmbH.  For detailed notice,
 * see the "NOTICE" file with this distribution.
 * 
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */
/**************************************************************************************************************************************

DESCRIPTION:    this class is used to write files and create folders.

USAGE:            $fw = new FileWriter;                                                        //instanciate
                $fw->makeFolder("/folder1/folder2","folder3");                                //makes new folder "folder3" in folder2
                $fw->write("/folder1/folder2/folder3/file.txt","contentcontencontent");        //writes in file file.txt

**************************************************************************************************************************************/

class mc_FileWriter {

    var $NEWUMASK       = 0;
    var $FOLDERPERM     = 0777;
    var $FILEPERM       = 0777;
    var $OWNER          = "nobody";
    var $ERROR;

    function mc_FileWriter(){
        unset($this->ERROR);
        return TRUE;
    }

    function makeFolder($path,$foldername){
        if(!file_exists($path."/".$foldername)){
            $oldumask = @umask($this->NEWUMASK);
            $b           = @mkdir($path."/".$foldername, $this->FOLDERPERM);
                        @umask($oldumask);
            if($b==TRUE){
                return TRUE;
            } else {
                $this->ERROR[] = "Can't create Folder [mkdir failed]";
                return FALSE;
            }
        } else return TRUE;
    }

    function write($filename,$content){
        if(!empty($filename) && !empty($content)){
            $fp = fopen($filename,"w");
            $b = fwrite($fp,$content);
            fclose($fp);
            @chmod($filename,$this->FILEPERM);
            @chown($filename,$this->OWNER);
            if($b != -1){
                return TRUE;
            } else {
                $this->ERROR[] = "Can't write File [no fwrite]";
                return FALSE;
            }
        } else {
            $this->ERROR[] = "Can't write File [no filename | no content]";
            return FALSE;
        }
    }

    /**
     * Vigorously erase files and directories.
     * @param $fileglob mixed
     *   - if string, must be a file name (foo.txt), glob pattern (*.txt), or directory name
     *   _ if array, must be an array of file names, glob patterns, or directories.
     */
/*
    function delete($filename){

       if (is_string($fileglob)) {
           if (is_file($fileglob)) {
               return unlink($fileglob);
           } else if (is_dir($fileglob)) {
               $ok = $this->delete("$fileglob/*");
               if (! $ok) {
                   return false;
               }
               return rmdir($fileglob);
           } else {
               $matching = glob($fileglob);
               if ($matching === false) {
                   return false;
               }
               $rcs = array_map('$this->delete', $matching);
               if (in_array(false, $rcs)) {
                   return false;
               }
           }
       } else if (is_array($fileglob)) {
           $rcs = array_map('$this->rm', $fileglob);
           if (in_array(false, $rcs)) {
               return false;
           }
       } else {
           return false;
       }
       return true;
    }
*/
    function getLastErrorMessage(){
        return $this->ERROR;
    }

}

/* FOR DEBUG ONLY

$fw = new FileWriter();
$fw->makeFolder(".","test");
$fw->write("./test/test.htm","works!");

FOR DEBUG ONLY */
?>