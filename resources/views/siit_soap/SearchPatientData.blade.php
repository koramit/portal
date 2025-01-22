<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <SearchPatientData xmlns="http://tempuri.org/">
            <hn>{{$key}}</hn>
            <Username>{{config('simrs.api_username')}}</Username>
            <Password>{{config('simrs.api_password')}}</Password>
            <RequestComputerName></RequestComputerName>
        </SearchPatientData>
    </soap:Body>
</soap:Envelope>
