<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'name',
        'phone',
        'id_division',
        'position',
    ];

    public $incrementing = false; // Disable auto-incrementing ID
    protected $keyType = 'string'; // Specify that the primary key is a string

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'id_division');
    }
}
