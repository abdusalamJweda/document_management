<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'employee_id',
        'document_id',
        'document_type_id',
    ];
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
