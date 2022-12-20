<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $table = 'debt';

    /** 
     * Primary key.
     */
    protected $primaryKey = 'debtId';

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'custId', //fk Custumer
        'debtAmount', //vlrPag
        'status',
        'ticketId', //fk
        'checkoutAt', //dataBaixa
        'createdAt',
        'updatedAt'
    ];

    /**
     * Get the Costumer of debits.
     */
    public function costumer()
    {
        return $this->belongsTo(Costumer::class, 'custId');
    }

    /**
     * Get the Costumer of debits.
     */
    public function tikets()
    {
        return $this->hasMany(Ticket::class, 'ticketId', 'ticketId');
    }
}
