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
        'debtId',
        'debtDueDate',
        'amount',
        'createdAt',
        'expirationDate',
        'bankId',
        'barCode',
        'statusId',
        'paidAt',
        'paidBy'
    ];

    /**
     * Get the customer of debits.
     */
    public function debit()
    {
        return $this->belongsTo(Debit::class, 'debtId');
        
    }

}
