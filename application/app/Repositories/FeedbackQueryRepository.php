<?php

namespace App\Repositories;

use App\Models\FeedbackQuery;

class FeedbackQueryRepository
{
    public function all()
    {
        return FeedbackQuery::all();
    }

    public function find(int $id)
    {
        return FeedbackQuery::findOrFail($id);
    }

    public function create(array $data)
    {
        return FeedbackQuery::create($data);
    }

    public function update(int $id, array $data)
    {
        $query = FeedbackQuery::findOrFail($id);
        $query->update($data);
        return $query;
    }

    public function delete(int $id)
    {
        return FeedbackQuery::destroy($id);
    }

    public function getByType(int $type)
    {
        return FeedbackQuery::where('type', $type)->get();
    }
}