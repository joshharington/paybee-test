<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BotSetting extends Model {

    use SoftDeletes;

    protected $table = 'bot_settings';
    protected $primaryKey = 'id';
    protected $fillable = ['key', 'value'];

}
