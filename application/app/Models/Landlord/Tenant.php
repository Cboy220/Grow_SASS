<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'tenantbar';
    protected $primaryKey = 'tenant_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['tenant_id'];
    const CREATED_AT = 'tenant_created';
    const UPDATED_AT = 'tenant_updated';

}
