<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteLines extends Model {
    use SoftDeletes;

    protected $table = 'com_zeapps_crm_quote_lines';
}