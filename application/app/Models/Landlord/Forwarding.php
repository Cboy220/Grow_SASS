<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Forwarding extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'forwarding';
    protected $primaryKey = 'forwarding_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['forwarding_id'];
    const CREATED_AT = 'forwarding_created';
    const UPDATED_AT = 'forwarding_updated';

}
