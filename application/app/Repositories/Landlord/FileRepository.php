<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for files
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use Illuminate\Support\Facades\File;
use Log;

class FileRepository {

    /**
     * Process a file uploaded for the frontend as follows:
     *   1. Check that the folder and file exist in the 'temp' directory
     *   2. move the whole directory in the the 'storage/frontend' directory
     * @param array $data information payload
     * @return bool status outcome
     */
    public function processFrontendImage($data = []) {

        //sanity
        if ($data["directory"] == '' || $data["filename"] == '') {
            Log::error("required information is missing (directory or filename)", ['process' => '[processFrontendUpload]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //path to this directory in the temp folder
        $file_path = BASE_DIR . "/storage/temp/" . $data["directory"] . "/" . $data["filename"];
        $old_dir_path = BASE_DIR . "/storage/temp/" . $data["directory"];
        $new_dir_path = BASE_DIR . "/storage/frontend/" . $data["directory"];

        //validation: file exists
        if (!file_exists($file_path)) {
            Log::error("the uploaded file could not be found", ['process' => '[processFrontendUpload]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path]);
            return false;
        }

        //check if the file is an image
        if (!is_array(getimagesize($file_path))) {
            Log::error("the uploaded file is not an image", ['process' => '[processFrontendUpload]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path]);
            return false;
        }

        //move directory
        File::moveDirectory($old_dir_path, $new_dir_path, true);

        return true;
    }

}