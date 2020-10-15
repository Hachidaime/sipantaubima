<?php

use app\controllers\Controller;
use app\helper\File;

/**
 *
 */
class FileController extends Controller
{
    public function upload()
    {
        File::upload();
    }
}
