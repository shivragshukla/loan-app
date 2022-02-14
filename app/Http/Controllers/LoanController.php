<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Loan;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Requests\ApplyLoanRequest;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_term_id' => 'required|exists:loan_terms,id',
            'amount' => 'required|numeric',
            'comment' => 'sometimes',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $loan = Loan::create(array_merge(
            $validator->validated(),
            [
                'ref_id' => Str::uuid(),
                'user_id' => auth()->id(),
                'status' => Status::PENDING_KEY,
            ]
        ));

        return response()->json([
            'message' => 'Loan successfully applied',
            'ref_id' => $loan->ref_id
        ], 201);
    }

    /**
     * Get the all loans of authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllLoans()
    {
        $loans = auth()->user()->loans;

        if (auth()->user()->hasRole(['manager', 'super-admin'])) {
            $loans = Loan::get();
        }

        return response()->json(
            $loans->map( function( $loan ) {
                return [
                    'ref_id' => $loan->ref_id,
                    'amount' => $loan->amount,
                    'status' => $loan->status,
                ];
            })
        );
    }

    /**
     * Get the summary loan of authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary( string $loanRefId )
    {
        try {
            $loan = Loan::with(['term', 'repayments', 'penalties'])
                ->where('ref_id', $loanRefId);
            if (auth()->user()->hasRole(['customer'])) {
                $loan = $loan->where('user_id', auth()->id());
            }
            $loan = $loan->first();
            if ( $loan ) {
                return response()->json([
                    'message' => 'Loan details',
                    'data' => $loan,
                ], 201);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 422);
        }
        return response()->json(['status' => 'No record found']);
    }
}
