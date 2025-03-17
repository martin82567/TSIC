<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class tips extends \Eloquent implements Authenticatable
{
    use AuthenticableTrait;
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'submitted_tips';
    protected $fillable = array(
        'user_id',
        'tips_information'
        
    );

    public $timestamps = false;
}