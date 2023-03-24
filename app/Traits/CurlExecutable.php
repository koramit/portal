<?php

namespace App\Traits;

trait CurlExecutable
{
    protected function executeCurl($strSOAP, $action, $url): array|bool|string
    {
        $headers = [
            'Host: '.config('simrs.host'),
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "'.$action.'"',
            'Transfer-Encoding: chunked',
        ];

        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_VERBOSE, true); // for debug
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_TIMEOUT, config('app.API_TIMEOUT')); // set connection timeout.
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, config('simrs.server_username').':'.config('simrs.server_password'));

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return false;
        }

        $lowerResponse = strtolower($response);
        if ($response === false || (str_contains($lowerResponse, 'error ') || str_contains($lowerResponse, ' error'))) {
            return false;
        }

        return str_replace('&#x', '', $response);
    }
}
