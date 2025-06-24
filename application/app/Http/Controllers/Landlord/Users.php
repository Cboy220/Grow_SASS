<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\User\EditAvatarResponse;
use App\Repositories\Landlord\AttachmentRepository;

class Users extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');
    }

    /**
     * update the logged in users menu preference
     * @return blade view | ajax view
     */
    public function preferenceMenu() {

        $user = \App\Models\User::Where('id', auth()->id())->first();

        //validate
        if (!request()->filled('leftmenu_position')) {
            return;
        }

        //save
        if (in_array(request('leftmenu_position'), ['collapsed', 'open'])) {
            $user->pref_leftmenu_position = request('leftmenu_position');
            $user->save();
        }
    }

    /**
     * Show the form for editing the specified users avatar
     * @return \Illuminate\Http\Response
     */
    public function avatar() {

        //reponse payload
        $payload = [];

        //response
        return new EditAvatarResponse($payload);
    }

    /**
     * Update the specified user in storage.
     * @param object AttachmentRepository instance of the repository
     * @param int $id user id
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(AttachmentRepository $attachmentrepo) {

        //validate input
        $data = [
            'directory' => request('avatar_directory'),
            'filename' => request('avatar_filename'),
        ];

        //process and save to db
        if (!$attachmentrepo->processAvatar($data)) {
            abort(409);
        }

        //update avatar
        \App\Models\User::where('id', auth()->id())
            ->update([
                'avatar_directory' => request('avatar_directory'),
                'avatar_filename' => request('avatar_filename'),
            ]);

        //redirect to home
        $jsondata['redirect_url'] = url('app-admin/home');

        //ajax response
        return response()->json($jsondata);
    }

}