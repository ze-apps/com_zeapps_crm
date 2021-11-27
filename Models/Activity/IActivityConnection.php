<?php

namespace App\com_zeapps_crm\Models\Activity;

interface IActivityConnection
{
    public function getParent() ;
    public function saveToActivityConnection();
}