<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        dd($_FILES, $_GET, $request->all());
        $file = $_FILES['upload']['tmp_name'];
        $file_name = $_FILES['upload']['name'];
        $file_name_array = explode(".", $file_name);
        $extension = end($file_name_array);
        $new_image_name = rand() . '.' . $extension;
        if (!file_exists(storage_path('/app/public/upload'))) {
            mkdir(storage_path('/app/public/upload'), 0777);
        }
        $allowed_extension = array("jpg", "gif", "png");
        if(in_array($extension, $allowed_extension))
        {
            move_uploaded_file($file, storage_path('/app/public/upload') . $new_image_name);
        }
    }
}
