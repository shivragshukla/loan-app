<?php

namespace App\Http\Controllers;

use App\Models\{
    Loan,
    Status,
    Penalty,
    Repayment,
    PenaltyStatus
};
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RepaymentController extends Controller
{
    private $amount = 0;
    private $penaltyRefId = "";

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function repayment(Request $request, string $loanRefId)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.1',
            'comment' => 'sometimes',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $loan = Loan::where('ref_id', $loanRefId)->where('user_id', auth()->id())->first();
        if (!$loan) {
            return response()->json(['message' => 'No record found'], 422);
        }

        if ( $loan->status !== Status::APPROVED_KEY) {
            return response()->json(['message' => 'Invalid repay for Loan Ref-Id: ' . $loan->ref_id], 422);
        }

        $this->amount = $request->amount;
        return $this->calculateInterest($loan);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function calculateInterest(Loan $loan)
    {
        $term = $loan->term;

        $P = $loan->amount;
        $r = (int)$term->rate / 100;
        $t = 1; // Not Sure, Sorry to check the SI

        $num_of_weeks = 0;

        switch ($term->name) {
            case 'Short Term':
                $num_of_weeks = 52;
                break;

            case 'Intermediate Term':
                $num_of_weeks = 260;
                break;
            
            case 'Long Term':
                $num_of_weeks = 520;
                break;
        }

        $AP = $P * (1 + $r * $t); // Total amount customer needs to pay
        $pay_per_week = round($AP / $num_of_weeks);


        // Sum of previous repays
        $sum_of_replay = Repayment::where('loan_id', $loan->id)
            ->where('user_id', auth()->id())
            ->sum('amount');


        // Check the Penalty
        $penalties = Penalty::where('loan_id', $loan->id)
            ->where('user_id', auth()->id())
            ->where('status', PenaltyStatus::OPEN_KEY)
            ->get();

        $sum_of_penalty = $penalties->map( function( $penalty ) {
            return $penalty->amount;
        })->sum();

        // Replay will be added to Penalty
        if ( $sum_of_penalty > 0 ) {
            $penalties->map(function(Penalty $penalty) {
                if ( $this->amount < $penalty->amount ) {
                    $penalty->amount = $penalty->amount - $this->amount;
                    $penalty->save();
                    $this->amount = 0;
                    $this->setPenaltyRefId($penalty->ref_id);
                    return false;
                }
                $penalty->status = PenaltyStatus::CLOSED_KEY;
                $penalty->save();
                $this->amount = $this->amount - $penalty->amount;
                $this->setPenaltyRefId($penalty->ref_id);
            });
        }

        if ($this->penaltyRefId) {
            return response()->json(['message' => 'Your repay adjusted in penalties, Please submit the repay amount again to ovide the Penalty for this week, find the Penalty Ref-Id : ' . ( $this->penaltyRefId )], 422);
        }

        $today_needs_to_pay = $sum_of_replay + $this->amount;

        // If Repay is greator then your Principle + Interest
        if ( $today_needs_to_pay > $AP ) {
            return response()->json(['message' => 'You don\'t need to pay that much, Please enter the amount : ' . ( $today_needs_to_pay - $AP )], 422);
        }

        // Insufficient repay amount
        if ($this->amount <  $pay_per_week) {
            return response()->json([
                'message' => 'Repay Decline! Insufficient repay amount, Your weekly Repay amount is ' . $pay_per_week . ' , Please add rest amount ' . ($pay_per_week - $this->amount)
            ], 422);
        }

        // Future repay is available, wait for next week to repay
        if (!(Carbon::create($loan->next_repay_date) <= now()->endOfWeek()) ) {
            return response()->json(['message' => 'Future repay is available, wait for next week to repay'], 422);
        }

         // Past | Penalty for delay
        if ((Carbon::create($loan->next_repay_date) < now()->startOfWeek()) ) {
            $penalty = Penalty::create([
                'ref_id' => Str::uuid(),
                'user_id' => auth()->id(),
                'amount' => $pay_per_week,
                'status' => PenaltyStatus::CLOSED_KEY,
                'comment' => 'Due in last week <' . $loan->next_repay_date . '> repay causes penalty',
            ]);

            return response()->json([
                'message' => 'Penalty! Your repay deducted & added in penalties, Please submit the repay amount again to avoide the Penalty for this week, find the Penalty Ref-Id : ' . ( $this->penaltyRefId )
            ], 422);
        }

        // This Week | Add valid repay
        if (Carbon::create($loan->next_repay_date)
            ->between(
                now()->startOfWeek(), 
                now()->endOfWeek()
            ) 
        ) {
            $repayment = Repayment::create([
                'ref_id' => Str::uuid(),
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'amount' => $pay_per_week
            ]);

            $loan->next_repay_date = Carbon::create($loan->next_repay_date)->addWeeks(+1);
            $loan->save();

            return response()->json([
                'message' => 'Sucess! Repay done for loan Ref-Id ' . $loan->ref_id . ', Ref-Id for repay : ' . $repayment->ref_id]
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setPenaltyRefId (string $penaltyRefId)
    {
        $this->penaltyRefId = $this->penaltyRefId 
            ? $this->penaltyRefId . ', ' . $penaltyRefId
            : $this->penaltyRefId;
    }
}
