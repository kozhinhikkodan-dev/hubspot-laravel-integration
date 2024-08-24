<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'website',
        'lifecyclestage',
        'jobtitle',
        'hubspot_object_id',
    ];

    public function scopeFindByObjectId($query, $objectId)
    {
        return $query->where('hubspot_object_id', $objectId)->first();
    }
}
