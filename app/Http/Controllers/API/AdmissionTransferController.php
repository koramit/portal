<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Resources\Admission;
use App\Models\Resources\AttendingStaff;
use App\Models\Resources\Ward;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class AdmissionTransferController extends Controller
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'an' => ['required', 'digits:8'],
        ]);

        $admission = Admission::query()
            ->with([
                'admissionTransfers' => fn ($query) => $query
                    ->addSelect([
                        'ward_name' => Ward::query()
                            ->select('name')
                            ->whereColumn('id', 'admission_transfers.ward_id'),
                    ])->addSelect([
                            'ward_ref_id' => Ward::query()
                                ->select('number')
                                ->whereColumn('id', 'admission_transfers.ward_id'),
                        ])->addSelect([
                            'attending_staff_name' => AttendingStaff::query()
                                ->select('name')
                                ->whereColumn('id', 'admission_transfers.attending_staff_id'),
                        ])->oldest(),
            ])
            ->where('an', $validated['an'])
            ->first();

        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            (bool) $admission,
        );

        if (! $admission) {
            return ['ok' => true, 'found' => false];
        }

        $transfers = $admission->admissionTransfers->map(function ($transfer) {
            return [
                'ward_name' => $transfer->ward_name,
                'ward_ref_id' => $transfer->ward_ref_id,
                'created_at' => $transfer->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return [
            'ok' => true,
            'found' => true,
            'discharge_status' => $admission->discharge_status_name,
            'discharge_type' => $admission->discharge_type_name,
            'admitted_at' => $admission->admitted_at->format('Y-m-d H:i:s'),
            'discharged_at' => $admission->discharged_at?->format('Y-m-d H:i:s'),
            'transfers' => $transfers,
            'transfer_count' => $transfers->count(),
        ];
    }
}
