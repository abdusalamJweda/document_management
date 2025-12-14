<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Builder;
class Document extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'document_type_id',
        'issued_date',
        'submission_date',
        'expiry_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issued_date' => 'date',
        'submission_date' => 'date',
    ];

    /**
     * Get the employee that owns the document.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the document type for the document.
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
    public function emailLog(){
        return $this->hasMany(EmailLog::class);
    }

    public function getFileAttribute(): ?string
    {
        return $this->getFirstMediaUrl('files');

    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('files')
            ->useDisk('public') // or 'media' if you want private
            ->singleFile();     // optional, forces only 1 file per document
    }
    public function getIsExpiredAttribute(): bool
    {
        // The expiry_date is cast to a Carbon instance because of protected $casts
        // Check if expiry_date is set and if the current date is after the expiry date.
        if ($this->expiry_date) {
            return now()->greaterThan($this->expiry_date);
        }

        return false; // Not expired if no expiry date is set
    }

    
}
