<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Traits\Crud;

class DriverController extends Controller
{
    use Crud;

    protected $model = Driver::class;
    protected $view = 'owner.driver';
    protected $title = 'Driver';
    protected $fileInfo = 'driver';
    protected $tableName = 'drivers';
    protected $guard = 'driver';

    public function __construct()
    {
        $this->owner = authUser();
    }
}
