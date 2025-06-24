<?php

namespace App\Repositories;

use App\Models\ClientExpectation;
use Carbon\Carbon;

class ClientExpectationRepository
{
    protected $model;

    public function __construct(ClientExpectation $model)
    {
        $this->model = $model;
    }

    /** Create a new expectation */
    public function create(array $data): ClientExpectation
    {
        return $this->model->create($data);
    }

    /** Get expectation by ID */
    public function find(int $id): ?ClientExpectation
    {
        return $this->model->find($id);
    }

    /** Update an expectation by ID */
    public function update(int $id, array $data): bool
    {
        $expectation = $this->find($id);
        if (!$expectation) {
            return false;
        }
        return $expectation->update($data);
    }

    /** Delete an expectation by ID */
    public function delete(int $id): bool
    {
        $expectation = $this->find($id);
        if (!$expectation) {
            return false;
        }
        return $expectation->delete();
    }

    /** Get all expectations for a given client */
    public function getByClientId(int $clientId)
    {
        return $this->model->where('client_id', $clientId)->get();
    }

    /** Calculate % of fulfilled expectations for a client */
    public function getFulfillmentPercentage(int $clientId): float
    {
        $total = $this->model->where('client_id', $clientId)->count();
        if ($total === 0) {
            return 0.0;
        }
        $completed = $this->model
            ->where('client_id', $clientId)
            ->where('status', 'fulfilled')
            ->count();

        return round(($completed / $total) * 100, 2);
    }

    /** Get all overdue (pending + past due_date) expectations for a client */
    public function getOverdueExpectations(int $clientId)
    {
        return $this->model
            ->where('client_id', $clientId)
            ->where('status', 'pending')
            ->where('due_date', '<', Carbon::today())
            ->get();
    }

    public function filterClientExpectation($clientId, $perPage = 10, $search = null)
    {
        $query = ClientExpectation::where('client_id', $clientId);

        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        return $query->orderBy('due_date', 'asc')->paginate($perPage);
    }

    public function getFilteredStats($clientId, $search = null)
    {
        $now = now();

        $query = ClientExpectation::where('client_id', $clientId);
        if ($search) {
            $query->where('title', 'like', "%$search%");
        }

        $all = $query->get();

        // Sum of all weights
        $totalWeight = $all->sum('weight');

        // Weight of fulfilled expectations
        $fulfilledWeight = $all->where('status', 'fulfilled')->sum('weight');

        // Weight of overdue expectations (status = pending && due_date < now)
        $overdueWeight = $all->filter(fn($e) =>
            $e->status === 'pending' && $e->due_date < $now
        )->sum('weight');

        // Remaining are pending
        $pendingWeight = $totalWeight - $fulfilledWeight - $overdueWeight;

        // Avoid divide-by-zero
        $fulfilledPercent = $totalWeight > 0 ? round(($fulfilledWeight / $totalWeight) * 100) : 0;
        $pendingPercent = $totalWeight > 0 ? round(($pendingWeight / $totalWeight) * 100) : 0;
        $overduePercent = $totalWeight > 0 ? 100 - $fulfilledPercent - $pendingPercent : 0;

        return [
            'total' => $totalWeight,
            'fulfilled' => $fulfilledWeight,
            'pending' => $pendingWeight,
            'overdue' => $overdueWeight,
            'fulfilledPercent' => $fulfilledPercent,
            'pendingPercent' => $pendingPercent,
            'overduePercent' => $overduePercent,
        ];
    }

    public function toggleStatus($expectationId): ?ClientExpectation
    {
        $expectation = ClientExpectation::find($expectationId);

        if (!$expectation) return null;

        $expectation->status = $expectation->status === 'fulfilled' ? 'pending' : 'fulfilled';
        $expectation->save();

        return $expectation;
    }
}
