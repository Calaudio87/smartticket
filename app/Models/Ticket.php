<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'subject', 'description',
        'category', 'priority', 'status', 'summary'
    ];

    /**
     * Laravel 11 approach: método en lugar de propiedad.
     */
    protected function casts(): array
    {
        return [
            // Aquí definiremos enums o JSON si es necesario más adelante
        ];
    }

    public function notes(): HasMany
    {
        return $this->hasMany(TicketNote::class);
    }
}
