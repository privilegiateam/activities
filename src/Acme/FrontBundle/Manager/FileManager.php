<?php

namespace Acme\OutchiBundle\Manager;

class FileManager
{

    public function uploadFile($FILES, $path, $fileName)
    {
            $ext = array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            );

        $extension = $FILES['form']['type']['imageFile']['file'];
        $filesize = $FILES['form']['size']['imageFile']['file'] / (1024 * 1024);

        $pathAbsoluteName = $this->getAbsolutPathFile($path, $fileName);
        $tmpName = $this->getTmpFile($_FILES);

        if (in_array($extension, $ext) && 2 > $filesize) {
            if (move_uploaded_file($tmpName, $pathAbsoluteName)) {
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }

    }

    public function getNameOfFile($id, $FILES)
    {
        $fileType = str_replace("image/", "", $FILES['form']['type']['imageFile']['file']);
        $fileName = md5($id.'-'.$_FILES['form']['name']['imageFile']['file']) . '.' . $fileType;
        return $fileName;
    }


    public function getTmpFile($FILES)
    {
        $tmp_name = $FILES["form"]["tmp_name"]['imageFile']['file'];
        return $tmp_name;
    }


    /*  Upload PDF file    */
    public function uploadPDF($FILES, $path, $fileName)
    {

        $ext = array(
            'pdf' => 'application/pdf',
        );

        $extension = $FILES['form']['type']['tarifFile']['file'];
        $filesize = $FILES['form']['size']['tarifFile']['file'] / (1024 * 1024);

        $pathAbsoluteName = $this->getAbsolutPathFile($path, $fileName);
        $tmpName = $this->getTmpFilePDF($_FILES);
        if ($extension == "application/pdf" && 5 > $filesize) {

            if (move_uploaded_file($tmpName, $pathAbsoluteName)) {
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    public function getNameOfFilePDF($id, $FILES)
    {
        //$fileType = str_replace("image/", "", $FILES['form']['type']['tarifFile']['file']);
        $fileType = 'pdf';
        $fileName = md5($id.'-'.$_FILES['form']['name']['tarifFile']['file']) . '.' . $fileType;
        return $fileName;
    }
    public function getTmpFilePDF($FILES)
    {
        $tmp_name = $FILES["form"]["tmp_name"]['tarifFile']['file'];
        return $tmp_name;
    }
    

    /*  Upload Media file    */
    public function uploadMedia($FILES, $path, $fileName)
    {
        $ext = array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        );

        $extension = $FILES['medias']['type'];
        $filesize = $FILES['medias']['size'] / (1024 * 1024);

        $pathAbsoluteName = $this->getAbsolutPathFile($path, $fileName);
        $tmpName = $this->getTmpFileMedia($_FILES);

        if (in_array($extension, $ext) && 2 > $filesize) {
            if (move_uploaded_file($tmpName, $pathAbsoluteName)) {
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    public function getNameOfFileMedia($id, $FILES)
    {

        $fileType = str_replace("image/", "", $FILES['medias']['type']);
        $fileName = md5($id.'-'.$_FILES['medias']['name']) . '.' . $fileType;
        return $fileName;
    }
    public function getTmpFileMedia($FILES)
    {
        $tmp_name = $FILES['medias']['tmp_name'];
        return $tmp_name;
    }


    public function getAbsolutPathFile($path, $fileName)
    {
        $pathAbsoluteName = __DIR__ . '/../../../../web/' .  $path  . '/' . $fileName;
        return $pathAbsoluteName;
    }
}