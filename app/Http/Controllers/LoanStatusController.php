<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Loan;
use App\Models\Status;
use Illuminate\Http\Request;

class LoanStatusController extends Controller
{
    /**
     * Approve the loan of User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(
        Request $request, 
        int $userId, 
        string $loanRefId
    ) {
        $data = ['status' => Status::APPROVED_KEY];
        if ($request->comment) {
            $data['comment'] = $request->comment; 
        }

        return $this->changeStatus($data, $userId, $loanRefId);
    }

    /**
     * Reject the loan of User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(
        Request $request, 
        int $userId, 
        string $loanRefId
    ) {
        $data = ['status' => Status::REJECTED_KEY];
        if ($request->comment) {
            $data['comment'] = $request->comment; 
        }

        return $this->changeStatus($data, $userId, $loanRefId);
    }

    /**
     * Change the status of the loan.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(
        array $data, 
        int $userId, 
        string $loanRefId
    ) {
        try {
            $loan = Loan::where('ref_id', $loanRefId)->where('user_id', $userId)->first();
            $status = $data['status'] === Status::APPROVED_KEY
                ? Status::APPROVED_KEY
                : Status::REJECTED_KEY;

            if (!$loan) {
                return response()->json(['status' => 'No record found'], 422);
            }
            if ( $loan->status !== Status::PENDING_KEY) {
                return response()->json(['status' => 'Invalid loan for approval/reject'], 422);
            }

            $updatedData = [
                'status' => $status,
                'manager_id' => auth()->id(),
            ];

            if ( $data['status'] === Status::APPROVED_KEY ) {
                $updatedData['start_repay_date'] = now()->addWeeks(+1);
                $updatedData['next_repay_date'] = now()->addWeeks(+1);
            }
            
            $loan->update(array_merge($data, $updatedData));

            return response()->json([
                'message' => 'Loan successfully changed',
                'ref_id' => $loan->ref_id,
            ], 201);

        } catch (Exception $e) {
            return response()->json($e->errors(), 422);
        }
        return response()->json(['status' => 'No record found']);
    }
}
