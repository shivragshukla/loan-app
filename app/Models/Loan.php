<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the term that owns the loan.
     */
    public function term()
    {
        return $this->belongsTo(LoanTerm::class, 'loan_term_id');
    }

    /**
     * Get the user that owns the loan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the manager that approved/rejected the loan.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the repayments for the loan.
     */
    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    /**
     * Get the penalties for the loan.
     */
    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }
}
