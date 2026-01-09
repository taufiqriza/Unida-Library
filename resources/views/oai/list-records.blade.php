<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/
         http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{{ now()->toISOString() }}</responseDate>
    <request verb="ListRecords" metadataPrefix="{{ $metadataPrefix }}">{{ $baseUrl }}</request>
    <ListRecords>
        @foreach($records as $record)
        <record>
            <header>
                <identifier>oai:unida.gontor.ac.id:{{ $record->id }}</identifier>
                <datestamp>{{ $record->updated_at->toISOString() }}</datestamp>
                @if($record->department?->faculty)
                <setSpec>faculty:{{ $record->department->faculty->id }}</setSpec>
                @endif
                <setSpec>type:{{ $record->type }}</setSpec>
            </header>
            <metadata>
                @if($metadataPrefix === 'oai_dc')
                <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                           xmlns:dc="http://purl.org/dc/elements/1.1/"
                           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                           xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/
                           http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
                    <dc:title>{{ $record->title }}</dc:title>
                    @if($record->title_en)
                    <dc:title>{{ $record->title_en }}</dc:title>
                    @endif
                    <dc:creator>{{ $record->author }}</dc:creator>
                    @if($record->advisor1)
                    <dc:contributor>{{ $record->advisor1 }} (Advisor)</dc:contributor>
                    @endif
                    @if($record->advisor2)
                    <dc:contributor>{{ $record->advisor2 }} (Advisor)</dc:contributor>
                    @endif
                    @if($record->abstract)
                    <dc:description>{{ $record->abstract }}</dc:description>
                    @endif
                    @if($record->abstract_en)
                    <dc:description>{{ $record->abstract_en }}</dc:description>
                    @endif
                    <dc:publisher>Universitas Darussalam Gontor</dc:publisher>
                    <dc:date>{{ $record->year }}</dc:date>
                    <dc:type>{{ ucfirst($record->type) }}</dc:type>
                    <dc:format>application/pdf</dc:format>
                    <dc:identifier>{{ route('opac.ethesis.show', $record->id) }}</dc:identifier>
                    @if($record->is_fulltext_public && $record->file_path)
                    <dc:identifier>{{ asset('storage/thesis/' . $record->file_path) }}</dc:identifier>
                    @endif
                    <dc:language>id</dc:language>
                    @if($record->keywords)
                    @foreach(explode(',', $record->keywords) as $keyword)
                    <dc:subject>{{ trim($keyword) }}</dc:subject>
                    @endforeach
                    @endif
                    @if($record->department)
                    <dc:subject>{{ $record->department->name }}</dc:subject>
                    @endif
                    <dc:rights>© {{ $record->year }} {{ $record->author }}</dc:rights>
                </oai_dc:dc>
                @endif
            </metadata>
        </record>
        @endforeach
    </ListRecords>
</OAI-PMH>
