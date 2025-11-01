<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\OfOfertaLaboral;
use App\Models\HvOfCanOfertum;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cargar el usuario con todas las relaciones necesarias
        $perfil = Usuario::with([
            'hojaVida.candidato.experienciasLaborales',
            'hojaVida.candidato.formacionacademica',
            'hojaVida.candidato.correo',
            'hojaVida.candidato.telefono',
            'hojaVida.candidato.ubicacion',
            'hojaVida.candidato.perfil',
            'hojaVida.candidato.sector',
            'hojaVida.candidato.areasPreferidas',
            'hojaVida.candidato.nuevoTrabajo'
        ])->find($user->id);

        // Obtener las aplicaciones del usuario
        $aplicaciones = HvOfCanOfertum::where('id_hoja_vida', session('hoja_vida'))
            ->pluck('idofoferta_laboral')
            ->toArray();

        // Inicializar porcentajes
        $experiencePercentage = 0;
        $educationPercentage = 0;
        $accountPercentage = 0;
        $profilePercentage = 0;
        $recommendedOffers = collect(); 
        $recommendedOffersBySector = collect(); 

        if ($perfil && $perfil->hojaVida && $perfil->hojaVida->candidato) {
            $candidato = $perfil->hojaVida->candidato;

            // Obtener ofertas recomendadas
            $recommendedOffers = $this->getRecommendedOffers($candidato);
            $recommendedOffersBySector = $this->getRecommendedOffersBySector($candidato);

            $experienceFields = 0;
            $totalExperienceFields = 6;
            if ($candidato->experienciasLaborales && $candidato->experienciasLaborales->count() > 0) {
                foreach ($candidato->experienciasLaborales as $exp) {
                    if ($exp->empresa) $experienceFields++;
                    if ($exp->id_tipo_cargo) $experienceFields++;
                    if ($exp->fecha_inicio) $experienceFields++;
                    if ($exp->descripcion_cargo) $experienceFields++;
                    if ($exp->id_sector) $experienceFields++;
                    if ($exp->id_area) $experienceFields++;
                    break; // Solo validamos la primera experiencia
                }
            }
            $experiencePercentage = ($experienceFields / $totalExperienceFields) * 100;

        
            $educationFields = 0;
            $totalEducationFields = 4;
            if ($candidato->formacionacademica && $candidato->formacionacademica->count() > 0) {
                foreach ($candidato->formacionacademica as $edu) {
                    if ($edu->institucion) $educationFields++;
                    if ($edu->titulo) $educationFields++;
                    if ($edu->id_nivel_educacion) $educationFields++;
                    if ($edu->fecha_inicio) $educationFields++;
                    break; // Solo validamos la primera formación
                }
            }
            $educationPercentage = ($educationFields / $totalEducationFields) * 100;

           
            $accountFields = 0;
            $totalAccountFields = 10;
            if ($candidato->nombres) $accountFields++;
            if ($candidato->apellidos) $accountFields++;
            if ($candidato->id_tipo_documento) $accountFields++;
            if ($candidato->id_genero) $accountFields++;
            if ($candidato->numero_documento) $accountFields++;
            if ($candidato->fecha_nacimiento) $accountFields++;
            if ($candidato->correo && $candidato->correo->email) $accountFields++;
            if ($candidato->telefono && $candidato->telefono->numero_telefono) $accountFields++;
            if ($candidato->telefono && $candidato->telefono->otro_numero_telefono) $accountFields++;
            if ($candidato->ubicacion) $accountFields++;

            
            $accountPercentage = ($accountFields / $totalAccountFields) * 100;

           
            $profileFields = 0;
            $totalProfileFields = 12;
            if ($candidato->perfil) {
                if ($candidato->perfil->descripcion_perfil) $profileFields++;
                if ($candidato->sector && $candidato->sector->count() > 0) $profileFields++;
                if ($candidato->areasPreferidas && $candidato->areasPreferidas->count() > 0) $profileFields++;
                if ($candidato->nuevoTrabajo) {
                    if ($candidato->nuevoTrabajo->id_rango_salario) $profileFields++;
                    if ($candidato->nuevoTrabajo->id_tipo_trabajo) $profileFields++;
                    if ($candidato->nuevoTrabajo->texto1) $profileFields++;
                    if ($candidato->nuevoTrabajo->texto2) $profileFields++;
                    if ($candidato->nuevoTrabajo->texto3) $profileFields++;
                    if ($candidato->nuevoTrabajo->texto4) $profileFields++;
                    if ($candidato->nuevoTrabajo->texto5) $profileFields++;
                    if ($candidato->nuevoTrabajo->texto6) $profileFields++;
                    if ($candidato->nuevoTrabajo->texto7) $profileFields++;
                }
            }
            $profilePercentage = ($profileFields / $totalProfileFields) * 100;
        }
        return view('candidate.dashboard.index', compact(
            'user',
            'experiencePercentage',
            'educationPercentage',
            'accountPercentage',
            'profilePercentage',
            'recommendedOffers',
            'recommendedOffersBySector',
            'aplicaciones'
        ));
    }

    private function getRecommendedOffers($candidato)
    {
        $cargoIds = $candidato->experienciasLaborales()
            ->pluck('id_tipo_cargo')
            ->unique()
            ->toArray();
       

                
        $areaIds = $candidato->experienciasLaborales()
            ->pluck('id_area')
            ->unique()
            ->toArray();

        $sectorIds = $candidato->experienciasLaborales()
            ->pluck('id_sector')
            ->unique()
            ->toArray();
       

        $ofertas = OfOfertaLaboral::where('id_estado', 1)
            ->with('ofertaSectores','ofertaAreas')
            ->whereHas('ofertaSectores', function($query) use ($sectorIds) {
                $query->whereIn('id_sector', $sectorIds);
            })
            ->whereHas('ofertaAreas', function($query) use ($areaIds) {
                $query->whereIn('id_area', $areaIds);
            })
            ->whereIn('id_cargo', $cargoIds)
            ->whereNull('fecha_cierre_at')
            ->with(['empresa', 'cargo', 'areas', 'sectores', 'ciudad', 'rangoSalarial'])
            ->orderBy('fecha_creacion', 'desc')
            ->take(5)
            ->get();
        

        return $ofertas;
    }

    private function getRecommendedOffersBySector($candidato)
    {

        $sectorIds = $candidato->experienciasLaborales()
            ->pluck('id_sector')
            ->unique()
            ->toArray();
         
        $ofertas = OfOfertaLaboral::where('id_estado', 1)
            ->with('ofertaSectores')
            ->whereHas('ofertaSectores', function($query) use ($sectorIds) {
                $query->whereIn('id_sector', $sectorIds);
            })
            ->whereNull('fecha_cierre_at')
            ->with(['empresa', 'cargo', 'areas', 'sectores', 'ciudad', 'rangoSalarial'])
            ->orderBy('fecha_creacion', 'desc')
            ->take(5) 
            ->get();
            
    
        return $ofertas;

    }
}
