<?php
namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        return view('page.vendors.index');
    }

    public function list(Request $request)
    {
        $query = Vendor::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('company_name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%");
        }

        return response()->json([
            'data' => $query->latest()->paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'company_name' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'gst_number' => 'nullable',
            'pan_number' => 'nullable',
            'address' => 'nullable',
            'type' => 'nullable',
        ]);

        Vendor::create($data);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        return Vendor::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'company_name' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'gst_number' => 'nullable',
            'pan_number' => 'nullable',
            'address' => 'nullable',
            'type' => 'nullable',
        ]);

        $vendor->update($data);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        Vendor::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
