<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Updatelog extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'updateslog';
    protected $primaryKey = 'updateslog_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['updateslog_id'];
    const CREATED_AT = 'updateslog_created';
    const UPDATED_AT = 'updateslog_updated';

}
