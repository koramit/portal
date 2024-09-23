<?php

namespace App\Services;

use App\Models\Resources\Admission;
use App\Models\Resources\AttendingStaff;
use App\Models\Resources\Ward;

class AdmissionManager
{
    public function manage(array $data): void
    {
        $admission = Admission::query()
            ->where('an', $data['an'])
            ->first();

        $ward = $this->maintainWard($data['ward_name'], $data['ward_name_short'], $data['ward_number']);
        $staff = $this->maintainAttendingStaff($data['attending'], $data['attending_license_no'] ?? $data['attending']);

        if ($admission) {
            if ($admission->ward_id !== $ward->id) {
                $admission->admissionTransfers()->create([
                    'ward_id' => $ward->id,
                    'attending_staff_id' => $staff->id,
                ]);
            }

            $admission->ward_id = $ward->id;
            $admission->attending_staff_id = $staff->id;
            $admission->admitted_at = $data['admitted_at'];
            $admission->discharged_at = $data['discharged_at'];
            $admission->discharge_type_name = $data['discharge_type_name'];
            $admission->discharge_status_name = $data['discharge_status_name'];
            $admission->checked_at = now();
            $admission->save();

            return;
        }

        // create
        $admission = new Admission;
        $admission->hn = $data['hn'];
        $admission->an = $data['an'];
        $admission->name = $data['patient_name'];
        $admission->dob = $data['dob'];
        $admission->gender = $data['gender'] === 'female' ? 1 : 2;
        $admission->admitted_at = $data['admitted_at'];
        $admission->discharged_at = $data['discharged_at'];
        $admission->discharge_type_name = $data['discharge_type_name'];
        $admission->discharge_status_name = $data['discharge_status_name'];

        $ward = $this->maintainWard($data['ward_name'], $data['ward_name_short'], $data['ward_number']);
        $staff = $this->maintainAttendingStaff($data['attending'], $data['attending_license_no'] ?? $data['attending']);

        $admission->ward_id = $ward->id;
        $admission->attending_staff_id = $staff->id;
        $admission->checked_at = now();
        $admission->save();

        $admission->admissionTransfers()->create([
            'ward_id' => $ward->id,
            'attending_staff_id' => $staff->id,
        ]);
    }

    protected function maintainWard(string $name, string $shortName, string $number)
    {
        if ($ward = Ward::query()->where('name', $name)->first()) {
            return $ward;
        }

        return Ward::query()->create([
            'name' => $name,
            'name_short' => $shortName,
            'number' => $number,
        ]);
    }

    protected function maintainAttendingStaff($name, $licenseNo)
    {
        if ($staff = AttendingStaff::query()->where('license_no', $licenseNo)->first()) {
            return $staff;
        }

        return AttendingStaff::query()->create([
            'name' => $name,
            'license_no' => $licenseNo,
        ]);
    }
}
