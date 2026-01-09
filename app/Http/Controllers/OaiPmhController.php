<?php

namespace App\Http\Controllers;

use App\Models\Ethesis;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class OaiPmhController extends Controller
{
    private string $baseUrl;
    private string $repositoryName = 'Universitas Darussalam Gontor Repository';
    private string $adminEmail = 'repository@unida.gontor.ac.id';
    
    public function __construct()
    {
        $this->baseUrl = url('/oai-pmh');
    }
    
    public function index(Request $request): Response
    {
        $verb = $request->get('verb');
        
        if (!$verb) {
            return $this->errorResponse('badVerb', 'Missing verb argument');
        }
        
        return match($verb) {
            'Identify' => $this->identify(),
            'ListMetadataFormats' => $this->listMetadataFormats($request),
            'ListRecords' => $this->listRecords($request),
            'ListIdentifiers' => $this->listIdentifiers($request),
            'GetRecord' => $this->getRecord($request),
            'ListSets' => $this->listSets(),
            default => $this->errorResponse('badVerb', 'Illegal verb')
        };
    }
    
    private function identify(): Response
    {
        $earliestDate = Ethesis::where('is_public', true)->min('created_at');
        $earliestDatestamp = $earliestDate ? \Carbon\Carbon::parse($earliestDate) : now();
        
        $xml = view('oai.identify', [
            'baseUrl' => $this->baseUrl,
            'repositoryName' => $this->repositoryName,
            'adminEmail' => $this->adminEmail,
            'earliestDatestamp' => $earliestDatestamp,
        ])->render();
        
        return response($xml)->header('Content-Type', 'application/xml');
    }
    
    private function listMetadataFormats(Request $request): Response
    {
        if ($request->has('identifier')) {
            $identifier = str_replace('oai:unida.gontor.ac.id:', '', $request->get('identifier'));
            if (!Ethesis::where('is_public', true)->where('id', $identifier)->exists()) {
                return $this->errorResponse('idDoesNotExist', 'No matching identifier');
            }
        }
        
        $xml = view('oai.list-metadata-formats')->render();
        return response($xml)->header('Content-Type', 'application/xml');
    }
    
    private function listRecords(Request $request): Response
    {
        $metadataPrefix = $request->get('metadataPrefix');
        if (!$metadataPrefix) {
            return $this->errorResponse('badArgument', 'Missing metadataPrefix');
        }
        
        if (!in_array($metadataPrefix, ['oai_dc', 'mods'])) {
            return $this->errorResponse('cannotDisseminateFormat', 'Unsupported metadata format');
        }
        
        $query = Ethesis::where('is_public', true)->with(['department.faculty']);
        
        // Date filtering
        if ($request->has('from')) {
            $from = Carbon::parse($request->get('from'));
            $query->where('updated_at', '>=', $from);
        }
        
        if ($request->has('until')) {
            $until = Carbon::parse($request->get('until'));
            $query->where('updated_at', '<=', $until);
        }
        
        // Set filtering
        if ($request->has('set')) {
            $set = $request->get('set');
            if (str_starts_with($set, 'faculty:')) {
                $facultyId = str_replace('faculty:', '', $set);
                $query->whereHas('department', fn($q) => $q->where('faculty_id', $facultyId));
            } elseif (str_starts_with($set, 'type:')) {
                $type = str_replace('type:', '', $set);
                $query->where('type', $type);
            }
        }
        
        $records = $query->orderBy('updated_at', 'desc')->get();
        
        $xml = view('oai.list-records', [
            'records' => $records,
            'metadataPrefix' => $metadataPrefix,
            'baseUrl' => $this->baseUrl,
        ])->render();
        
        return response($xml)->header('Content-Type', 'application/xml');
    }
    
    private function listIdentifiers(Request $request): Response
    {
        // Similar to listRecords but only return headers
        $request->merge(['metadataPrefix' => $request->get('metadataPrefix', 'oai_dc')]);
        return $this->listRecords($request);
    }
    
    private function getRecord(Request $request): Response
    {
        $identifier = $request->get('identifier');
        $metadataPrefix = $request->get('metadataPrefix');
        
        if (!$identifier || !$metadataPrefix) {
            return $this->errorResponse('badArgument', 'Missing required arguments');
        }
        
        $id = str_replace('oai:unida.gontor.ac.id:', '', $identifier);
        $record = Ethesis::where('is_public', true)->where('id', $id)->with(['department.faculty'])->first();
        
        if (!$record) {
            return $this->errorResponse('idDoesNotExist', 'No matching identifier');
        }
        
        $xml = view('oai.get-record', [
            'record' => $record,
            'metadataPrefix' => $metadataPrefix,
            'baseUrl' => $this->baseUrl,
        ])->render();
        
        return response($xml)->header('Content-Type', 'application/xml');
    }
    
    private function listSets(): Response
    {
        $xml = view('oai.list-sets')->render();
        return response($xml)->header('Content-Type', 'application/xml');
    }
    
    private function errorResponse(string $code, string $message): Response
    {
        $xml = view('oai.error', [
            'code' => $code,
            'message' => $message,
            'baseUrl' => $this->baseUrl,
        ])->render();
        
        return response($xml)->header('Content-Type', 'application/xml');
    }
}
