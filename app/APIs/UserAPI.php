<?php

namespace App\APIs;

use App\Contracts\UserAPI as UserAPIContract;
use App\Traits\CurlExecutable;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserAPI implements UserAPIContract
{
    use CurlExecutable;

    public function getUserByLogin(string $login, bool $withSensitiveInfo): array
    {
        $headers = [
            'APPNAME' => config('siad.check_user_app_name'),
            'APIKEY' => config('siad.check_user_api_key'),
        ];

        $response = $this->makePost(config('siad.check_user_url'), ['id' => $login], $headers);

        if (! $response['ok'] || ! $response['found']) {
            return $response;
        }

        $profile = $this->getProfile($response['UserInfo']['ID']);

        return [
            'ok' => true,
            'found' => true,
            'active' => $response['isActive'], //
            'login' => $login,
            'org_id' => $response['UserInfo']['ID'],
            'full_name' => $response['UserInfo']['DisplayName'],
            'document_id' => $withSensitiveInfo ? ($profile['pid'] ?? null) : null,
            'position_id' => $profile['job_key'] ?? null,
            'position_name' => $profile['job_key_desc'] ?? null,
            'division_id' => $profile['org_unit_m'] ?? null,
            'division_name' => $profile['org_unit_m_desc'] ?? null,
            'password_expires_in_days' => (int) str_replace('Password Remain(Day(s)): ', '', $response['message']),
            'remark' => $profile['remark'] ?? null,
        ];
    }

    public function getUserById(int|string $id, bool $withSensitiveInfo): array
    {
        $login = $this->getStatus($id);

        if (! $login['ok'] || ! $login['found']) {
            return $login;
        }

        return $this->getUserByLogin($login['login'], $withSensitiveInfo);
    }

    public function authenticate(string $login, string $password, bool $withSensitiveInfo): array
    {
        $headers = [
            'APPNAME' => config('siad.auth_user_app_name'),
            'APIKEY' => config('siad.auth_user_api_key'),
        ];
        $response = $this->makePost(config('siad.auth_user_url'), ['name' => $login, 'pwd' => $password], $headers);

        if (! $response['ok'] || ! $response['found']) {
            return $response;
        }

        $profile = $this->getProfile($response['UserInfo']['UserData']['sapid']);

        return [
            'ok' => true, // mean user is active
            'found' => true,
            'login' => $login,
            'org_id' => $response['UserInfo']['UserData']['sapid'],
            'full_name' => $response['UserInfo']['UserData']['full_name'],
            'full_name_en' => $response['UserInfo']['UserData']['eng_name'],
            'document_id' => $withSensitiveInfo ? ($profile['pid'] ?? null) : null,
            'position_id' => $profile['job_key'] ?? null,
            'position_name' => $profile['job_key_desc'] ?? null,
            'division_id' => $profile['org_unit_m'] ?? null,
            'division_name' => $profile['org_unit_m_desc'] ?? null,
            'department_name' => $response['UserInfo']['UserData']['department'], //
            'office_name' => $response['UserInfo']['UserData']['office'], //
            'email' => $response['UserInfo']['UserData']['email'], //
            'password_expires_in_days' => $response['UserInfo']['UserData']['daysLeft'],
            'remark' => $profile['remark'] ?? null,
        ];
    }

    protected function makePost(string $url, array $data, array $headers): array
    {
        try {
            $response = Http::withHeaders($headers)
                ->post($url, $data);
        } catch (Exception $e) {
            if ($url === config('siad.auth_user_url')) {
                return $this->altAuthenticate($data['name'], $data['pwd']);
            }

            Log::error($e->getMessage());

            return [
                'ok' => false,
                'status' => 500,
                'error' => 'server',
                'message' => $e->getMessage(),
            ];
        }

        if ($response->successful()) {
            $data = $response->json() + ['ok' => true];
            if (isset($data['msg'])) {
                $data['message'] = $data['msg'];
                unset($data['msg']);
            } else {
                $data['message'] = null;
            }

            return $data;
        }

        // @TODO not success has variant SHOULD cover all cases ie, response body is empty
        return [
            'ok' => false,
            'status' => $response->status(),
            'error' => $response->serverError() ? 'server' : 'client',
            'message' => $response->body(),
        ];
    }

    protected function getProfile(int|string $id): array
    {
        $functionName = 'SiITCheckUser';
        $action = 'http://tempuri.org/'.$functionName;

        $strSOAP = '<?xml version="1.0" encoding="utf-8"?>';
        $strSOAP .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
        $strSOAP .= '<soap:Body>';
        $strSOAP .= '<'.$functionName.' xmlns="http://tempuri.org/">';
        $strSOAP .= '<Userid>'.$id.'</Userid>';
        $strSOAP .= '<Password>password</Password>';
        $strSOAP .= '<SystemID>1</SystemID>';
        $strSOAP .= '</'.$functionName.'>';
        $strSOAP .= '</soap:Body>';
        $strSOAP .= '</soap:Envelope>';

        // make request and check the response.
        if (($response = $this->executeCurl($strSOAP, $action, config('simrs.user_url'))) === false) {
            return [
                'ok' => false,
                'status' => 500,
                'error' => 'server',
                'body' => 'Server Error',
            ];
        }

        $response = str_replace('&#x', '', $response);
        $xml = simplexml_load_string($response);
        $namespaces = $xml->getNamespaces(true);

        $response = $xml->children($namespaces['soap'])
            ->Body
            ->children($namespaces[''])
            ->SiITCheckUserResponse
            ->SiITCheckUserResult
            ->children($namespaces['diffgr'])
            ->diffgram
            ->children()
            ->NewDataSet
            ->children()
            ->GetUsers
            ->children();

        return ((array) $response) + ['ok' => true];
    }

    protected function getStatus(string $id): array
    {
        $response = Http::withOptions(['verify' => false])
            ->post(config('siad.user_status_url'), ['employeeID' => $id]);

        if ($response->status() !== 200) {
            return [
                'ok' => false,
                'found' => false,
                'message' => 'something went wrong with status code '.$response->status(),
            ];
        }

        $response = $response->json();

        if ($response === '') {
            return [
                'ok' => true,
                'found' => false,
                'message' => 'not found',
            ];
        }

        return [
            'ok' => true,
            'found' => true,
            'login' => $response['AccountName'],
            'status' => strtolower($response['Status']),
        ];
    }

    protected function altAuthenticate(string $login, string $password): array
    {
        $userData = [
            'ok' => false,
            'status' => 500,
            'error' => 'server',
            'message' => 'alt also failed',
        ];

        $response = Http::acceptJson()->post(config('siad.alt_auth_url'), [
            'username' => $login,
            'password' => $password,
        ]);

        if ($response->status() === 200) {
            $userData = [];
            $userData['login'] = $login;
            $token = $response->json()['access_token'];

            $response = Http::acceptJson()
                ->withToken($token)
                ->get(config('siad.alt_user_info_url'));

            if ($response->status() !== 200) {
                return $userData;
            }

            $u1 = $response->json();
            $userData['org_id'] = $u1['sub'];
            $userData['full_name'] = $u1['unique_name'];
            $userData['email'] = $u1['email'];

            $response = Http::acceptJson()
                ->post(config('siad.alt_user_org_id_url'), ['employeeID' => $userData['org_id']]);

            if ($response->status() !== 200) {
                return $userData;
            }

            $u2 = $response->json();
            $userData['password_expires_in_days'] = $u2['pwdExpiryDay'];
            $userData['ok'] = true;
            $userData['found'] = true;

            return $userData;
        } elseif ($response->status() === 400) {
            return [
                'ok' => true,
                'found' => false,
                'message' => 'not found',
            ];
        }

        return $userData;
    }
}
