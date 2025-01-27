<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <SiITCheckUser xmlns="http://tempuri.org/">
            <Userid>{{$id}}</Userid>
            <Password>password</Password>
            <SystemID>1</SystemID>
        </SiITCheckUser>
    </soap:Body>
</soap:Envelope>
