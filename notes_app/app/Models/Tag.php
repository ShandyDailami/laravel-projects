<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name'])]
class Tag extends Model
{
    use HasFactory, Notifiable;

    public function notes()
    {
        return $this->belongsToMany(Note::class, 'note_tags', 'tag_id', 'note_id')
                    ->withPivot('created_at');
    }
}
