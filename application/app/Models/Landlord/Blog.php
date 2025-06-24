<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'blogbar';
    protected $primaryKey = 'blog_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['blog_id'];
    const CREATED_AT = 'blog_created';
    const UPDATED_AT = 'blog_updated';

}
