<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientExpectation extends Model
{
    // Table name (optional if it follows Laravel naming conventions)
    protected $table = 'client_expectations';

    // Primary key
    protected $primaryKey = 'client_expectation_id';

    // Disable auto-incrementing if needed (default is true)
    public $incrementing = true;

    // If your primary key is not an integer, set $keyType (default is 'int')
    protected $keyType = 'int';

    // Timestamps are custom named, so disable default timestamps
    public $timestamps = false;

    // Use custom timestamp fields and tell Laravel to manage them manually
    protected $dates = ['expectation_created', 'expectation_updated'];

    // Allow mass assignment on these fields
    protected $fillable = [
        'client_id',
        'title',
        'content',
        'weight',
        'due_date',
        'status',
        'expectation_created',
        'expectation_updated',
    ];

    public function clients() {
        return $this->belongsTo('App\Modal\Client', 'client_id', 'client_id');
    }

}
