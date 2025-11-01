@extends('layout.auth') 

@section('title', 'Nueva cuenta')


@section('content')

<div class="top-logo d-none d-md-block">
    <a href="https://www.authenticfarma.com/"><img src="{{ asset("images/logo-authenticfarma-white.png") }}" alt="Logo Authentic" /></a>
</div>
<div class="login-wrapper py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="login-card bg-white p-4 p-md-5 rounded-lg shadow-sm text-center">
          <h4 class="mb-4" style="color: #0057b8;">Crear una cuenta</h4>

          <form action="{{ route('register.store') }}" method="POST" id="registerForm">
            @csrf

            <div class="cfield">
              <input type="text" name="nombre" class="form-control" placeholder="Nombres" value="{{ old('nombre') }}" required>
            </div>

            <div class="cfield">
              <input type="text" name="apellido" class="form-control" placeholder="Apellidos" value="{{ old('apellido') }}" required>
            </div>

            <div class="cfield">
              <input type="email" name="email" class="form-control" placeholder="Correo electrónico" value="{{ old('email') }}" required>
            </div>

            <div class="cfield">
              <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>

            <div class="cfield">
              <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar contraseña" required>
            </div>

            <div class="form-group form-check text-left">
              <input class="form-check-input" type="checkbox" name="policies_data" id="policies-data">
              <label class="form-check-label" for="policies-data">
                He leído y acepto la <a target="_blank" href="{{ route('policies-data.index') }}" style="color: #00a86b;">política de privacidad de datos</a>.
              </label>
            </div>

            <div class="form-group form-check text-left">
              <input class="form-check-input" type="checkbox" name="management_data" id="management-data">
              <label class="form-check-label" for="management-data">
                Autorizo el tratamiento de mis datos personales de acuerdo a la <a target="_blank" href="{{ route('management-data.index') }}" style="color: #00a86b;">política de manejo de datos</a>.
              </label>
            </div>

            <button type="submit" class="btn btn-block mt-3 text-white" style="background-color: #00a86b;" id="registerButton" disabled>
              Registrarse
            </button>

            <hr />

            <p class="text-muted mb-0">¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color: #00a86b;">Inicia sesión aquí</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', function () {
        const registerButton = document.getElementById('registerButton');
        const policiesDataCheckbox = document.getElementById('policies-data');
        const managementDataCheckbox = document.getElementById('management-data');

        function toggleRegisterButton() {
            registerButton.disabled = !(policiesDataCheckbox.checked && managementDataCheckbox.checked);
        }

        policiesDataCheckbox.addEventListener('change', toggleRegisterButton);
        managementDataCheckbox.addEventListener('change', toggleRegisterButton);
    });
</script>
@endsection
