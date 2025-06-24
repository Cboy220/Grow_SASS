<?php

namespace App\Repositories;

use App\Models\Feedback;
use Illuminate\Support\Facades\DB;

class FeedbackRepository
{
    public function all()
    {
        return Feedback::all();
    }

    public function find(int $id)
    {
        return Feedback::findOrFail($id);
    }

    public function create(array $data)
    {
        return Feedback::create($data);
    }

    public function update(int $id, array $data)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update($data);
        return $feedback;
    }

    public function delete(int $id)
    {
        return Feedback::destroy($id);
    }

    public function getWithDetails(int $id)
    {
        return Feedback::with([
            'feedbackDetails.feedbackQuery',
            'user'
        ])->findOrFail($id);
    }

    public function getByClientId(int $clientId)
    {
        return Feedback::where('client_id', $clientId)->get();
    }

    public function getAllWithDetailsAndQueries()
    {
        return Feedback::with([
            'feedbackDetails.feedbackQuery', // eager load nested relationship
            'user' // optional, if you need user too
        ])->get();
    }



    public function getFeedbackSummariesForClient($clientId = null, $search = null, $perPage = 5)
    {
        $clientId = $clientId ?? auth()->user()->clientid;

        $query = DB::table('feedbacks as f')
            ->join('feedback_details as d', 'f.feedback_id', '=', 'd.feedback_id')
            ->join('feedback_queries as q', 'd.feedback_query_id', '=', 'q.feedback_query_id')
            ->select(
                'f.feedback_id',
                'f.feedback_date',
                'f.comment',
                DB::raw('ROUND(SUM(q.weight * d.value) * 10 / SUM(q.weight * q.range), 2) as total_marks')
            )
            ->groupBy('f.feedback_id', 'f.feedback_date', 'f.comment')
            ->orderBy('f.feedback_date', 'desc');

        if ($clientId !== 0) {
            $query->where('f.client_id', $clientId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('f.feedback_date', 'like', "%{$search}%")
                ->orWhere('f.comment', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * this fuction is to update feedback data and feedback detail one.
     * @return Feedback
     */
    public function updateFeedbackWithDetails(int $feedbackId, array $data)
    {
        DB::beginTransaction();

        try {
            // 1. Update main feedback
            $feedback = Feedback::findOrFail($feedbackId);
            $feedback->comment = $data['comment'] ?? $feedback->comment;
            $feedback->feedback_date = $data['feedback_date'] ?? $feedback->feedback_date;
            $feedback->save();

            // 2. Update each feedback_detail
            if (!empty($data['details']) && is_array($data['details'])) {
                foreach ($data['details'] as $detail) {
                    FeedbackDetail::where('feedback_id', $feedbackId)
                        ->where('feedback_query_id', $detail['feedback_query_id'])
                        ->update([
                            'value' => $detail['value'],
                            'feedback_detail_updated' => now(),
                        ]);
                }
            }

            DB::commit();
            return $feedback;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getLatestFeedbackSummaries($clientId = 0, $limit = 5)
    {
        $query = DB::table('feedbacks as f')
            ->join('feedback_details as d', 'f.feedback_id', '=', 'd.feedback_id')
            ->join('feedback_queries as q', 'd.feedback_query_id', '=', 'q.feedback_query_id')
            ->select(
                'f.feedback_id',
                'f.feedback_date',
                'f.comment',
                DB::raw('ROUND(SUM(q.weight * d.value) / SUM(q.weight), 2) as total_marks')
            )
            ->groupBy('f.feedback_id', 'f.feedback_date', 'f.comment')
            ->orderBy('f.feedback_date', 'desc');
            if($clientId > 0) {
                $query->where('f.feedback_id', $clientId);
            }
        return $query->limit($limit)->get();
    }

    public function deleteFeedbackWithDetails($feedbackId)
    {
        return DB::transaction(function () use ($feedbackId) {
            // Delete related feedback_details first
            DB::table('feedback_details')->where('feedback_id', $feedbackId)->delete();

            // Then delete the main feedback record
            return DB::table('feedbacks')->where('feedback_id', $feedbackId)->delete();
        });
    }

}