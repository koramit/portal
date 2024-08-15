<?php

namespace App\APIs;

use App\Contracts\LabAPI as LabAPIContract;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LabAPI implements LabAPIContract
{
    private ?string $API_TOKEN;

    protected string $TOKEN_CACHE_KEY = 'si-lis-client-token';

    public function __construct()
    {
        if ($token = cache($this->TOKEN_CACHE_KEY)) {
            $this->API_TOKEN = $token;

            return;
        }

        try {
            $this->API_TOKEN = Http::asForm()
                ->withOptions(['verify' => false])
                ->retry(3, 200)
                ->post(config('si_lis.auth_url'), [
                    'client_id' => config('si_lis.id'),
                    'client_secret' => config('si_lis.secret'),
                    'grant_type' => 'client_credentials',
                ])->json()['access_token'];

            cache()->put($this->TOKEN_CACHE_KEY, $this->API_TOKEN, now()->addHour());
        } catch (Exception $e) {
            Log::error('get_si_lis_api_token@'.$e->getMessage());
            $this->API_TOKEN = null;
        }
    }

    public function getLabPendingReports(int|string $hn): array
    {
        $result = $this->makePost('/reports/checkReport', [
            'HN' => (string) $hn,
        ]);

        if ($result === null) {
            return ['ok' => false, 'serverError' => true];
        }

        if (! isset($result['status']) || ! isset($result['result'])) {
            return [
                'ok' => true,
                'found' => false,
            ];
        }

        $pendingReports = [];
        $recentlyReports = [];
        foreach ($result['result'] as $item) {
            $temp = [
                'lab_no' => $item['LAB_NO'],
                'ref_no' => $item['REF_NO'],
                'service_name' => $item['SERV_DESC'],
                'datetime_order' => $item['ORDER_DATE'] . ' ' . $item['ORDER_TIME'],
            ];
            if ($item['REPORT']) {
                $recentlyReports[] = $temp;
            } else {
                $pendingReports[] = $temp;
            }
        }

        return [
            'ok' => true,
            'found' => true,
            'status' => $result['status'],
            'pending_reports' => $pendingReports,
            'recently_reports' => $recentlyReports,
        ];
    }

    public function getLabRecentlyOrders(string $hn): array
    {
        try {
            $result = Http::withToken($this->API_TOKEN)
                ->withOptions(['verify' => false])
                ->retry(3, 200)
                ->get(config('si_lis.service_url').'/reports/'.$hn)
                ->json();
        } catch (Exception $e) {
            Log::error('recently-reports@'.$e->getMessage());

            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }

        if ($result === null) {
            return ['ok' => false, 'serverError' => true];
        }

        $orders = collect($result)->map(fn ($item) => [
                'lab_no' => $item['LAB_NO'],
                'ref_no' => $item['REF_NO'],
                'service_id' => $item['SERV_ID'],
                'service_name' => $item['SERV_DESC'],
                'datetime_order' => $item['ORDER_DATE'] . ' ' . $item['ORDER_TIME'],
                'datetime_report' => $item['REPORT_DATE'] && $item['REPORT_TIME']
                    ? $item['REPORT_DATE'] . ' ' . $item['REPORT_TIME']
                    : null,
            ])->values()->all();

        return [
            'ok' => true,
            'found' => (boolean) count($orders),
            'recently_orders' => $orders,
        ];
    }

    public function getLabFromRefNo(string $refNo): array
    {
        $result = $this->makePost('/reports/Detail', ['REF_NO' => $refNo]);

        if ($result === null) {
            return ['ok' => true, 'found' => false];
        }

        return [
            'ok' => true,
            'found' => true,
            'report' => [
                'service_id' => $result['SERV_ID'],
                'service_name' => $result['SERV_DESC'],
                'datetime_order' => $result['ORDER_DATE'] . ' ' . $result['ORDER_TIME'],
                'datetime_specimen_received' => $result['SPECIMEN_RECEIVED'] . ' ' . $result['SPECIMEN_RECEIVED_TIME'],
                'datetime_report' => $result['REPORT_DATE'] && $result['REPORT_TIME']
                    ? $result['REPORT_DATE'] . ' ' . $result['REPORT_TIME']
                    : null,
                'requester_id' => $result['REQUESTOR_ID'],
                'requester' => $result['REQUESTOR'],
                'note' => $result['NOTE'],
                'results' => collect($result['RESULT'])->map(fn ($item) => $this->labItemCast($item))->values()->all()
            ]
        ];
    }

    public function getLabFromServiceId(array $validated): array
    {
        $form = [
            'HN' => $validated['hn'],
            'GROUP_SERVICE_ID' => $validated['service_ids'],
            'GROUP' => !($validated['latest'] ?? true),
        ];
        if (isset($validated['date_start']) && isset($validated['date_end'])) {
            $form['START_DATE'] = $validated['date_start'];
            $form['END_DATE'] = $validated['date_end'];
        }

        $result = $this->makePost('/reports/Service', $form);

        if ($result === []) {
            return ['ok' => true, 'found' => false];
        }

        return [
            'ok' => true,
            'found' => true,
            'reports' => collect($result)->map(fn ($item) => [
                'lab_no' => $item['LAB_NO'],
                'service_id' => $item['SERV_ID'],
                'service_name' => $item['SERV_DESC'],
                'datetime_order' => $item['ORDER_DATE'] . ' ' . $item['ORDER_TIME'],
                'datetime_specimen_received' => $item['SPECIMEN_RECEIVED'] . ' ' . $item['SPECIMEN_RECEIVED_TIME'],
                'datetime_report' => $item['REPORT_DATE'] && $item['REPORT_TIME']
                    ? $item['REPORT_DATE'] . ' ' . $item['REPORT_TIME']
                    : null,
                'requester_id' => $item['REQUESTOR_ID'],
                'requester' => $item['REQUESTOR'],
                'note' => $item['NOTE'],
                'results' => collect($item['RESULT'])->map(fn ($item) => $this->labItemCast($item))->values()->all(),
            ])->values()->all(),
        ];
    }

    public function getLabFromItemCode(array $validated): array
    {
        $form = [
            'HN' => $validated['hn'],
            'GROUP_TI_CODE' => $validated['item_codes'],
        ];
        if (isset($validated['date_start']) && isset($validated['date_end'])) {
            $form['END_DATE'] = $validated['date_end'];
            $form['START_DATE'] = $validated['date_start'];
        }

        $result = $this->makePost('/reports/Item', $form);

        if ($result === []) {
            return ['ok' => true, 'found' => false];
        }

        return [
            'ok' => true,
            'found' => true,
            'reports' => collect($result)->map(fn ($item) => $this->labItemWithHeaderCast($item))->values()->all(),
        ];
    }

    public function getLabFromItemCodeAllResults(array $validated): array
    {
        $form['HN'] = $validated['hn'];
        $form['TI_CODE'] = $validated['item_code'];

        try {
            $result = Http::withToken($this->API_TOKEN)
                ->withOptions(['verify' => false])
                ->retry(3, 200)
                ->get(config('si_lis.service_url').'/reports/Items', $form)
                ->json();
        } catch (Exception $e) {
            Log::error('recently-reports@'.$e->getMessage());

            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }

        if ($result === null) {
            return ['ok' => false, 'serverError' => true];
        }

        if ($result === []) {
            return ['ok' => true, 'found' => false];
        }

        return [
            'ok' => true,
            'found' => true,
            'reports' => collect($result)->map(fn ($item) => $this->labItemWithHeaderCast($item))->values()->all(),
        ];
    }

    protected function makePost(string $url, array $form): ?array
    {
        try {
            $result = Http::withToken($this->API_TOKEN)
                ->withOptions(['verify' => false])
                ->retry(3, 200)
                ->post(config('si_lis.service_url').$url, $form)
                ->json();
        } catch (Exception $e) {
            Log::error($url.'-make_post@'.$e->getMessage());

            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

    protected function labItemCast(array $item): array
    {
        return [
            'item_code' => $item['TI_CODE'],
            'item_name' => $item['TI_NAME'],
            'value_string' => $item['RESULT_CHAR'],
            'value_numeric' => $item['RESULT_NUM'],
            'range_label' => $item['RANGE'],
            'abnormal_label' => $item['ABNORMAL'],
            'units' => $item['UNITS'],
            'long_result_available' => $item['LONG_RES'],
            'long_result_label' => $item['LONG_RESULT'],
            'report_res' => $item['REPORT_RES'],
        ];
    }

    protected function labItemWithHeaderCast(array $item): array
    {
        return [
            'ref_no' => $item['REF_NO'],
            'service_id' => $item['SERV_ID'],
            'service_name' => $item['SERV_DESC'],
            'datetime_order' => $item['ORDER_DATE'] . ' ' . $item['ORDER_TIME'],
            'datetime_specimen_received' => $item['SPECIMEN_RECEIVED'] . ' ' . $item['SPECIMEN_RECEIVED_TIME'],
            'datetime_report' => $item['REPORT_DATE'] && $item['REPORT_TIME']
                ? $item['REPORT_DATE'] . ' ' . $item['REPORT_TIME']
                : null,
            'requester_id' => $item['REQUESTOR_ID'],
            'requester' => $item['REQUESTOR'],
            'item_code' => $item['TI_CODE'],
            'item_name' => $item['TI_NAME'],
            'value_string' => $item['RESULT_CHAR'],
            'value_numeric' => $item['RESULT_NUM'],
            'range_label' => $item['RANGE'],
            'abnormal_label' => $item['ABNORMAL'],
            'units' => $item['UNITS'],
            'long_result_available' => $item['LONG_RES'],
            'long_result_label' => $item['LONG_RESULT'],
            'report_res' => $item['REPORT_RES'],
            'note' => $item['NOTE'],
        ];
    }
}
