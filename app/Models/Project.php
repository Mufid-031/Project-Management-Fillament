<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'project_users',
            'project_id',
            'user_id'
        );
    }
}
