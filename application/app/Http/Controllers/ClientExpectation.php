<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\ClientExpectationRepository;
use App\Http\Responses\ClientExpectation\IndexResponse;
use App\Http\Responses\ClientExpectation\CreateResponse;
use App\Http\Responses\ClientExpectation\EditResponse;
use App\Http\Responses\ClientExpectation\DeleteResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ClientExpectation extends Controller
{
    protected $expectationrepo;

    public function __construct(ClientExpectationRepository $expectationrepo)
    {
        $this->expectationrepo = $expectationrepo;
    }

    /** List all expectations for a client */
    public function index(Request $request): IndexResponse
    {
        $clientId = auth()->user()->clientid;
        $expectations = $this->expectationrepo->getByClientId($clientId);
        $page = $this->pageSettings('index');
        $payload = [
            'page'=> $page,
            'expectations'=> $page,
        ];

        return new IndexResponse($payload);
    }

    /** Show a single expectation */
    public function show(int $id): JsonResponse
    {
        $expectation = $this->expectationrepo->find($id);
        if (!$expectation) {
            return response()->json(['message' => 'Expectation not found'], 404);
        }
        return response()->json($expectation);
    }

    public function create(Request $request, $id): CreateResponse
    {
        $payload = [
            'page' => $this->pageSettings('create'),
            'id' => $id
        ];

        return new CreateResponse($payload);
    }
    
    public function edit(Request $request, $id): EditResponse
    {
        $payload = [
            'page' => $this->pageSettings('edit'),
            'id' => $id,
            'expectation'=> $this->expectationrepo->find($id)
        ];

        return new EditResponse($payload);
    }
    
    public function delete(Request $request, $id): DeleteResponse
    {
        $payload = [
            'page' => $this->pageSettings('delete'),
            'id' => $id,
        ];

        return new DeleteResponse($payload);
    }

    /** Create a new expectation */
    public function store(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'status' => ['required', Rule::in(['pending', 'fulfilled'])],
        ]);
        $data['client_id'] = $id;

        $expectation = $this->expectationrepo->create($data);

        return response()->json(['success' => true, 'expectation' => $expectation, 'message' => __('lang.request_has_been_completed')], 201);
    }

    /** Update an existing expectation */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|nullable',
            'weight' => 'sometimes|numeric|min:0',
            'due_date' => 'sometimes|date',
            'status' => [ 'sometimes', Rule::in(['pending', 'fulfilled']) ],
        ]);

        $updated = $this->expectationrepo->update($id, $data);
        if (!$updated) {
            return response()->json(['message' => 'Expectation not found'], 404);
        }

        return response()->json(['message' => 'Expectation updated successfully']);
    }

    /** Delete an expectation */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->expectationrepo->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Expectation not found'], 404);
        }
        return response()->json(['message' => __('lang.request_has_been_completed')]);
    }

    public function pageSettings($section, $data = []) 
    {
        $page = [
        'crumbs' => [
            __('lang.expectativas'),
            __('lang.expectativas'),
        ],
        'crumbs_special_class' => 'main-pages-crumbs',
        'page' => 'expectation',
        'meta_title' => __('lang.expectativas'),
        'heading' => __('lang.expectativas'),
        'mainmenu_expectation' => 'active',
        ];
    
        $page += ['status' => $section];

        switch ($section) {
            case 'create': {
                $page += [
                    'path' => 'pages/client/components/modals/add-edit-expectation',
                    'action_route' => 'expectation.create',
                    'modal_title' => __('lang.create_new_expectation')
                ];
                break;
            }
            case 'edit': {
                $page += [
                    'path' => 'pages/client/components/modals/add-edit-expectation',
                    'action_route' => 'expectation.update',
                    'modal_title' => __('lang.edit_expectation')
                ];
                break;
            }
            case 'delete': {
                $page += [
                    'path' => 'pages/feedback/components/modals/delete-confirm',
                    'action_route' => 'expectation.delete',
                    'modal_title' => __('lang.edit_expectation')
                ];
                break;
            }
        }
        return $page;
    }

    public function fetchExpectation(Request $request, int $clientId) {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 5);

        $expectations = $this->expectationrepo->filterClientExpectation($clientId, $perPage, $search);
        $stats = $this->expectationrepo->getFilteredStats($clientId, $search);

        return response()->json([
            'success'=> true,
            'statsHtml' => view('pages.client.components.misc.expectation-stats', compact('stats'))->render(),
            'listHtml'  => view('pages.client.components.misc.expectation-list', compact('expectations'))->render(),
        ]);
    }

    public function fetchForClient(Request $request) {
        $clientId = auth()->user()->clientid;
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $expectations = $this->expectationrepo->filterClientExpectation($clientId, $perPage, $search);
        $stats = $this->expectationrepo->getFilteredStats($clientId, $search);

        return response()->json([
            'success'=> true,
            'statsHtml' => view('pages.expectation.components.misc.expectation-stats', compact('stats'))->render(),
            'listHtml'  => view('pages.expectation.components.misc.expectation-list', compact('expectations'))->render(),
        ]);
    }

    public function toggleCheck(Request $request, int $expectationId) {
        $expectation = $this->expectationrepo->toggleStatus($expectationId);

        return response()->json([
            'success'=> true,
            'expectation'=> $expectation,
            'message'=> __('lang.request_has_been_completed')
        ]);
    }
}
