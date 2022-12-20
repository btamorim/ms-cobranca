<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';

    /** 
     * Primary key.
     */
    protected $primaryKey = 'ticketId';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'debtId', //fk
        'debtDueDate', //dtVencimento
        'amount',
        'createdAt',
        'expirationDate', //vencimento
        'bankId',
        'barCode',
        'statusId',
        'paidAt',
        'paidBy'
    ];

    /**
     * Get the Costumer of debits.
     */
    public function debit()
    {
        return $this->belongsTo(Debit::class, 'debtId');
        
    }

}
