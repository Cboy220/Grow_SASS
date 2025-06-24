<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'pages';
    protected $primaryKey = 'page_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['page_id'];
    const CREATED_AT = 'page_created';
    const UPDATED_AT = 'page_updated';

}
