<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        parent::__construct();
        $this->userType     = 'owner';
        $this->column       = 'owner_id';
        $this->user         = authUser();
        $this->redirectLink = 'owner.ticket.index';
        $this->userType     = 'owner';
    }
}
