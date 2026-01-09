<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/
         http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{{ now()->toISOString() }}</responseDate>
    <request verb="Identify">{{ $baseUrl }}</request>
    <Identify>
        <repositoryName>{{ $repositoryName }}</repositoryName>
        <baseURL>{{ $baseUrl }}</baseURL>
        <protocolVersion>2.0</protocolVersion>
        <adminEmail>{{ $adminEmail }}</adminEmail>
        <earliestDatestamp>{{ $earliestDatestamp->toISOString() }}</earliestDatestamp>
        <deletedRecord>no</deletedRecord>
        <granularity>YYYY-MM-DDThh:mm:ssZ</granularity>
        <description>
            <oai-identifier xmlns="http://www.openarchives.org/OAI/2.0/oai-identifier"
                           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                           xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier
                           http://www.openarchives.org/OAI/2.0/oai-identifier.xsd">
                <scheme>oai</scheme>
                <repositoryIdentifier>unida.gontor.ac.id</repositoryIdentifier>
                <delimiter>:</delimiter>
                <sampleIdentifier>oai:unida.gontor.ac.id:1</sampleIdentifier>
            </oai-identifier>
        </description>
    </Identify>
</OAI-PMH>
