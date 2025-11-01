@extends('layout.app') 

@section('title', 'Mi configuración')

@section('styles')    
<style>
    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #fff;
        height: 100%;
    }
    .form-section h4 {
        margin-bottom: 1.5rem;
        color: #333;
        font-family: 'Quicksand', serif !important;
        font-weight: bold;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-floating-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .form-floating-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e8ecec;
        border-radius: 10px;
        font-family: 'Quicksand', serif !important;
        font-size: 15px;
        transition: all 0.3s ease;
        background: transparent;
    }
    .form-floating-group input:focus {
        border-color: #0057b8;
        box-shadow: 0 0 0 0.2rem rgba(0,87,184,0.1);
        outline: none;
    }
    .floating-label {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        padding: 0 5px;
        color: #666;
        font-size: 15px;
        transition: all 0.3s ease;
        pointer-events: none;
    }
    .form-floating-group input:focus + .floating-label,
    .form-floating-group input:not(:placeholder-shown) + .floating-label {
        top: 0;
        font-size: 12px;
        color: #0057b8;
        background: white;
    }
    .btn-primary {
        background: linear-gradient(to right, #00a86b, #0057b8);
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 10px;
        font-family: 'Quicksand', serif !important;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .profile-image-container {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto 2rem;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #00a86b;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .profile-image-container:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .profile-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
        border-radius: 50%;
    }
    .profile-image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,168,107,0.9);
        padding: 0.75rem;
        text-align: center;
        opacity: 0;
        transition: all 0.3s ease;
    }
    .profile-image-container:hover .profile-image-overlay {
        opacity: 1;
    }
    .profile-image-overlay i {
        color: white;
        font-size: 24px;
    }
    .preview-image {
        max-width: 200px;
        margin: 1rem auto;
        display: none;
        position: relative;
    }
    .preview-image img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .preview-image .remove-preview {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .upload-btn {
        background: linear-gradient(to right, #00a86b, #0057b8);
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 10px;
        font-family: 'Quicksand', serif !important;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 1rem;
    }
    .upload-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .upload-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .error-message {
        color: #dc3545;
        font-size: 14px;
        margin-top: 0.5rem;
        display: none;
    }
    .success-message {
        color: #28a745;
        font-size: 14px;
        margin-top: 0.5rem;
        display: none;
    }
    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        z-index: 2;
    }
    .password-field {
        position: relative;
    }
    .icon-blue {
        color: #0057b8;
    }
    .text-center {
        text-align: center;
    }
    .profile-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .profile-section .btn-primary {
        margin-top: 1rem;
    }
</style>
@endsection

@section('content')
<div class="col-lg-12 column">
    <div class="padding-left">
        <div class="profile-form-edit">
            <div class="border-title">
                <h3><i class="las la-user-edit icon-blue" style="font-size: 1.5rem"></i>Mi configuración</h3>
            </div>
            <div class="edu-history-sec contact-edit">
                <div class="row">
                    <!-- Columna de Foto de Perfil -->
                    <div class="col-lg-6">
                        <div class="form-section profile-section">
                            <h4><i class="las la-camera icon-blue"></i> Foto de Perfil</h4>
                            <div class="profile-image-container" onclick="document.getElementById('profile_photo').click()">
                                @if($foto_url)
                                    <img src="{{ $foto_url }}" alt="Foto de perfil" id="current-profile-image">
                                @else
                                    <img src="{{ asset('images/profile.jpg') }}" alt="Foto de perfil" id="current-profile-image" 
                                         onerror="this.src='{{ asset('images/default-avatar.png') }}';">
                                @endif
                                <div class="profile-image-overlay">
                                    <i class="las la-camera"></i>
                                </div>
                            </div>
                            <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" id="photo-form">
                                @csrf
                                @method('PUT')
                                <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png" style="display: none;">
                                <div class="text-center mt-3">
                                    <small class="text-muted">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB</small>
                                </div>
                                <div id="photo-error-message" class="text-danger" style="display:none;"></div>
                            </form>
                        </div>
                    </div>

                    <!-- Columna de Cambio de Contraseña -->
                    <div class="col-lg-6">
                        <div class="form-section">
                            <h4><i class="las la-key icon-blue"></i> Cambiar Contraseña</h4>
                            <form action="{{ route('profile.update-password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-floating-group password-field">
                                    <input type="password" id="current_password" name="current_password" placeholder=" " required>
                                    <span class="floating-label">Contraseña Actual*</span>
                                    <i class="las la-eye password-toggle" onclick="togglePassword('current_password')"></i>
                                </div>
                                <div class="form-floating-group password-field">
                                    <input type="password" id="new_password" name="new_password" placeholder=" " required>
                                    <span class="floating-label">Nueva Contraseña*</span>
                                    <i class="las la-eye password-toggle" onclick="togglePassword('new_password')"></i>
                                </div>
                                <div class="form-floating-group password-field">
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder=" " required>
                                    <span class="floating-label">Confirmar Nueva Contraseña*</span>
                                    <i class="las la-eye password-toggle" onclick="togglePassword('new_password_confirmation')"></i>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-block btn-primary-custom">
                                        <i class="las la-save"></i> Actualizar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
   
    // Script para mostrar vista previa de la imagen
    // document.getElementById('profile_photo').addEventListener('change', function(e) {
    //     const file = e.target.files[0];
    //     const errorMessage = document.getElementById('photo-error-message');
    //     const uploadBtn = document.getElementById('upload-photo-btn');
    //     errorMessage.style.display = 'none';
    //     uploadBtn.style.display = 'none';
    //     if (file) {
    //         if (!file.type.startsWith('image/')) {
    //             errorMessage.textContent = 'Por favor, selecciona un archivo de imagen válido.';
    //             errorMessage.style.display = 'block';
    //             this.value = '';
    //             return;
    //         }
    //         if (file.size > 2 * 1024 * 1024) {
    //             errorMessage.textContent = 'La imagen no debe superar los 2MB.';
    //             errorMessage.style.display = 'block';
    //             this.value = '';
    //             return;
    //         }
    //         uploadBtn.style.display = 'inline-flex';
    //     }
    // });

    // Función para remover la vista previa
    function removePreview() {
        const preview = document.getElementById('image-preview');
        const fileInput = document.getElementById('profile_photo');
        const uploadBtn = document.getElementById('upload-photo-btn');
        const errorMessage = document.getElementById('error-message');
        const successMessage = document.getElementById('success-message');
        
        preview.style.display = 'none';
        fileInput.value = '';
        uploadBtn.style.display = 'none';
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';
    }

    // Función para alternar la visibilidad de la contraseña
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.nextElementSibling;
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('la-eye');
            icon.classList.add('la-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('la-eye-slash');
            icon.classList.add('la-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const profilePhoto = document.getElementById('profile_photo');
        const currentProfileImage = document.getElementById('current-profile-image');
        const photoForm = document.getElementById('photo-form');

        profilePhoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar el tipo de archivo
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    toastr.error('Solo se permiten archivos JPG y PNG');
                    this.value = '';
                    return;
                }

                // Validar el tamaño (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    toastr.error('El archivo no debe superar los 2MB');
                    this.value = '';
                    return;
                }

                // Mostrar loading
                currentProfileImage.style.opacity = '0.5';
                
                // Crear previsualización
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentProfileImage.src = e.target.result;
                    currentProfileImage.style.opacity = '1';
                }
                reader.readAsDataURL(file);

                // Agregar listener para recargar después del submit
                photoForm.addEventListener('submit', function() {
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                });

                // Enviar el formulario automáticamente
                photoForm.submit();
            }
        });
    });
</script>
@endpush
@endsection



