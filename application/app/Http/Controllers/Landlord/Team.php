<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Team\CreateResponse;
use App\Http\Responses\Landlord\Team\DestroyResponse;
use App\Http\Responses\Landlord\Team\EditResponse;
use App\Http\Responses\Landlord\Team\IndexResponse;
use App\Http\Responses\Landlord\Team\StoreResponse;
use App\Http\Responses\Landlord\Team\UpdateResponse;
use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Team extends Controller {

    public function __construct(
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('primaryAdmin')->only([
            'create',
            'store',
            'edit',
            'delete',
            'update',
        ]);

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        $users = \App\Models\User::orderBy('first_name', 'ASC')->get();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'users' => $users,
        ];

        //show the form
        return new IndexResponse($payload);
    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
        ];

        //show the form
        return new CreateResponse($payload);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [
            'first_name.required' => __('lang.first_name') . '-' . __('lang.is_required'),
            'last_name.required' => __('lang.last_name') . '-' . __('lang.is_required'),
            'email.required' => __('lang.email') . '-' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => [
                'required',
            ],
            'last_name' => [
                'required',
            ],
            'email' => [
                'required',
                Rule::unique('users', 'email'),
            ],

        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //password
        $password = random_string(8);

        //store record
        $user = new \App\Models\User();
        $user->first_name = request('first_name');
        $user->last_name = request('last_name');
        $user->email = request('email');
        $user->password = Hash::make($password);
        $user->save();

        /** ----------------------------------------------
         * send email to user
         * ----------------------------------------------*/
        $data = [
            'password' => $password,
        ];
        $mail = new \App\Mail\Landlord\Admin\TeamWelcome($user, $data, []);
        $mail->build();

        //refreshed
        $users = \App\Models\User::Where('id', $user->id)->get();

        //payload
        $payload = [
            'users' => $users,
        ];

        //render
        return new StoreResponse($payload);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the foo
        if (!$user = \App\Models\User::Where('id', $id)->first()) {
            abort(404);
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'user' => $user,
        ];

        //show the form
        return new EditResponse($payload);
    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2])) {
            abort(409, 'Demo Mode: You cannot edit the main demo users. You can create new ones for testing');
        }

        if (!$user = \App\Models\User::Where('id', $id)->first()) {
            abort(404);
        }

        //custom error messages
        $messages = [
            'first_name.required' => __('lang.first_name') . '-' . __('lang.is_required'),
            'last_name.required' => __('lang.last_name') . '-' . __('lang.is_required'),
            'email.required' => __('lang.email') . '-' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => [
                'required',
            ],
            'last_name' => [
                'required',
            ],
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore($id, 'id'),
            ],

        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //store record
        $user->first_name = request('first_name');
        $user->last_name = request('last_name');
        $user->email = request('email');
        $user->save();

        //refreshed
        $users = \App\Models\User::Where('id', $user->id)->get();

        //payload
        $payload = [
            'users' => $users,
            'id' => $id,
        ];

        //render
        return new UpdateResponse($payload);

    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2])) {
            abort(409, 'Demo Mode: You cannot delete the main demo users. You can create new ones for testing');
        }

        if (!$user = \App\Models\User::Where('id', $id)->first()) {
            abort(404);
        }

        //ensure it is not the primary admin
        if ($user->primary_admin == 'yes') {
            abort(409, __('lang.you_cannot_delete_primary_admin'));
        }

        $user->delete();

        //payload
        $payload = [
            'id' => $id,
        ];

        return new DestroyResponse($payload);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.team'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.team'),
            'heading' => __('lang.team'),
            'page' => 'team',
            'mainmenu_team' => 'active',
        ];

        //return
        return $page;
    }
}