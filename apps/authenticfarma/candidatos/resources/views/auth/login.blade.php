@extends('layout.auth') 

@section('title', 'Iniciar sesión')


@section('content')
    <div class="top-logo d-none d-md-block">
        <a href="https://www.authenticfarma.com/"><img src="{{ asset("images/logo-authenticfarma-white.png") }}" alt="Logo Authentic" /></a>
    </div>

    <div class="login-wrapper">
        <div class="login-card text-center">
        <h4>¡Hola! Inicia sesión con tu correo</h4>        <form action="{{ route('login') }}" method="POST">
            @csrf
            @if(isset($redirectTo))
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @endif
            
            <div class="form-group text-left">
                {{-- <div class="form-floating-group">
                    <input type="email" id="email" name="email" placeholder=" " value="" required />
                    <span class="floating-label">Correo*</span>
                </div> --}}
                <input type="email" class="form-control" name="email" id="email" placeholder="correo electronico" required />
            </div>
            
            <div class="form-group text-left">
                {{-- <div class="form-floating-group">
                    <input type="password" id="password" name="password" placeholder=" " value="" required />
                    <span class="floating-label">Contraseña*</span>
                </div> --}}
                <input type="password" class="form-control" name="password" id="password" placeholder="contraseña" required />
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-3">Iniciar Sesión</button>

            <div class="my-3">o</div>

            <a class="btn btn-google btn-block" href="/google-auth/redirect" title="Iniciar sesión con Google">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 99 101" width="20" height="20">
                    <g fill-rule="nonzero">
                        <path fill="#15B457" d="M97.4923998,56.9161607 C94.9859282,82.360637 76.5285081,99.7776415 49.8926655,99.7776415 C24.6022856,99.7776415 3.89511874,81.0621896 0.490861066,56.9159694 L19.7232209,56.9157943 C22.8767058,70.9520988 35.2678722,81.3180399 49.8828998,81.3180399 C69.591995,81.3180399 76.8255093,67.1048496 78.0652569,59.8713353 L78.0652569,59.8713353 L49.8828998,59.8713353 L49.8820158,56.915293 L97.4923998,56.9161607 Z"></path>
                        <path fill="#278BE6" d="M84.0591115,86.8041791 C92.7861822,78.0771084 97.7744595,65.6015177 97.7744595,51.1345087 C97.7744595,47.893587 97.5206525,45.3945606 97.0227994,42.9052902 L49.8829985,42.9052902 L49.8829985,59.8713255 L78.0653555,59.8713255 C77.3136954,64.1079613 74.5706249,70.8436039 68.5866238,75.5878477 L84.0591027,86.8041791 L84.0591115,86.8041791 Z"></path>
                        <path fill="#F0BB2F" d="M5.9840011,73.586693 C2.24522722,66.6069779 -7.63833441e-14,58.6218221 -7.63833441e-14,49.8947612 C-7.63833441e-14,42.407438 1.49355643,35.4277522 4.49043491,29.1899519 L20.4510004,40.4160392 C19.4552961,43.4129177 18.9574439,46.6538394 18.9574439,49.8947709 C18.9574439,54.1314067 19.709104,58.3777984 21.4564703,62.1165625 L5.98399134,73.586693 L5.9840011,73.586693 Z"></path>
                        <path fill="#EC3838" d="M4.49044468,29.1899519 C12.2218111,11.9798733 29.6856888,0.002125 49.8828803,0.002125 C63.354185,0.002125 74.5802723,4.99041205 83.3073333,12.9755776 L69.8360285,25.9490302 C66.0972546,22.4542996 59.6056455,18.2176638 49.8828803,18.2176638 C35.9137234,18.2176638 23.9457408,27.9502044 20.1972012,41.1676905 L4.49044468,29.1899422 L4.49044468,29.1899519 Z"></path>
                    </g>
                </svg>
                Continua con Google
            </a>            <div class="mt-3 text-muted">
            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </div>
            <hr />
            <p class="text-muted mb-0">¿No tienes cuenta? <a href="{{ route('register.index') }}">Regístrate ahora</a></p>
        </form>
        </div>
    </div>

@endsection