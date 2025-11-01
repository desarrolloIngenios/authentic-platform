<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class OfOfertaLaboral
 * 
 * @property int $idofoferta_laboral
 * @property string|null $descripcion
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $titulo
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int|null $id_area
 * @property int $id_empresa
 * @property int $id_estado
 * @property bool $is_confidencial
 * @property int $numero_vacantes
 * @property int|null $id_cargo
 * @property int|null $id_idioma
 * @property int|null $id_nivel_idioma
 * @property int|null $id_sector
 * @property int|null $id_nivel_educacion
 * @property int|null $id_ciudad
 * @property int|null $id_tiempo_experiencia
 * @property int|null $sector_porcentaje
 * @property int|null $area_porcentaje
 * @property int|null $cargo_porcentaje
 * @property int|null $idioma_porcentaje
 * @property int|null $experiencia_porcentaje
 * @property int|null $educacion_porcentaje
 * @property int|null $id_rango_salario
 * @property Carbon|null $fecha_cierre_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|HvOfCanOfertaFavorito[] $favoritos
 * @property-read \Illuminate\Database\Eloquent\Collection|HvOfCanOfertum[] $postulaciones
 *
 * @package App\Models
 */
class OfOfertaLaboral extends Model
{
	protected $table = 'of_oferta_laboral';
	protected $primaryKey = 'idofoferta_laboral';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idofoferta_laboral' => 'int',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_area' => 'int',
		'id_empresa' => 'int',
		'id_estado' => 'int',
		'is_confidencial' => 'bool',
		'numero_vacantes' => 'int',
		'id_cargo' => 'int',
		'id_idioma' => 'int',
		'id_nivel_idioma' => 'int',
		'id_sector' => 'int',
		'id_nivel_educacion' => 'int',
		'id_ciudad' => 'int',
		'id_tiempo_experiencia' => 'int',
		'sector_porcentaje' => 'int',
		'area_porcentaje' => 'int',
		'cargo_porcentaje' => 'int',
		'idioma_porcentaje' => 'int',
		'experiencia_porcentaje' => 'int',
		'educacion_porcentaje' => 'int',
		'id_rango_salario' => 'int',
		'fecha_cierre_at' => 'datetime'
	];

	protected $fillable = [
		'descripcion',
		'fecha_creacion',
		'fecha_modificacion',
		'titulo',
		'usuario_creacion',
		'usuario_modificacion',
		'id_area',
		'id_empresa',
		'id_estado',
		'is_confidencial',
		'numero_vacantes',
		'id_cargo',
		'id_idioma',
		'id_nivel_idioma',
		'id_sector',
		'id_nivel_educacion',
		'id_ciudad',
		'id_tiempo_experiencia',
		'sector_porcentaje',
		'area_porcentaje',
		'cargo_porcentaje',
		'idioma_porcentaje',
		'experiencia_porcentaje',
		'educacion_porcentaje',
		'id_rango_salario',
		'fecha_cierre_at'
	];

	public function candidatosHojasVida()
    {
        return $this->belongsToMany(HvHojaVida::class, 'hv_of_can_oferta', 'idofoferta_laboral', 'id_hoja_vida', 'idofoferta_laboral', 'id_hoja_vida')->with('candidato')->withPivot(['ai']);;
    }
	
	public function OfHvSelecionados()
	{
		return $this->belongsToMany(HvHojaVida::class, 'oferta_and_hoja_vida_seleccionados', 'oferta_laboral_id', 'hoja_vida_id');
	}
	
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'oferta_area', 'id_oferta', 'id_area');
    }

	public function cargos()
	{
		return $this->belongsToMany(TipoCargo::class, 'oferta_laboral_cargo', 'id_oferta_laboral', 'id_cargo');
	}

    public function sectores()
    {
        return $this->belongsToMany(Sector::class, 'oferta_sector', 'id_oferta', 'id_sector');
    }

	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'id_empresa');
	}

	public function cargo()
	{
		return $this->belongsTo(TipoCargo::class, 'id_cargo');
	}

	public function idioma()
	{
		return $this->belongsTo(Idioma::class, 'id_idioma');
	}

	public function nivelIdioma()
	{
		return $this->belongsTo(NivelIdioma::class, 'id_nivel_idioma');		
	}

	public function nivelEducacion()
	{
		return $this->belongsTo(NivelEducacion::class, 'id_nivel_educacion');
	}

	public function ciudad()
	{
		return $this->belongsTo(Ciudad::class, 'id_ciudad');
	}

	public function tiempoExperiencia()
	{
		return $this->belongsTo(TiempoExperiencium::class, 'id_tiempo_experiencia');
	}
	
	public function rangoSalarial()
	{
		return $this->belongsTo(RangoSalario::class, 'id_rango_salario');
	}

	/**
     * Obtiene las marcaciones de favoritos de esta oferta laboral
     */
    public function favoritos(): HasMany
    {
        return $this->hasMany(HvOfCanOfertaFavorito::class, 'idofoferta_laboral', 'idofoferta_laboral');
    }

    /**
     * Obtiene todas las postulaciones realizadas a esta oferta laboral
     */
    public function postulaciones(): HasMany
    {
        return $this->hasMany(HvOfCanOfertum::class, 'idofoferta_laboral', 'idofoferta_laboral')
            ->with(['hojaVida', 'hojaVida.candidato', 'estado']);
    }

    public function ofertaSectores()
    {
        return $this->hasMany(OfertaSector::class, 'id_oferta', 'idofoferta_laboral');
    }

    public function ofertaAreas()
    {
        return $this->hasMany(OfertaArea::class, 'id_oferta', 'idofoferta_laboral');
    }
}
