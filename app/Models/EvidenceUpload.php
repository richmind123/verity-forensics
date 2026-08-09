<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentUser;
use Illuminate\Database\Eloquent\Model;

class EvidenceUpload extends Model
{
    use BelongsToCurrentUser;

    protected $fillable = ['user_id', 'filename', 'file_hash', 'row_count'];
}