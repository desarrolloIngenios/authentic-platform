<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HvOfCanOfertum;
use App\Models\OfOfertaLaboral;

class PostulationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $postulaciones = HvOfCanOfertum::where('id_hoja_vida', session('hoja_vida'))
            ->with(['ofertaLaboral', 'ofertaLaboral.cargo', 'ofertaLaboral.empresa'])
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(20);
        return view('candidate.postulation.index', compact('user', 'postulaciones'));
    }


    public function showDrawer($id)
    {
     
        $vacante = OfOfertaLaboral::with([
            'empresa', 'cargo', 'idioma', 'nivelIdioma', 'nivelEducacion', 'ciudad', 'tiempoExperiencia', 'rangoSalarial', 'sectores', 'areas',            
        ])->findOrFail($id); 
        return view('candidate.postulation.partials.viewVacant', compact('vacante'));
    }
}
