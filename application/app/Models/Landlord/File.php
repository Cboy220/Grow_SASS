<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class File extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'files';
    protected $primaryKey = 'file_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['file_id'];
    const CREATED_AT = 'file_created';
    const UPDATED_AT = 'file_updated';

}
