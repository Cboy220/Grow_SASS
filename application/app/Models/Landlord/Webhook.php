<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'webhookbar';
    protected $primaryKey = 'webhooks_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['webhooks_id'];
    const CREATED_AT = 'webhooks_created';
    const UPDATED_AT = 'webhooks_updated';

}
