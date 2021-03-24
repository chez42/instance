<?php

class cFileInfo{public $filename, $basename, $extension, $size, $createdDate, $modifiedDate, $directory, $fullFile;
                public $moveto, $newname, $newExtension, $numLines, $createdTime;}

class cParsing{
    protected $file_info;

    public function __construct(){
        $this->file_info = new cFileHandling();
    }

    protected function LogProcess($custodian, $message, $process_id=0){
        global $adb;
        $id = $this->GetCustodianID($custodian);

        $query = "INSERT INTO process_log (custodian_id, process_id, entered_time, details) VALUES (?, ?, NOW(), ?)";
        $adb->pquery($query, array($id, $process_id, $message));
    }

    protected function GetCustodianID($custodian){
        global $adb;
        $query = "SELECT id FROM custodians WHERE custodian = ?";
        $result = $adb->pquery($query, array($custodian));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'id');
        }
        return 0;
    }

    protected function SetFileInfo($file){
        $tmp = new cFileInfo;
        $tmp->filename = $file->getFilename();
        $tmp->basename = $file->getBasename('.' . $file->getExtension());
        $tmp->extension = $file->getExtension();
        $tmp->newExtension = "$" . $tmp->extension;
        $tmp->size = $file->getSize();
        $tmp->createdDate = date("Y-m-d", $file->getCTime());
        $tmp->modifiedDate = date("Y-m-d", $file->getMTime());
        $tmp->createdTime = $file->getCTime();
        $tmp->directory = $file->getPath();
        $tmp->fullFile = $tmp->directory . "/" . $file->getBasename();
##        $file->moveto = str_ireplace("lanserver2n", "archive", $file->directory);
#        $file->moveto = $file->directory . '/parsed';
        $tmp->moveto = str_ireplace("lanserver2n", "archive", $tmp->directory);
        $tmp->newname = $tmp->basename . "_" . $tmp->createdDate . "." . $tmp->extension;

        return $tmp;
    }

    /**
     * @param $directory
     * @param $extension
     * @param null $num_days
     * @return array
     */
    protected function GetFiles($directory, $extension, $num_days=null): array{
        $files = array();
        if(!is_null($num_days))
            $time = 60*60*24*$num_days;

        if (file_exists($directory)) {
            foreach (new DirectoryIterator($directory) AS $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }

                if(!is_null($num_days)){
                    if($fileInfo->isFile() && time() - $fileInfo->getCTime() <= $time && strtoupper($fileInfo->getExtension()) == strtoupper($extension)){
                        $files[] = $this->SetFileInfo($fileInfo);
                    }
                }elseif(strtoupper($fileInfo->getExtension()) == strtoupper($extension)){
                    $files[] = $this->SetFileInfo($fileInfo);
                }
            }
        }
        return $files;
    }

    /**
     * @param $directory
     * @param $extension
     * @param null $num_days
     * @return array
     */
    protected function GetAllFiles($directory, $num_days=null): array{
        $files = array();
        if(!is_null($num_days))
            $time = 60*60*24*$num_days;

        if (file_exists($directory)) {
            foreach (new DirectoryIterator($directory) AS $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }

                if(!is_null($num_days)){
                    if($fileInfo->isFile() && time() - $fileInfo->getCTime() <= $time){
                        $files[] = $this->SetFileInfo($fileInfo);
                    }
                }else{
                    $files[] = $this->SetFileInfo($fileInfo);
                }
            }
        }
        return $files;
    }

    /**
     * Determines if the directory exists or not.  Creates it if not (if it can)
     * @param $directory
     * @return bool
     */
    public function ConfirmDirectory($directory, $must_be_writable = false){
        if($must_be_writable == false && is_dir($directory)) {
            return true;
        }

        if(!is_dir($directory)){//The directory doesn't exist
            if (!is_writable($directory)){//Directory isn't writeable
                return false;
            }
            mkdir($directory, 0755);//Create it
        }

        if(is_dir($directory)){
            return true;
        }
        return false;
    }

    /**
     * @param cFileInfo $file
     */
    public function TransferFileToArchive(cFileInfo $file){
#        $moveto = $file->directory . '/gox_backup';
        if($this->ConfirmDirectory($file->moveto, true)) {//The directory exists, so time to move the file
            return rename($file->fullFile, $file->moveto . '/' . $file->newname);
        }
        return false;
    }

    /**
     * Move the file to the archive directory and if successful, catalogue it
     * @param $file
     */
    public function MoveAndCatalogueFile($file){
        global $adb;
        if($this->TransferFileToArchive($file) == "true") {
            $query = "INSERT INTO custodian_omniscient.file_catalogue (filename, directory, size, new_filename, extension, new_extension, created_date, modified_date, num_rows, new_directory, created_time)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $adb->pquery($query, array($file->filename, $file->directory, $file->size, $file->newname, $file->extension, $file->newExtension,
                                       $file->createdDate, $file->modifiedDate, $file->numLines, $file->moveto, $file->createdTime), true);
        }
    }

    /**
     * Move file back to original directory from archive
     * @param array $id
     */
    public function MoveBackCataloguedFile(array $id){
        global $adb;
        $questions = generateQuestionMarks($id);
        $query = "SELECT * FROM custodian_omniscient.file_catalogue WHERE id IN ({$questions})";
        $result = $adb->pquery($query, arraY($id));
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
                if($this->ConfirmDirectory($x['directory'], true)) {//The directory exists, so time to move the file
                    $parse_file = $x['directory'] . '/' . $x['filename'];
                    $archive_file = $x['new_directory'] . '/' . $x['new_filename'];
                    if(file_exists($archive_file)){
                        $x = 1;
                        $new_archive_file = $archive_file;
                        while (file_exists($new_archive_file))
                        {
                            // get file extension
                            $extension = pathinfo($new_archive_file, PATHINFO_EXTENSION);

                            // get file's name
                            $filename = pathinfo($new_archive_file, PATHINFO_FILENAME);

                            // get file's directory
                            $directory=dirname($new_archive_file);

                            // add and combine the filename, iterator, extension
                            $new_filename = $filename . '-' . $x . '.' . $extension;

                            // add file name to the end of the path to place it in the new directory; the while loop will check it again
                            $new_archive_file = $directory . $new_filename;

                            $x++;
                        }
                        rename($new_archive_file, $parse_file);
                    }
                    else
                        rename($archive_file, $parse_file);
#                    $this->SetOriginalDate($parse_file, $x['created_date']);
                }
            }
        }
    }

    public function SetOriginalDate($file, $date){
        $d = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' 11:00:00');
        touch($file, $d->getTimestamp());
    }
}