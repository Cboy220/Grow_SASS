<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FeedbackDetail;
use App\Models\User;

class Feedback extends Model
{/**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

     protected $table = "feedbacks";
    protected $primaryKey = 'feedback_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['feedback_id'];
    const CREATED_AT = 'feedback_created';
    const UPDATED_AT = 'feedback_updated';

     // Feedback belongs to a User (client)
    public function user()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Feedback has many FeedbackDetails
    public function feedbackDetails()
    {
        return $this->hasMany(FeedbackDetail::class, 'feedback_id');
    }
}
