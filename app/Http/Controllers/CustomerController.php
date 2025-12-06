<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerActivity;
use App\Models\CustomerNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /* ---------------------------------------------------------
     | LIST PAGE (Blade)
     --------------------------------------------------------- */
    public function index()
    {
        return view('page.customers.list');
    }

    /* ---------------------------------------------------------
     | AJAX LIST (Filters + Pagination)
     --------------------------------------------------------- */
    public function ajax(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);

        $q = Customer::query();

        if ($search = trim($request->search)) {
            $q->where(function ($x) use ($search) {
                $x->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('alternate_mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_from')) {
            $q->whereDate('created_at', '>=', $request->filter_from);
        }
        if ($request->filled('filter_to')) {
            $q->whereDate('created_at', '<=', $request->filter_to);
        }

        $data = $q->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($data);
    }

    /* ---------------------------------------------------------
     | CREATE FORM
     --------------------------------------------------------- */
    public function create()
    {
        return view('page.customers.form');
    }

    /* ---------------------------------------------------------
     | STORE
     --------------------------------------------------------- */
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $customer = Customer::create($data);

        $this->logActivity($customer->id, 'created', 'Customer created');

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully');
    }

    /* ---------------------------------------------------------
     | EDIT FORM
     --------------------------------------------------------- */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('page.customers.form', compact('customer'));
    }

    /* ---------------------------------------------------------
     | UPDATE
     --------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $data = $this->validatePayload($request);

        $customer = Customer::findOrFail($id);
        $customer->update($data);

        $this->logActivity($customer->id, 'updated', 'Customer updated');

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated');
    }

    /* ---------------------------------------------------------
     | DELETE
     --------------------------------------------------------- */
    public function delete(Request $request)
    {
        $customer = Customer::find($request->id);

        if (! $customer) {
            return response()->json(['status' => false, 'message' => 'Not found'], 404);
        }

        $customer->delete();

        $this->logActivity($request->id, 'deleted', 'Customer deleted');

        return response()->json(['status' => true]);
    }

    /* ---------------------------------------------------------
     | JSON VIEW (modal/details)
     --------------------------------------------------------- */
    public function viewJson($id)
    {
        $row = Customer::with(['notes.user', 'activities.user'])
            ->findOrFail($id);

        return response()->json($row);
    }

    /* ---------------------------------------------------------
     | GLOBAL SEARCH API (for attach in leads/projects/invoice)
     --------------------------------------------------------- */
    public function searchApi(Request $request)
    {
        $q = trim($request->q);

        if (strlen($q) < 2) {
            return [];
        }

        return Customer::where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('mobile', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(function ($c) {
                return [
                    'id'      => $c->id,
                    'text'    => "{$c->name} ({$c->mobile})",
                    'name'    => $c->name,
                    'email'   => $c->email,
                    'mobile'  => $c->mobile,
                    'address' => $c->address,
                ];
            });
    }

    /* ---------------------------------------------------------
     | ADD NOTE (AJAX)
     --------------------------------------------------------- */
    public function addNote(Request $request, $id)
    {
        $request->validate(['note' => 'required|string']);

        $note = CustomerNote::create([
            'customer_id' => $id,
            'user_id' => Auth::id(),
            'note' => $request->note,
        ]);

        return response()->json(['status' => true, 'note' => $note]);
    }

    /* ---------------------------------------------------------
     | ADD ACTIVITY (AJAX)
     --------------------------------------------------------- */
    public function addActivity(Request $request, $id)
    {
        $request->validate([
            'action'  => 'required|string',
            'message' => 'nullable|string',
        ]);

        $act = CustomerActivity::create([
            'customer_id' => $id,
            'user_id'     => Auth::id(),
            'action'      => $request->action,
            'message'     => $request->message,
        ]);

        return response()->json(['status' => true, 'activity' => $act]);
    }

    /* ---------------------------------------------------------
     | VALIDATION
     --------------------------------------------------------- */
    private function validatePayload(Request $request)
    {
        return $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'mobile'           => 'nullable|string|max:255',
            'alternate_mobile' => 'nullable|string|max:255',
            'address'          => 'nullable|string',
            'note'             => 'nullable|string',
        ]);
    }

    /* ---------------------------------------------------------
     | LOG ACTIVITY (every change)
     --------------------------------------------------------- */
    private function logActivity($customerId, $action, $message)
    {
        try {
            CustomerActivity::create([
                'customer_id' => $customerId,
                'user_id'     => Auth::id(),
                'action'      => $action,
                'message'     => $message,
            ]);
        } catch (\Throwable $e) {
            // safe fail
        }
    }
}
