<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Boq;
use App\Models\BoqItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\Settings;
use Barryvdh\DomPDF\Facade\Pdf;

class BoqController extends Controller
{
    public function edit($project_id,$boq_id,$item_id)
    {
        try {
            $boq = BoqItem::findOrFail($item_id);
            return response()->json([
                'status' => true,
                'boq'  => $boq
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error'  => $th->getMessage()
            ],500);
        }
    }

    public function editBoq($project_id,$boq_id)
    {
        try {
            $boq = Boq::with('items')->findOrFail($boq_id);
            $boq_items = json_decode(Settings::getValue('projects_boq_items'),true);
            return view('page.boq.form', compact('project_id','boq','boq_items'));
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error'  => $th->getMessage()
            ],500);
        }
        return view('page.boq.form', compact('boq','project_id','boq_items'));
    }

    public function index(Request $request, $project_id= null){
        try {
            if ($project_id) {
                $boq = Boq::where('project_id',$project_id)->get();
            }else{
                $boq = Boq::get();
            }
            return response()->json([
                'status' => true,
                'error'  => $boq
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error'  => $th->getMessage()
            ],500);
        }
    }

    public function list(Request $request, $project_id, $boq_id){
        try {
            $items = BoqItem::where('boq_id',$boq_id)->get();
            return response()->json([
                'status' => true,
                'boq'  => $items
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error'  => $th->getMessage()
            ],500);
        }
    }

    public function update(Request $request, $project_id, $boq_id, $item_id=null){
        try {
            $boqItem = new BoqItem();
            $boqItem->item = $request->name;
            $boqItem->boq_id = $boq_id;
            $boqItem->unit = $request->unit;
            $boqItem->rate = $request->rate;
            $boqItem->quantity = $request->quentity;
            $boqItem->amount = $request->amount;
            $boqItem->specification = $request->specification;
            $boqItem->save();
            $this->calculateTotalAmount($boq_id);
            return response()->json([
                'status' => true,
                'item' => $boqItem
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()
            ],500);
        }
    }

    public function delete(Request $request, $project_id, $boq_id, $item_id){
        try {
            $item = BoqItem::findOrFail($item_id)->delete();
            $this->calculateTotalAmount($boq_id);
            return response()->json([
                'status' => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error'  => $th->getMessage()
            ],500);
        }
    }


    protected function calculateTotalAmount($boq_id){
        $boq = Boq::with('items')->findOrFail($boq_id);
        $total = $boq->items->sum('amount');
        $boq->total_amount = $total;
        $pdf = Pdf::loadView('page.boq.pdf', compact('boq'));
        $path = "boqs/boq_{$boq->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());
        $boq->pdf_path = $path;
        $boq->save();
    }
}
