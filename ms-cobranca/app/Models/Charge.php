<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    // protected $table = 'debit';

    /** 
     * Primary key.
     */
    // protected $primaryKey = 'debtId';

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'governmentId',
        'email',
        'debtAmount',
        'debtDueDate',
        'debtId'
    ];


}
