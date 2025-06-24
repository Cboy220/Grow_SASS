<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Package extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'packagebar';
    protected $primaryKey = 'package_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['package_id'];
    const CREATED_AT = 'package_created';
    const UPDATED_AT = 'package_updated';

}
