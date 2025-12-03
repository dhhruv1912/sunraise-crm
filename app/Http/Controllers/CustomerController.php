<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /* LIST PAGE */
    public function index()
    {
        return view('page.customers.list');
    }

    /* AJAX LIST */
    public function ajax(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $q = Customer::query();

        if ($search = $request->search) {
            $q->where(function($x) use ($search) {
                $x->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%");
            });
        }

        return response()->json(
            $q->orderBy('id', 'desc')->paginate($perPage)
        );
    }

    /* CREATE FORM */
    public function create()
    {
        return view('page.customers.form');
    }

    /* STORE */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string',
            'email'             => 'nullable|email',
            'mobile'            => 'nullable|string',
            'alternate_mobile'  => 'nullable|string',
            'address'           => 'nullable|string',
            'note'              => 'nullable|string',
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully');
    }

    /* EDIT FORM */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('page.customers.form', compact('customer'));
    }

    /* UPDATE */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'              => 'required|string',
            'email'             => 'nullable|email',
            'mobile'            => 'nullable|string',
            'alternate_mobile'  => 'nullable|string',
            'address'           => 'nullable|string',
            'note'              => 'nullable|string',
        ]);

        Customer::where('id', $id)->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated');
    }

    /* DELETE */
    public function delete(Request $request)
    {
        Customer::where('id', $request->id)->delete();
        return response()->json(['status' => true]);
    }

    /* JSON VIEW */
    public function viewJson($id)
    {
        $row = Customer::findOrFail($id);
        return response()->json($row);
    }

    /* GLOBAL SEARCH API */
    public function searchApi(Request $request)
    {
        $q = trim($request->q);

        return Customer::where('name', 'like', "%$q%")
            ->orWhere('mobile', 'like', "%$q%")
            ->orWhere('email', 'like', "%$q%")
            ->limit(10)
            ->get()
            ->map(function($c){
                return [
                    'id' => $c->id,
                    'text' => "{$c->name} ({$c->mobile})",
                    'name' => $c->name,
                    'email' => $c->email,
                    'mobile' => $c->mobile,
                    'address' => $c->address,
                ];
            });
    }
}
