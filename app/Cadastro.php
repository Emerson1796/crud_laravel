<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cadastro extends Model
{
    protected $table = 'cadastro';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'endereco',
        'curriculo',
        'IP',
    ];

    protected $dates = ['deleted_at'];

}
