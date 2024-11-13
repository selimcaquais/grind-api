<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    
    use HasFactory;

    protected $table = 'tasks';
    
    protected $fillable = [
        'name', 
        'iteration_max',
        'streak',
        'days',
        'user_id',
    ];

     // Indiquer que days est un attribut de type JSON
     protected $casts = [
        'days' => 'array', // Cela convertira automatiquement 'days' en tableau lors de l'accès
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}