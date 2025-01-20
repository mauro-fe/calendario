<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // Nome da tabela (opcional, se seguir o padrão plural de Eloquent)
    protected $table = 'events';
    

    // Campos permitidos para inserção em massa
    protected $fillable = ['title', 'start', 'end', 'description'];

    // Desabilitar timestamps se não forem usados
    public $timestamps = true;
    

    // Relacionamentos (se necessário, podem ser adicionados aqui)
}


