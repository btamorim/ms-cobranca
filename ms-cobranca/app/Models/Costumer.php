<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costumer extends Model
{
    use HasFactory;

    protected $table = 'custumer';

    /** 
     * Primary key.
     */
    protected $primaryKey = 'custId';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'governmentId',
        'status',
        'createdAt'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * Get the debt 
     */
    public function debits()
    {
        return $this->hasMany(Debit::class, 'custId', 'custId');
    }

}
