<?php

namespace App\Modules\Documents\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Services\DocumentService;

class DocumentReaderController extends Controller
{

    public function __construct(private DocumentService $documentService){}

    public function index($uuid) {
        $data = $this->documentService->getTrashedByUuid($uuid);
        return view('pdf_reader.reader')->with([
            'document_link' => $data->document_link
        ]);
    }
}
