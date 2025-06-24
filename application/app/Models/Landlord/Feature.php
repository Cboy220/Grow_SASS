<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'featurebar';
    protected $primaryKey = 'feature_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['feature_id'];
    const CREATED_AT = 'feature_created';
    const UPDATED_AT = 'feature_updated';

}
