<?php

/** ---------------------------------------------------------------------------------------------------------------
 * The purpose of this middleware it to set run updates for the landlord side of the SaaS application
 * It does not run the tenant side of updates (which are done via cronjob)
 *
 *
 *
 * @package    Grow CRM
 * @author     NextLoop
 * @revised    10 May 2023
 *--------------------------------------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Landlord;
use Closure;
use DB;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Updates {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //do not run this for SETUP path
        if (env('SETUP_STATUS') != 'COMPLETED') {
            return $next($request);
        }

        if (request()->ajax()) {
            return $next($request);
        }

        //get a list of all the sql files in the updates folder
        $path = BASE_DIR . "/updates-saas";
        $files = File::files($path);
        $updated = false;
        foreach ($files as $file) {

            //file details
            $filename = $file->getFilename();
            $extension = $file->getExtension();
            $filepath = $file->getPathname();

            //runtime function name (e.g. updating_1_13)
            $function_name = str_replace('.sql', '', $filename);
            $function_name = str_replace('.', '_', "landlord_" . $function_name);

            //intial file (version 1.1) that must be run on its own
            if ($filename == '1.1.sql') {
                $updated = $this->initialUpdate($path, $file);
                continue;
            }

            //run the routine if this is an sql file
            if ($extension == 'sql') {

                Log::alert("an sql file was found. will now check it it has not been executed before", ['process' => '[updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

                if (\App\Models\Landlord\Update::On('landlord')->Where('update_mysql_filename', $filename)->doesntExist()) {

                    Log::alert("the mysql file ($filename) has not previously been executed. Will now execute it", ['process' => '[updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

                    //execute the SQL
                    try {
                        //db
                        DB::connection('landlord')->unprepared(file_get_contents($filepath));

                        //save record
                        $record = new \App\Models\Landlord\Update();
                        $record->setConnection('landlord');
                        $record->update_mysql_filename = $filename;
                        $record->save();

                        Log::alert("the mysql file ($filename) executed ok - will now delete it", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    } catch (Exception $e) {
                        Log::error("the mysql file ($filename) could not be executed. error: " . $e->getMessage(), ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    }

                    //delete the file
                    try {
                        unlink($path . "/$filename");
                    } catch (Exception $e) {
                        Log::error("the mysql file ($filename) could not be deleted. error: " . $e->getMessage(), ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    }

                    /** -------------------------------------------------------------------------
                     * Run any updating function, if it exists
                     * as found in the file - application/updating/landlord_1_1.php ...etc
                     * -------------------------------------------------------------------------*/
                    Log::alert("checking if a runtime function: [$function_name()] exists", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    if (function_exists($function_name)) {

                        Log::alert("runtime function: [$function_name()] was found. It will now be executed", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

                        try {
                            call_user_func($function_name);
                            Log::alert("the runtime function: [$function_name()] was executed", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        } catch (Exception $e) {
                            Log::critical("updating runtime function: [$function_name()] could not be executed. Error: " . $e->getMessage(), ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        }
                    }

                    //finish
                    Log::alert("updating of mysql file ($filename) has been completed", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                } else {

                    Log::alert("the file is not an sql file. will now delete it", ['process' => '[updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

                    try {
                        unlink($path . "/$filename");
                        Log::alert("found a non mysql file ($filename) inside the updates folder. Will try to delete it", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    } catch (Exception $e) {
                        Log::error("the file ($filename) could not be deleted", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    }
                }

                //we have done an update
                $updated = true;
            }
        }

        //finish - clear cache
        if ($updated) {
            try {
                \Artisan::call('route:clear');
                \Artisan::call('config:clear');
                \Artisan::call('view:clear');
            } catch (Exception $e) {
                Log::error("the mysql file ($filename) could not be deleted", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        return $next($request);
    }

    /**
     * Inital update which creates the 'updates' database table which is needed for future updates
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function initialUpdate($path, $file) {

        Log::alert("the sql file is v1.1 will now execute it.", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //file details
        $filename = $file->getFilename();
        $extension = $file->getExtension();
        $filepath = $file->getPathname();

        //execute the SQL (only for CRM with version 1.0)
        if (config('system.settings_version') == '1.0') {
            try {
                //db
                DB::unprepared(file_get_contents($filepath));
                Log::info("the mysql file ($filename) executed ok - will now delete it", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            } catch (Exception $e) {
                Log::error("the mysql file ($filename) could not be executed. error: " . $e->getMessage(), ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        } else {
            Log::alert("the system has already been updated beyond this sql version.", ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //delete the file
        try {
            unlink($path . "/$filename");
        } catch (Exception $e) {
            Log::error("the mysql file ($filename) could not be deleted. error: " . $e->getMessage(), ['process' => '[landlord-updates]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        return true;

    }

}