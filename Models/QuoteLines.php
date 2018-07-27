<?php

namespace App\com_zeapps_crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteLines extends Model
{
    use SoftDeletes;

    protected $table = 'com_zeapps_crm_quote_lines';

    public static function updateOldTable($id_quote, $sort)
    {
        DB::statement('UPDATE com_zeapps_crm_quote_lines SET sort = (sort-1) WHERE id_quote = ' . $id_quote . ' AND sort > ' . $sort);
    }

    public static function updateNewTable($id_quote, $sort)
    {
        DB::statement('UPDATE com_zeapps_crm_quote_lines SET sort = (sort+1) WHERE id_quote = ' . $id_quote . ' AND sort >= ' . $sort);
    }
}