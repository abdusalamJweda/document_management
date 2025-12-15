<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Employee extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        
        'name',
        'email',
        'job_title',
        'employee_id',
        'department_id',
    ];
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function emailLog(){
        return $this->hasMany(EmailLog::class);
    }

    // public function getNameAttribute()
    // {
    //     return $this->name;
    // }
    public function getDocumentTypesAttribute(): string
    {
        // Use pluck to get the documentType relation from the documents, 
        // then pluck the 'name' attribute from those DocumentType models.
        // It's crucial to check if the documentType relation is loaded and exists.
        
        $documentTypeNames = $this->documents
            // Filter out documents where the documentType relation isn't loaded or is null
            ->filter(fn($document) => $document->relationLoaded('documentType') && $document->documentType)
            ->pluck('document_type.name') // Pluck the name from the related DocumentType model
            ->unique();
            
        return $documentTypeNames->implode(', ');
    }
}