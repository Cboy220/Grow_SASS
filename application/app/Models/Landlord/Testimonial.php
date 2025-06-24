<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'testimonialbar';
    protected $primaryKey = 'testimonial_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['testimonial_id'];
    const CREATED_AT = 'testimonial_created';
    const UPDATED_AT = 'testimonial_updated';

}
