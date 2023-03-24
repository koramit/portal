<?php

namespace App\APIs;

use App\Traits\CurlExecutable;
use Illuminate\Support\Facades\Cache;

class PatientAPI
{
    use CurlExecutable;

    protected array $patient;

    protected array $serverError = [
        'ok' => false,
        'status' => 500,
        'error' => 'server',
        'message' => 'Server Error',
    ];

    public function getPatient(string $hn, bool $withSensitiveInfo): array
    {
        $functionName = 'SearchPatientDataDescriptionTypeExcludeD'; // return alive and dead patient
        $action = 'http://tempuri.org/'.$functionName;

        if (($response = $this->executeCurl($this->composeSOAP($functionName, 'hn', $hn), $action, config('simrs.patient_url'))) === false) {
            return $this->serverError;
        }

        $xml = simplexml_load_string($response);
        $namespaces = $xml->getNamespaces(true);
        $response = $xml->children($namespaces['soap'])
            ->Body
            ->children($namespaces[''])
            ->SearchPatientDataDescriptionTypeExcludeDResponse
            ->SearchPatientDataDescriptionTypeExcludeDResult
            ->children($namespaces['diffgr'])
            ->diffgram
            ->children()
            ->Result
            ->children()
            ->PatResult
            ->children();

        $data = (array) $response;

        if (($reply = $this->noResult($data['return_code'])) !== false) {
            return $reply;
        }

        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = null;
            } else {
                $data[$key] = trim($value);
            }
        }

        $patient = [
            'ok' => true,
            'found' => true,
            'alive' => $this->patientAlive($hn),
            'hn' => $hn,
            'patient_name' => ($data['title'] ?? '').' '.($data['patient_firstname'] ?? '').' '.($data['patient_surname'] ?? ''),
            'title' => $data['title'] ?? null,
            'first_name' => $data['patient_firstname'] ?? null,
            'middle_name' => $data['patient_middlename'] ?? null,
            'last_name' => $data['patient_surname'] ?? null,
            'document_id' => $data['identity_card_no'] ?? null,
            'dob' => $data['birth_date'] ? $this->castDateFormat($data['birth_date']) : null,
            'gender' => ($data['sex'] ?? '') === 'หญิง' ? 'female' : 'male',
            'race' => $data['race_name'] ?? null,
            'nation' => $data['nationality_name'] ?? null,
            'tel_no' => str_replace('-', '', (($data['present_tele_no'] ?? '').' '.($data['mobile_no'] ?? ''))),
            'spouse' => $data['marrier_name'] ?? null,
            'address' => $data['present_address'] ?? null,
            'subdistrict' => $data['tambon'] ?? null,
            'district' => $data['amphur'] ?? null,
            'postcode' => $data['zipcode'] ?? null,
            'province' => $data['province'] ?? null,
            'insurance_name' => $data['patient_type_name'] ?? null,
            'marital_status' => $data['marriage_stat_name'] ?? null,
            'alternative_contact' => (($data['connected_relation_name'] ?? '').' '.($data['connected_name'] ?? '').' '.($data['connected_tele_no'] ?? '')),
        ];

        if ($withSensitiveInfo) {
            return $patient;
        }

        $patient['document_id'] = null;
        $patient['race'] = null;
        $patient['nation'] = null;
        $patient['tel_no'] = null;
        $patient['spouse'] = null;
        $patient['address'] = null;
        $patient['subdistrict'] = null;
        $patient['district'] = null;
        $patient['postcode'] = null;
        $patient['province'] = null;
        $patient['insurance_name'] = null;
        $patient['marital_status'] = null;
        $patient['alternative_contact'] = null;

        return $patient;
    }

    public function getAdmission(int|string $an, bool $withSensitiveInfo): array
    {
        $functionName = 'SearchInpatientAllByAN';
        $action = 'http://tempuri.org/'.$functionName;

        if (($response = $this->executeCurl($this->composeSOAP($functionName, 'AN', $an, 'UserName'), $action, config('simrs.patient_url'))) === false) {
            return $this->serverError;
        }

        $xml = simplexml_load_string($response);
        $namespaces = $xml->getNamespaces(true);
        $response = $xml->children($namespaces['soap'])
            ->Body
            ->children($namespaces[''])
            ->SearchInpatientAllByANResponse
            ->SearchInpatientAllByANResult
            ->children($namespaces['diffgr'])
            ->diffgram
            ->children()
            ->Result
            ->children()
            ->InpatientResult
            ->children();

        $data = (array) $response;

        if (($reply = $this->noResult($data['return_code'])) !== false) {
            return $reply;
        }

        return $this->handleAdmitData($data, $withSensitiveInfo);
    }

    public function getPatientAdmissions(int|string $hn, bool $withSensitiveInfo): array
    {
        $functionName = 'SearchInpatientAll';
        $action = 'http://tempuri.org/'.$functionName;

        if (($response = $this->executeCurl($this->composeSOAP($functionName, 'HN', $hn, 'UserName'), $action, config('simrs.patient_url'))) === false) {
            return $this->serverError;
        }

        $xml = simplexml_load_string($response);
        $namespaces = $xml->getNamespaces(true);
        $response = $xml->children($namespaces['soap'])
            ->Body
            ->children($namespaces[''])
            ->SearchInpatientAllResponse
            ->SearchInpatientAllResult
            ->children($namespaces['diffgr'])
            ->diffgram
            ->children()
            ->Result
            ->children();

        $admissions = ((array) $response)['InpatientResult'];
        $admissions = is_array($admissions) ?
            array_map(function ($admission) {
                return (array) $admission;
            }, $admissions) :
            [(array) $admissions];

        if (($reply = $this->noResult($admissions[0]['return_code'])) !== false) {
            return $reply;
        }

        $admissions = array_map(function ($admission) use ($withSensitiveInfo) {
            $record = $this->handleAdmitData($admission, $withSensitiveInfo);
            unset($record['patient']);

            return $record;
        }, $admissions);

        return [
            'ok' => true,
            'found' => true,
            'patient' => $this->patient,
            'admissions' => $admissions,
        ];
    }

    public function getPatientRecentlyAdmit(int|string $hn, bool $withSensitiveInfo)
    {
        $cacheKey = 'recently-admit-'.$hn;
        if ($admission = Cache::get($cacheKey)) {
            return $admission;
        }

        $admissions = $this->getPatientAdmissions($hn, $withSensitiveInfo);
        if ($admissions['found'] ?? false) {
            $admission = collect($admissions['admissions'])->last();
            $admission['patient'] = $admissions['patient'];
            Cache::put($cacheKey, $admission, 600);

            return $admission;
        } else {
            $admissions['patient'] = $this->getPatient($hn, $withSensitiveInfo);

            return $admissions;
        }
    }

    protected function composeSOAP(string $functionName, string $keyName, string $keyValue, string $userTag = 'Username'): string
    {
        $SOAPStr = '<?xml version="1.0" encoding="utf-8"?>';
        $SOAPStr .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
        $SOAPStr .= '<soap:Body>';
        $SOAPStr .= '<'.$functionName.' xmlns="http://tempuri.org/">';
        $SOAPStr .= '<'.$keyName.'>'.$keyValue.'</'.$keyName.'>';
        $SOAPStr .= '<'.$userTag.'>'.config('simrs.api_username').'</'.$userTag.'>';
        $SOAPStr .= '<Password>'.config('simrs.api_password').'</Password>';
        $SOAPStr .= '<RequestComputerName></RequestComputerName>';
        $SOAPStr .= '</'.$functionName.'>';
        $SOAPStr .= '</soap:Body>';
        $SOAPStr .= '</soap:Envelope>';

        return $SOAPStr;
    }

    protected function noResult(string $code): bool|array
    {
        $reply = ['ok' => true, 'found' => false];
        switch ($code) {
            case '0': // found
                return false;
            case '3': // dead
                return false;
            case '1':
                $reply['message'] = 'not found';

                return $reply;
            case '2':
                $reply['message'] = 'cancel';

                return $reply;
            case '4':
                $reply['message'] = 'error';

                return $reply;
            case '9':
                $reply['message'] = 'not allowed';

                return $reply;
            default:
                return false;
        }
    }

    protected function patientAlive(string $hn): ?bool
    {
        $functionName = 'SearchPatientData'; // return alive patient only
        $action = 'http://tempuri.org/'.$functionName;

        if (($response = $this->executeCurl($this->composeSOAP($functionName, 'hn', $hn), $action, config('simrs.patient_url'))) === false) {
            return null;
        }

        $xml = simplexml_load_string($response);
        $namespaces = $xml->getNamespaces(true);
        $response = $xml->children($namespaces['soap'])
            ->Body
            ->children($namespaces[''])
            ->SearchPatientDataResponse
            ->SearchPatientDataResult
            ->children($namespaces['diffgr'])
            ->diffgram
            ->children()
            ->Result
            ->children()
            ->PatResult
            ->children();

        $data = (array) $response;

        return match ($data['return_code']) {
            '0' => true,
            '3' => false,
            default => null,
        };
    }

    protected function castDateFormat($value): ?string
    {
        if (strlen($value) == 8) {
            $yy = substr($value, 0, 4) - 543;
            $mm = substr($value, 4, 2) == '00' ? '07' : substr($value, 4, 2);
            $dd = substr($value, 6, 2) == '00' ? '15' : substr($value, 6, 2);

            return $yy.'-'.$mm.'-'.$dd;
        }

        return null;
    }

    protected function handleAdmitData($data, $withSensitiveInfo): array
    {
        $this->patient = $this->getPatient($data['hn'], $withSensitiveInfo);
        if (! $this->patient['ok']) {
            return $this->patient;
        }

        $result = [
            'ok' => true,
            'found' => true,
            'alive' => $data['return_code'] === '0',
            'hn' => $data['hn'],
            'an' => $data['an'],
            'dob' => $this->patient['dob'],
            'gender' => $this->patient['gender'],
            'patient_name' => $this->patient['patient_name'],
            'patient' => $this->patient,
        ];

        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $result[$key] = null;
            } else {
                $result[$key] = trim($value);
            }
        }

        $result['ward_name'] = $result['ward_name'] ?? null;
        $result['ward_name_short'] = $result['ward_brief_name'] ?? null;
        $result['admitted_at'] = $this->castSiMRDateTimeFormat($result['admission_date'], $result['admission_time']);
        $result['discharged_at'] = $this->castSiMRDateTimeFormat($result['discharge_date'], $result['discharge_time']);
        $result['attending'] = $result['doctor_name'] ?? null;
        $result['attending_license_no'] = $result['refer_doctor_code'] ?? null;
        $result['discharge_type'] = $result['discharge_type_name'] ?? null;
        $result['discharge_status'] = $result['discharge_status_name'] ?? null;
        $result['department'] = $result['patient_dept_name'] ?? null;
        $result['division'] = $result['patient_sub_dept_name'] ?? null;

        return $result;
    }

    protected function castSiMRDateTimeFormat($datePart, $timePart): ?string
    {
        if (strlen($datePart) !== 8) {
            return null;
        }

        $yy = substr($datePart, 0, 4) - 543;
        $mm = substr($datePart, 4, 2) == '00' ? '07' : substr($datePart, 4, 2);
        $dd = substr($datePart, 6, 2) == '00' ? '15' : substr($datePart, 6, 2);
        $timePart = str_pad($timePart, 4, '0', STR_PAD_LEFT);
        $timePart = substr($timePart, 0, 2).':'.substr($timePart, 2);

        return $yy.'-'.$mm.'-'.$dd.' '.$timePart.':00';
    }
}
