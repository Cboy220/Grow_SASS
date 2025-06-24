<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Database extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'databases';
    protected $primaryKey = 'database_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['database_id'];
    const CREATED_AT = 'database_created';
    const UPDATED_AT = 'database_updated';

}
