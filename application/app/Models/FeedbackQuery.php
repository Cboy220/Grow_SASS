<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackQuery extends Model
{
    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'feedback_queries';
    protected $primaryKey = 'feedback_query_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['feedback_query_id'];
    const CREATED_AT = 'feedback_query_created';
    const UPDATED_AT = 'feedback_query_updated';
    
    public function feedbackDetail() {
        return $this->hasOne('App\Models\FeebackQuery','feedback_query_id');
    }
}
