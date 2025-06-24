<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class ProofOfPayment extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'proof_of_payments';
    protected $primaryKey = 'proof_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['proof_id'];
    const CREATED_AT = 'proof_created';
    const UPDATED_AT = 'proof_updated';

}
