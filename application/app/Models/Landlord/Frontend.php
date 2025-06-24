<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Frontend extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'frontend';
    protected $primaryKey = 'frontend_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['frontend_id'];
    const CREATED_AT = 'frontend_created';
    const UPDATED_AT = 'frontend_updated';

}
