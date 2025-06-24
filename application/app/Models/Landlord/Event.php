<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $primaryKey = 'event_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['event_id'];
    const CREATED_AT = 'event_created';
    const UPDATED_AT = 'event_updated';

}
