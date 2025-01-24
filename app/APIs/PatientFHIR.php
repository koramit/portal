<?php

namespace App\APIs;

use App\Traits\PatientSensitiveDataRemovable;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientFHIR
{
    use PatientSensitiveDataRemovable;

    public function getPatient(string $keyName, string $keyValue, bool $raw, bool $withSensitiveInfo): array
    {
        $identifier = match ($keyName) {
            'hn' => "http://si.mahidol.ac.th/eHIS/MP_PATIENT|$keyValue",
            'cid' => "https://terms.sil-th.org/id/th-cid|$keyValue",
            'passport' => "https://terms.sil-th.org/id/passport-number|$keyValue",
            default => null,
        };

        try {
            $response = Http::withOptions(['verify' => false])
                ->get(config('si_dsl.proxy_url'), [
                    'url' => config('si_dsl.patient_endpoint'),
                    'headers' => config('si_dsl.headers'),
                    'body' => ['identifier' => $identifier],
                ]);
        } catch (Exception $e) {
            Log::error('patient-fhir@'.$e->getMessage());

            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }

        if ($response->status() !== 200) {
            return [
                'ok' => true,
                'found' => false,
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $response = $response->json();
        if ($response['total'] !== 1) {
            return [
                'ok' => true,
                'found' => false,
            ];
        }

        if ($raw && $withSensitiveInfo) {
            return [
                'ok' => true,
                'found' => true,
                'response' => $response,
            ];
        }

        $resource = $response['entry'][0]['resource'];

        $dateDeath = null;
        if (array_key_exists('deceasedDateTime', $resource)) {
            $alive = false;
            $dateDeath = $resource['deceasedDateTime'];
        } elseif (array_key_exists('deceasedBoolean', $resource)) {
            $alive = !$resource['deceasedBoolean'];
        } else {
            $alive = true;
        }

        $hn = null;
        $thaiId = null;
        $passport = null;
        foreach ($resource['identifier'] as $identifier) {
            if ($identifier['system'] === 'http://si.mahidol.ac.th/eHIS/MP_PATIENT') {
                $hn = $identifier['value'];
            }
            if ($identifier['system'] === 'https://terms.sil-th.org/id/th-cid') {
                $thaiId = $identifier['value'];
            }
            if ($identifier['system'] === 'https://terms.sil-th.org/id/passport-number') {
                $passport = $identifier['value'];
            }
        }

        $title = null;
        $firstName = null;
        $lastName = null;
        $patientName = null;
        foreach ($resource['name'] as $name) {
            foreach ($name['extension'] as $extension) {
                if ($extension['valueCode'] === 'th') {
                    $title = trim(implode(' ', $name['prefix']));
                    $firstName = trim(implode(' ', $name['given']));
                    $lastName = trim($name['family']);
                    $patientName = trim($name['text']);
                    break;
                }
            }
        }
        $middleName = null;
        $firstNameSplit = explode(' ', $firstName);
        if (count($firstNameSplit) > 1) {
            $middleName = array_pop($firstNameSplit);
        }

        $race = null;
        $nation = null;
        foreach ($resource['extension'] as $extension) {
            if ($extension['url'] === 'http://hl7.org/fhir/StructureDefinition/patient-nationality') {
                foreach ($extension['extension'] as $item) {
                    foreach ($item['valueCodeableConcept']['coding'] as $code) {
                        if ($code['system'] === 'http://si.mahidol.ac.th/eHIS/MP_PATIENT') {
                            $nation = $code['display'];
                        }
                        break;
                    }
                    if ($nation) {
                        break;
                    }
                }
            }

            if ($extension['url'] === 'https://dsl.org/fhir/StructureDefinition/Patient-race') {
                foreach ($extension['valueCodeableConcept']['coding'] as $code) {
                    if ($code['system'] === 'http://si.mahidol.ac.th/eHIS/MP_RACE') {
                        $race = $code['display'];
                        break;
                    }
                }
            }
        }

        $telNo = null;
        foreach ($resource['telecom'] as $telecom) {
            if ($telecom['system'] === 'phone') {
                $telNo = $telecom['value'];
            }
        }

        $presentAddress = $resource['address'][0] ?? [];
        $address = trim(implode(' ', $presentAddress['line'] ?? []));
        $subdistrict =  $presentAddress['city'] ?? null;
        $district =  $presentAddress['district'] ?? null;
        $province =  $presentAddress['state'] ?? null;
        $postcode =  $presentAddress['postalCode'] ?? null;

        $maritalStatus =  null;
        if (array_key_exists('maritalStatus', $resource)) {
            foreach ($resource['maritalStatus']['coding'] as $code) {
                if ($code['system'] === 'http://si.mahidol.ac.th/eHIS/MP_PATIENT') {
                    $maritalStatus = $code['display'];
                }
            }
        }

        $altContact = [];
        if (array_key_exists('contact', $resource)) {
            foreach ($resource['contact'] as $contact) {
                $contactText = null;
                $relate = null;
                foreach ($contact['relationship'] as $relationship) {
                    foreach ($relationship['coding'] as $code) {
                        if ($code['system'] === 'http://si.mahidol.ac.th/eHIS/MP_PAT_REL_CONTACTS') {
                            $relate = $code['display'];
                            break;
                        }
                    }
                    if ($relate) {
                        break;
                    }
                }
                $contactText .= $relate;
                $contactText .= (' ' . $contact['name']['text'] ?? '');
                $contactText .= (' ' . $contact['address']['text'] ?? '');
                $contactText .= (' ' . $contact['telecom']['value'] ?? '');
                $altContact[] = trim($contactText);
            }
        }
        $altContact = trim(implode(' | ', $altContact));

        $photoDataUrl = [];
        if (array_key_exists('photo', $resource)) {
            foreach ($resource['photo'] as $photo) {
                $photoDataUrl[] = 'data:'.$photo['contentType'].';base64,'.$photo['data'];
            }
        }

        $patient = [
            'ok' => true,
            'found' => true,
            'alive' => $alive,
            'date_death' => $dateDeath,
            'hn' => (int) $hn,
            'patient_name' => $patientName,
            'title' => $title,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'document_id' => $thaiId ?? $passport,
            'dob' => $resource['birthDate'],
            'gender' => $resource['gender'],
            'race' => $race,
            'nation' => $nation,
            'tel_no' => $telNo,
            'spouse' => null,
            'address' => $address,
            'subdistrict' => $subdistrict,
            'district' => $district,
            'postcode' => $postcode,
            'province' => $province,
            'insurance_name' => null,
            'marital_status' => $maritalStatus ?: null,
            'alternative_contact' => $altContact ?: null,
            'photo' => $photoDataUrl,
        ];

        if ($withSensitiveInfo) {
            return $patient;
        }

        $patient['age'] = $patient['dob']
            ? (int) abs(now()->diffInYears($patient['dob']))
            : null;

        $this->removeSensitiveData($patient);

        return $patient;
    }
}
