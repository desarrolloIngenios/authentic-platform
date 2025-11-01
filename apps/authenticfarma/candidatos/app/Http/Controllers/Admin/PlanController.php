<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PlanRequest;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $plans = Plan::with('details')->paginate(10);
        return view('admin.plans.index', compact('user','plans'));
    }


    public function store(PlanRequest $request)
    {
        $validated = $request->validated();
        $plan = Plan::create($validated);
        // Guardar detalles si vienen en el request
        if ($request->has('details')) {
            foreach ($request->input('details') as $detail) {
                $plan->details()->create([
                    'description' => $detail['description'],
                ]);
            }
        }
        toastr()->success('Plan creado correctamente.');
        return redirect()->route('plan.index');
    }

    public function show($id)
    {
        $plan = Plan::with('details')->findOrFail($id);
        return response()->json($plan);
    }

    public function update(PlanRequest $request, $id)
    {
        $validated = $request->validated();
        $plan = Plan::findOrFail($id);
        $plan->update($validated);
        // Actualizar detalles
        if ($request->has('details')) {
            $plan->details()->delete();
            foreach ($request->input('details') as $detail) {
                $plan->details()->create([
                    'description' => $detail['description'],
                ]);
            }
        }
        toastr()->success('Plan actualizado correctamente.');
        return redirect()->route('plan.index');
    }

   
}
