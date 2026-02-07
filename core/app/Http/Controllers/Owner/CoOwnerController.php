<?php

namespace App\Http\Controllers\Owner;

use App\Models\CoOwner;
use App\Http\Controllers\Controller;
use App\Traits\Crud;

class CoOwnerController extends Controller
{
    use Crud;

    protected $model = CoOwner::class;
    protected $view = 'owner.co-owner';
    protected $title = 'Co-Owner';
    protected $fileInfo = 'co_owner';
    protected $tableName = 'co_owners';
    protected $guard = 'co-owner';


    public function __construct()
    {
        $this->owner = authUser();
    }
}
