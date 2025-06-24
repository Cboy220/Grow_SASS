<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Feedback;
use App\Models\FeedbackQuery;

class FeedbackDetail extends Model
{
    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'feedback_detail_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['feedback_detail_id'];
    const CREATED_AT = 'feedback_detail_created';
    const UPDATED_AT = 'feedback_detail_updated';

     // FeedbackDetail belongs to Feedback
     public function feedback()
     {
         return $this->belongsTo(Feedback::class, 'feedback_id');
     }
 
     // FeedbackDetail belongs to FeedbackQuery
     public function feedbackQuery()
     {
         return $this->belongsTo(FeedbackQuery::class, 'feedback_query_id');
     }
}
