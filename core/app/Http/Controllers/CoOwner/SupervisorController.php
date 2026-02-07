<?php

namespace App\Http\Controllers\CoOwner;

use App\Models\Supervisor;
use App\Http\Controllers\Controller;
use App\Traits\Crud;

class SupervisorController extends Controller
{
    use Crud;

    protected $model = Supervisor::class;
    protected $title = 'Supervisor';
    protected $view  = 'co_owner.supervisor';
    protected $fileInfo = 'supervisor';
    protected $tableName = 'supervisors';
    protected $guard = 'supervisor';


    public function __construct()
    {
        $this->owner = authUser('co-owner')->owner;
    }
}
