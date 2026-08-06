<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceUpload extends Model
{
    protected $fillable = ['filename', 'file_hash', 'row_count'];
}
