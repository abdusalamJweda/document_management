<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;
use App\Models\DocumentType;
class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $issuedDate = Carbon::parse($data['issued_date']);
        $documentType = DocumentType::find($data['document_type_id']);
        $data['expiry_date'] = $issuedDate->addDays($documentType->expiry_duration_days);

        return $data;
    }
}
