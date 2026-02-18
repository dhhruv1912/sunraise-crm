<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerActivity;
use App\Models\CustomerNote;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /* ---------------------------------------------------------
     | LIST PAGE (Blade)
     --------------------------------------------------------- */
    public function index()
    {
        return view('page.customers.index');
    }

    public function ajaxList(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $q = Customer::query();

        if ($request->search) {
            $s = $request->search;
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $total = (clone $q)->count();

        $rows = $q
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($c) {

                $photo = $c->documents
                    ->firstWhere('type', 'passport_size_photo');

                return [
                    'id'     => $c->id,
                    'name'   => $c->name,
                    'mobile' => $c->mobile,
                    'email'  => $c->email,

                    'avatar' => $photo
                        ? Storage::disk('public')->url($photo->file_path)
                        : null,
                ];
            });

        return response()->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $perPage),
            ],
            'canEdit' => Gate::allows('project.customer.edit')
        ]);
    }

    public function ajaxWidgets()
    {
        return view('page.customers.widgets', [
            'total'      => Customer::count(),
            'projects'   => Project::count(),
            'active'     => Project::where('status', '!=', 'complete')->count(),
            'outstanding' => Invoice::sum('balance'),
            'totalInvoiced' => Invoice::sum('total'),
        ]);
    }

    public function ajaxActivities(Customer $customer)
    {
        $activities = $customer->activities()
            ->latest()
            ->limit(20)
            ->get();

        return view(
            'page.customers.partials.activities',
            compact('activities')
        );
    }
    public function ajaxDocuments(Customer $customer)
    {
        $documents = Document::where(function ($q) use ($customer) {
            $q->where('entity_type', Customer::class)
                ->where('entity_id', $customer->id);
        })
            ->latest()
            ->get();

        return view(
            'page.customers.partials.documents',
            compact('documents')
        );
    }
    public function edit(Customer $customer)
    {
        $docs = Document::where('entity_type', Customer::class)
            ->where('entity_id', $customer->id)
            ->get()
            ->keyBy('type');
        return view('page.customers.edit', compact('customer','docs'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'mobile'             => 'required|string|max:20|unique:customers,mobile',
            'email'              => 'nullable|email|unique:customers,email',
            'alternate_mobile'   => 'nullable|string|max:20',
            'address'            => 'nullable|string',

            // identity
            'aadhar_card_number' => 'nullable|string|max:255',
            'pan_card_number'    => 'nullable|string|max:255',

            // banking
            'bank_name'          => 'nullable|string|max:255',
            'ifsc_code'          => 'nullable|string|max:255',
            'ac_holder_name'     => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'branch_name'        => 'nullable|string|max:255',
        ]);

        $customer = Customer::create($data);

        // activity log
        CustomerActivity::create([
            'customer_id' => $customer->id,
            'action'      => 'customer_created',
            'message'     => 'Customer profile created',
            'user_id'     => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Customer created successfully',
            'id'      => $customer->id,
        ]);
    }
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'mobile'             => 'required|string|max:20|unique:customers,mobile,' . $customer->id,
            'email'              => 'nullable|email|unique:customers,email,' . $customer->id,
            'alternate_mobile'   => 'nullable|string|max:20',
            'address'            => 'nullable|string',

            // identity
            'aadhar_card_number' => 'nullable|string|max:255',
            'pan_card_number'    => 'nullable|string|max:255',

            // banking
            'bank_name'          => 'nullable|string|max:255',
            'ifsc_code'          => 'nullable|string|max:255',
            'ac_holder_name'     => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'branch_name'        => 'nullable|string|max:255',
        ]);

        $customer->update($data);

        CustomerActivity::create([
            'customer_id' => $customer->id,
            'action'      => 'customer_updated',
            'message'     => 'Customer profile updated',
            'user_id'     => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Customer updated successfully',
        ]);
    }
    public function view(Customer $customer)
    {
        $docs = Document::where('entity_type', Customer::class)
            ->where('entity_id', $customer->id)
            ->get()
            ->keyBy('type');
        return view('page.customers.view', compact('customer','docs'));
    }
}
