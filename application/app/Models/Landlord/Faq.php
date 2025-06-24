<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'faqbar';
    protected $primaryKey = 'faq_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['faq_id'];
    const CREATED_AT = 'faq_created';
    const UPDATED_AT = 'faq_updated';

}
