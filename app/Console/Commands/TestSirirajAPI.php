<?php

namespace App\Console\Commands;

use App\APIs\EncounterAPI;
use App\APIs\ItemizeAPI;
use App\APIs\LabAPI;
use App\APIs\PatientAllergyAPI;
use App\APIs\PatientAppointmentAPI;
use App\APIs\PatientFHIR;
use App\APIs\PatientMedicationAPI;
use Illuminate\Console\Command;

class TestSirirajAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:siriraj-api {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Siriraj API connectivity';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        [$encounterHn, $allergyHn, $appointmentHn, $medicationHn] = explode('-', $this->argument('data'));

        // Encounter API
        $encounter = (new EncounterAPI)(['hn' => $encounterHn]);
        $this->feedback('Encounter', $encounter);

        // Itemize API
        $itemize = (new ItemizeAPI)->getItem('drug', 'folic');
        $this->feedback('Itemize', $itemize);

        // Patient Allergy API
        $allergy = (new PatientAllergyAPI)($allergyHn);
        $this->feedback('Patient Allergy', $allergy);

        // Patient Appointment API
        $appointment = (new PatientAppointmentAPI)('hn', $appointmentHn, '2025-01-01', '2025-12-31');
        $this->feedback('Patient Appointment', $appointment);

        // Patient FHIR API
        $patientFhir = (new PatientFHIR)->getPatient('hn', $encounterHn, false, false);
        $this->feedback('Patient FHIR', $patientFhir);

        // Patient Medication API
        $medications = (new PatientMedicationAPI)(['hn' => $medicationHn, 'category' => 'opd', 'date_start' => '2025-01-01', 'date_end' => '2025-12-31']);
        $this->feedback('Patient Medication', $medications);

        // Lap API
        $labs = (new LabAPI)->getLabRecentlyOrders($encounterHn);
        $this->feedback('Lab', $labs);
    }

    protected function feedback(string $apiName, array $result): void
    {
        if (! $result['ok']) {
            $this->error($apiName.' API Error');
        } else {
            $this->info($apiName.' API OK');
        }
    }
}
