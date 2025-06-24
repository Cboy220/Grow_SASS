<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'subscriptionbar';
    protected $primaryKey = 'subscription_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['subscription_id'];
    const CREATED_AT = 'subscription_created';
    const UPDATED_AT = 'subscription_updated';

}
