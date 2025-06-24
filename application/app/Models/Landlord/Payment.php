<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'paymentbar';
    protected $primaryKey = 'payment_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['payment_id'];
    const CREATED_AT = 'payment_created';
    const UPDATED_AT = 'payment_updated';

}
