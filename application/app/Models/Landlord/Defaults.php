<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Defaults extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'defaults';
    protected $primaryKey = 'defaults_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['defaults_id'];
    const CREATED_AT = 'defaults_created';
    const UPDATED_AT = 'defaults_updated';

}
