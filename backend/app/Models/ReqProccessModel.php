<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//На входе запрос из 100к строк формата ключ (int) => расход трафика в байтах (int).
//Нужно обработать этот запрос, просуммировать расход, если есть повторы ключей и записать все в базу.
//В базе таблица key (int, index unique), traffic (bigint)
class ReqProccessModel extends Model
{
    use HasFactory;

    protected $table = 'req_proccess';
    protected $fillable = ['key', 'traffic'];
    public $timestamps = true;

}
