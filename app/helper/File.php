<?php
namespace app\helper;

/**
 *
 */
class File
{
    /**
     * * FileHandler::Upload()
     * ? Upload File ke Temporary directory
     */
    public static function Upload()
    {
        $temp = $_FILES['file']['tmp_name']; // ? Temporary file name
        $filename = $_FILES['file']['name']; // ? File name
        $filepath = "upload/temp/{$filename}"; // ? File path
        $location = DOC_ROOT . $filepath; // ? File location
        $source = BASE_URL . "/{$filepath}"; // ? Source

        list($type, $extension) = explode('/', mime_content_type($temp)); // ? Get mime type

        // * Allowed type & extension
        $allowed_type = ['video', 'image'];
        $allowed_extension = ['pdf', 'xml'];

        // TODO: Get accepted file type & extension
        list($accept_type, $accept_extension) = explode(
            '/',
            str_replace('kml', 'xml', $_POST['accept']),
        );

        $upload = false;

        // TODO: Cek extension
        if (
            in_array($extension, $allowed_extension) &&
            $accept_extension == $extension
        ) {
            // ? Extension cocok
            $upload = true;
        } else {
            // ! extension tidak cocok
            // TODO: Cek type
            if (
                in_array($type, $allowed_type) &&
                in_array($accept_type, $allowed_type)
            ) {
                // ? Type cocok
                $upload = true;
            }
        }

        // TODO: Cek Upload OK
        if ($upload) {
            // ? Upload OK
            // TODO: Upload File
            if (move_uploaded_file($temp, $location)) {
                // ? Upload Success
                Flasher::setFlash(
                    'Your file is temporarily uploaded.',
                    'File',
                    'warning',
                );
            } else {
                // ! Upload Gagal
                Flasher::setFlash('Nothing file uploaded.', 'File', 'error');
            }
        } else {
            // ! File tidak cocok
            Flasher::setFlash('File is not allowed.', 'File', 'error');
        }

        // * Mengembalikan nilai result
        $result = [];
        $result['alert'] = Flasher::getFlash();
        $result['location'] = $location;
        $result['filename'] = $filename;
        $result['source'] = $source;
        $result['filetype'] = $type;
        echo json_encode($result);
    }

    /**
     * * FileHandler::MoveFromTemp($filedir, $filename)
     * ? Pindah file dari temporary directory
     * @param string filedir
     * ? Directory tujuan
     * @param string filedir
     * ? Nama file
     * @param bool $timestamp
     */
    public static function moveFromTemp(
        string $filedir,
        string $filename,
        bool $timestamp = false,
        bool $clear = false
    ) {
        // TODO: Cek file ada di temporary directory
        if (file_exists(DOC_ROOT . "upload/temp/{$filename}")) {
            // ? file ada
            // TODO: Parsing directory
            $dir = [];
            foreach (explode('/', $filedir) as $folder) {
                $dir[] = $folder;
                $new_dir = DOC_ROOT . 'upload/' . implode('/', $dir);
                // echo $new_dir . "<br>";
                // TODO: Membuat directory baru jika belum ada
                self::createWritableFolder($new_dir);
            }

            $time = $timestamp == true ? date('ymdHis', time()) . '_' : '';

            if ($clear) {
                // var_dump($clear);
                self::clearOldFile($filedir);
            }

            $filename = !empty($time) ? $time . $filename : $filename;

            // TODO: Pindah file
            rename(
                DOC_ROOT . "upload/temp/{$filename}",
                DOC_ROOT . "upload/{$filedir}/{$filename}",
            );

            return $filename;
            // if (file_exists(UPLOAD_DIR . "{$filedir}/{$time}{$filename}")) {
            //   echo UPLOAD_DIR . "{$filedir}/{$time}{$filename}" . 'moved <br>';
            // } else {
            //   echo UPLOAD_DIR . "{$filedir}/{$time}{$filename}" . 'not moved <br>';
            // }
        }
        return false;
    }

    public static function clearOldFile(string $filedir)
    {
        // var_dump($filedir);
        $files = glob(DOC_ROOT . "upload/{$filedir}/*"); // ?  get all file names
        // var_dump($files);
        foreach ($files as $file) {
            // ?  iterate files
            // var_dump($file);
            if (is_file($file)) {
                @unlink($file);
            } // TODO: delete file
        }
    }

    public static function createWritableFolder(string $folder)
    {
        // $folder = $folder;
        // if ($folder != '.' && $folder != '/') {
        //     self::createWritableFolder(dirname($folder));
        // }
        // TODO: Cek folder exist
        if (file_exists($folder)) {
            // ! Folder exist
            return is_writable($folder);
        }

        // TODO: Buat folder baru
        @mkdir($folder, 0777, true);
    }

    public static function checkUploadedFile()
    {
        $filepath = $_POST['filepath'];
        list($file, $status) = self::checkFileExist($filepath);

        $result = [];
        $result['status'] = $status;
        $result['file'] = $file;
        echo json_encode($result);
        exit();
    }

    public static function checkFileExist($filepath)
    {
        list($filename) = array_reverse(explode('/', $filepath));

        $status = 404;
        $file = '';
        if (file_exists(DOC_ROOT . "upload/{$filepath}")) {
            $status = 200;
            $file = DOC_ROOT . "upload/{$filepath}";
        } else {
            if (file_exists(DOC_ROOT . "upload/temp/{$filename}")) {
                $status = 200;
                $file = DOC_ROOT . "upload/temp/{$filename}";
            }
        }

        return [$file, $status];
    }
}

?>
