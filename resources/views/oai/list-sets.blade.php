<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/
         http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{{ now()->toISOString() }}</responseDate>
    <request verb="ListSets">{{ url('/oai-pmh') }}</request>
    <ListSets>
        <set>
            <setSpec>type:skripsi</setSpec>
            <setName>Skripsi</setName>
        </set>
        <set>
            <setSpec>type:tesis</setSpec>
            <setName>Tesis</setName>
        </set>
        <set>
            <setSpec>type:disertasi</setSpec>
            <setName>Disertasi</setName>
        </set>
    </ListSets>
</OAI-PMH>
