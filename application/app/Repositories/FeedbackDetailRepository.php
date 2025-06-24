<?php

namespace App\Repositories;

use App\Models\FeedbackDetail;

class FeedbackDetailRepository
{
    public function all()
    {
        return FeedbackDetail::all();
    }

    public function find(int $id)
    {
        return FeedbackDetail::findOrFail($id);
    }

    public function create(array $data)
    {
        return FeedbackDetail::create($data);
    }

    public function update(int $id, array $data)
    {
        $detail = FeedbackDetail::findOrFail($id);
        $detail->update($data);
        return $detail;
    }

    public function delete(int $id)
    {
        return FeedbackDetail::destroy($id);
    }

    public function getByFeedbackId(int $feedbackId)
    {
        return FeedbackDetail::where('feedback_id', $feedbackId)->with('feedbackQuery')->get();
    }
}