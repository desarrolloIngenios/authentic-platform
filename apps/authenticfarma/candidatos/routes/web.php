<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

// Controladores de Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
// Controladores de usuario
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Candidate\EducationController;
use App\Http\Controllers\Candidate\EducationAdController;
use App\Http\Controllers\Candidate\LanguageController;
use App\Http\Controllers\Candidate\JobController;
use App\Http\Controllers\Candidate\AccountController;
use App\Http\Controllers\Candidate\PostulationController;
use App\Http\Controllers\Candidate\VacantController as VacantController;
use App\Http\Controllers\Candidate\DashboardController as CandidateDashboardController;
use App\Http\Controllers\Candidate\CandidateController as CandidateController;

// Controladores de admin
use App\Http\Controllers\Admin\CandidateController as AdminCandidateController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\VacantController as AdminVacantController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;

// Controladores de IA
use App\Http\Controllers\AI\GeminiController;

use App\Http\Controllers\PlanController;


// =====================
// RUTAS DE SISTEMA
// =====================

// Health check para Kubernetes
Route::get('/healthz', function () {
    return response()->json(['status' => 'ok'], 200);
});

// Página principal con lógica de autenticación
Route::get('/', function () {
    return Auth::guest()
        ? redirect()->route('login.index')
        : redirect()->route('account.index');
});

// =====================
// RUTAS DE AUTENTICACIÓN
// =====================
Route::middleware('guest')->group(function () {
    // Login y registro
    Route::get('/login', [LoginController::class, 'index'])->name('login.index');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/register', [RegisterController::class, 'index'])->name('register.index');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

    // Activación de cuenta
    Route::get('/activate/{token}', [RegisterController::class, 'activateAccount'])->name('activate.account');

    // Recuperación de contraseña
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

    // Autenticación con Google
    Route::get('/google-auth/redirect', [GoogleController::class, 'redirect'])->name('google-auth.redirect');
    Route::get('/google-auth/callback', [GoogleController::class, 'callback'])->name('google-auth.callback');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// =====================
// RUTAS DE POLÍTICAS Y DATOS
// =====================
Route::view('/management-data', 'management-data.index')->name('management-data.index');
Route::view('/policies-data', 'policies-data.index')->name('policies-data.index');

// =====================
// RUTAS DE USUARIO AUTENTICADO
// =====================
Route::middleware(['auth', 'expiredSession', 'user', 'handleSessionError'])->group(function () {
    // Perfil y cuenta
    Route::resource('profile', ProfileController::class)->only(['index', 'edit', 'update', 'upload']);
    Route::post('/profile/upload', [ProfileController::class, 'upload'])->name('profile.upload');
    Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::put('/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::resource('account', AccountController::class)->only(['index', 'store']);

    // Formación académica
    Route::resource('educacion', EducationController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/education/modal-create', [EducationController::class, 'modalCreate']);
    Route::get('/education/modal-edit/{id}', [EducationController::class, 'modalEdit']);
    // Formación adicional
    Route::resource('educationad', EducationAdController::class)->only(['store', 'update', 'destroy']);
    Route::get('/educationad/modal-create', [EducationAdController::class, 'modalCreate']);
    Route::get('/educationad/modal-edit/{id}', [EducationAdController::class, 'modalEdit']);
    // Idiomas
    Route::resource('language', LanguageController::class)->only(['store', 'update', 'destroy']);
    Route::get('/language/modal-create', [LanguageController::class, 'modalCreate']);
    Route::get('/language/modal-edit/{id}', [LanguageController::class, 'modalEdit']);
    // Experiencia laboral
    Route::resource('job', JobController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/job/modal-create', [JobController::class, 'modalCreate']);
    Route::get('/job/modal-edit/{id}', [JobController::class, 'modalEdit']);
    // Vacantes
    Route::resource('vacant', VacantController::class);
    Route::get('/vacante/{id}', [VacantController::class, 'showDrawer']);
    Route::post('/vacante/{id}/aplicar', [VacantController::class, 'aplicar'])->name('vacante.aplicar');
    // Postulaciones
    Route::resource('postulation', PostulationController::class)->only(['index']);
    Route::get('/postulation/{id}/vacant', [PostulationController::class, 'showDrawer']);
    // Candidato
    Route::resource('candidate', CandidateController::class);
    // Tablero
    Route::resource('dashboard', CandidateDashboardController::class);
});

// =====================
// RUTAS DE ADMINISTRADOR
// =====================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard y planes
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/plans', [AdminPlanController::class, 'index'])->name('plan.index');
    Route::post('/plans', [AdminPlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{id}', [AdminPlanController::class, 'update'])->name('plans.update');
    Route::get('/plans/{id}', [AdminPlanController::class, 'show'])->name('plans.show');
    // Candidatos
    Route::get('/candidates', [AdminCandidateController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/{id}', [AdminCandidateController::class, 'show'])->name('candidates.show');
    // Vacantes
    Route::get('/vacantes', [AdminVacantController::class, 'index'])->name('admin.vacant.index');
    Route::get('/vacante/{id}', [AdminVacantController::class, 'showDrawerAdmin'])->name('admin.vacant.show');
    Route::get('/vacantes/{id}/postulados', [AdminVacantController::class, 'postulados'])->name('admin.vacant.postulados');
});

// =====================
// RUTAS PÚBLICAS DE VACANTES
// =====================
Route::get('/ver-vacante/{id}', [VacantController::class, 'showPublic'])->name('vacante.public');
Route::get('/vacantes', [VacantController::class, 'index'])->name('public.vacant.index');
Route::get('/vacantes/relevantes', [VacantController::class, 'getRelevantVacancies'])->name('vacant.relevant');

Route::get('/healthz', function () {
    return response()->json(['status' => 'ok'], 200);
});

// ================================
// RUTAS DE IA - GEMINI
// ================================
Route::middleware(['auth'])->prefix('ai')->name('ai.')->group(function () {
    // Test de conectividad
    Route::get('/test', [GeminiController::class, 'testConnection'])->name('test');
    
    // Análisis de CV
    Route::post('/analyze-cv', [GeminiController::class, 'analyzeCV'])->name('analyze.cv');
    
    // Generar preguntas de entrevista
    Route::post('/interview-questions', [GeminiController::class, 'generateInterviewQuestions'])->name('interview.questions');
    
    // Matching candidato-puesto
    Route::post('/match-candidate', [GeminiController::class, 'matchCandidate'])->name('match.candidate');
    
    // Estadísticas de uso de IA
    Route::get('/stats', [GeminiController::class, 'getAIStats'])->name('stats');
});
