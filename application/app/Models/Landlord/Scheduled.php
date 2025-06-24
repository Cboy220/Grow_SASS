<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Scheduled extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'scheduled';
    protected $primaryKey = 'scheduled_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['scheduled_id'];
    const CREATED_AT = 'scheduled_created';
    const UPDATED_AT = 'scheduled_updated';

}