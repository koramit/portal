<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <SearchInpatientAdmitOnly xmlns="http://tempuri.org/">
            <HN>{{$key}}</HN>
            <UserName>{{config('simrs.api_username')}}</UserName>
            <Password>{{config('simrs.api_password')}}</Password>
            <RequestComputerName></RequestComputerName>
        </SearchInpatientAdmitOnly>
    </soap:Body>
</soap:Envelope>
