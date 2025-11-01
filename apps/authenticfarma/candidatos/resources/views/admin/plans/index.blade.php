@extends('layout.admin')

@section('title', 'Planes')

@section('styles')
<style>
    .plans-section {
        padding: 2rem 0;
        max-width: 900px;
        margin: 0 auto;
        width: 100%;
    }
    .plans-title {
        color: #111;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .plans-title i {
        font-size: 2.1rem;
        color: #00a86b;
        margin-right: 0.5rem;
    }
    .btn-new-plan {
        margin-left: auto;
        background: #00a86b;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.2rem;
        font-size: 1rem;
        font-weight: 600;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-new-plan:hover {
        background: #0057b8;
        color: #fff;
    }
    .plans-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 1.5rem;
    }
    .plan-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 168, 107, 0.08);
        padding: 2rem 1.5rem 1.5rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        transition: box-shadow 0.3s, transform 0.3s;
        border: 1px solid #e0e0e0;
        position: relative;
    }
    .plan-card:hover {
        box-shadow: 0 8px 24px rgba(0, 168, 107, 0.15);
        transform: translateY(-4px) scale(1.02);
        border-color: #00a86b;
    }
    .plan-edit {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        color: #0057b8;
        font-size: 1.3rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .plan-edit:hover {
        color: #00a86b;
    }
    .plan-name {
        font-size: 1.3rem;
        font-weight: 600;
        color: #0057b8;
        margin-bottom: 0.5rem;
    }
    .plan-description {
        color: #666;
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    .plan-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #00a86b;
        margin-bottom: 0.5rem;
    }
    .plan-image {
        width: 100%;
        max-width: 120px;
        margin-bottom: 1rem;
        border-radius: 8px;
        object-fit: cover;
        background: #f4f6f8;
    }
    .plan-status {
        font-size: 0.95rem;
        font-weight: 500;
        color: #fff;
        background: #00a86b;
        border-radius: 6px;
        padding: 0.2rem 0.7rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }
    .plan-status.inactive {
        background: #b0b0b0;
    }
    @media (max-width: 600px) {
        .plans-section {
            padding: 1rem 0.5rem;
        }
        .plans-title {
            font-size: 1.3rem;
        }
        .plan-card {
            padding: 1.2rem 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="plans-section">
    <div class="d-flex align-items-center mb-3">
        <h2 class="plans-title">
            <i class="la la-briefcase"></i>
            Planes disponibles
        </h2>
        <button class="btn-new-plan" data-modal-target="modalCrearPlan">
            <i class="la la-plus"></i> Nuevo plan
        </button>
    </div>
    <div class="plans-list">
        @forelse ($plans as $plan)
            <div class="plan-card">
                <button class="plan-edit" title="Editar" data-modal-target="modalEditarPlan" data-id="{{ $plan->id }}">
                    <i class="la la-pencil"></i>
                </button>
                @if($plan->image_url)
                    <img src="{{ $plan->image_url }}" alt="Imagen del plan" class="plan-image" />
                @endif
                <div class="plan-name">{{$plan->name}}</div>
                @if(isset($plan->price))
                    <div class="plan-price">${{ number_format($plan->price, 0, ',', '.') }}</div>
                @endif
                <span class="plan-status {{ $plan->status !== 'active' ? 'inactive' : '' }}">
                    {{ $plan->status === 'active' ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        @empty
            <div>No hay planes disponibles.</div>
        @endforelse
    </div>
</div>
@include('admin.plans.modals.create')
@include('admin.plans.modals.edit')
@endsection