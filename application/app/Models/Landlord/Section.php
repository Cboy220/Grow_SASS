<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Section extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'sectionbar';
    protected $primaryKey = 'section_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['section_id'];
    const CREATED_AT = 'section_created';
    const UPDATED_AT = 'section_updated';

}
