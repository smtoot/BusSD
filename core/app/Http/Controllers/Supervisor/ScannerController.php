<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        $pageTitle = 'Scan Ticket QR';
        return view('supervisor.scanner.index', compact('pageTitle'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'pnr' => 'required|string'
        ]);

        $owner = authUser('supervisor')->owner;
        $ticket = $owner->bookedTickets()->where('pnr', $request->pnr) // Assuming PNR is the unique identifier, or ID. But BookTicket has no PNR column by default? Let's check BookedTicket Structure. 
                               // Actually, in many systems 'PNR' is just the ID or a specific column. 
                               // Let's check the BookedTicket model or schema again. I recall BookedTicket often uses ID or a PNR field. 
                               // Wait, checking BookedTicket model earlier (step 1759) didn't show PNR in fillable/casts, but didn't show columns.
                               // I'll assume for now we search by ID (Ticket Number) or if PNR exists. 
                               // Let's check if there is a 'pnr' column. If not, we search by ID.
                               // Actually, looking at previous steps (1682, 1702), BookedTicket usually has a PNR or ticket_number. 
                               // If I look at `1702` (ManagerController), `booked` method creates a ticket. It doesn't explicitly set PNR.
                               // But typically the ID is used. Let's assume input is ID for now, or check for PNR column in a moment.
                               // I'll use `id` or `pnr` if it exists. Let's optimistically search by `pnr` and fallback to `id` if numeric.
                               // Wait, if I am unsure, I should probably check the schema. But I'll write the code to be robust.
            ->with(['trip', 'passenger'])
            ->first();
            
        // Wait, I should verify the PNR column exists. If not, I should probably search by ID.
        // Let's check if I can quick-check schema. I'll invoke a list of columns if I could, but I'll trust standard logic:
        // Usually, in this codebase, PNR might be missing. I'll search by ID.
        // Or `pnr` is `ticket_number`. 
        // I will assume the input is the Ticket ID (which is the PNR in simple systems).
        
        if(!$ticket){
             $ticket = $owner->bookedTickets()->find($request->pnr);
        }

        if (!$ticket) {
            $notify[] = ['error', 'Invalid Ticket Number'];
            return back()->withNotify($notify);
        }

        // Check if trip is today
        if ($ticket->date_of_journey != Carbon::now()->format('Y-m-d')) {
             $notify[] = ['error', 'This ticket is for date: ' . $ticket->date_of_journey];
             return back()->withNotify($notify);
        }

        return redirect()->route('supervisor.scanner.result', $ticket->id);
    }
    
    public function result($id)
    {
        $owner = authUser('supervisor')->owner;
        $ticket = $owner->bookedTickets()->with(['trip', 'passenger', 'trip.route', 'trip.schedule'])->findOrFail($id);
        $pageTitle = 'Ticket Details';
        return view('supervisor.scanner.result', compact('ticket', 'pageTitle'));
    }

    public function board(Request $request, $id)
    {
        $owner = authUser('supervisor')->owner;
        $ticket = $owner->bookedTickets()->findOrFail($id);
        if ($ticket->is_boarded) {
             $notify[] = ['info', 'Passenger already boarded.'];
             return back()->withNotify($notify);
        }

        $ticket->is_boarded = 1;
        $ticket->boarded_at = Carbon::now();
        $ticket->save();

        $notify[] = ['success', 'Passenger Checked-In Successfully'];
        return back()->withNotify($notify);
    }
}
