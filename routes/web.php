<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthHomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ElectionsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
	Route::group(['middleware' => 'auth'], function(){
		// logout route
		Route::get('/logout', [LoginController::class,'logout']);
		Route::get('/clear-cache', [HomeController::class,'clearCache']);

		// dashboard route  
		Route::get('/', [HomeController::class,'index']);
		Route::get('/dashboard', [AuthHomeController::class,'index']);
		//only those have manage_user permission will get access
		Route::group(['middleware' => 'can:manage_user'], function(){
		Route::get('users', [UserController::class,'index']);
		Route::get('list-users', [UserController::class,'listUsers']);
		Route::get('/user/get-list', [UserController::class,'getUserList']);
			Route::get('/user/create', [UserController::class,'create']);
			Route::post('/user/create', [UserController::class,'store'])->name('create-user');
			Route::get('/user/{id}', [UserController::class,'edit']);
			Route::post('/user/edit-etat', [UserController::class,'update']);
			Route::get('/user/delete/{id}', [UserController::class,'delete']);
			Route::post('/users/new-add', [UserController::class,'newuseradd']);
			Route::post('/users/edit-etat', [UserController::class,'editetat']);
			Route::post('/users/new-update', [UserController::class,'newuserupdate']);
		});

		//only those have manage_role permission will get access
		Route::group(['middleware' => 'can:manage_role|manage_user'], function(){
			Route::get('/roles', [RolesController::class,'index']);
			Route::get('/role/get-list', [RolesController::class,'getRoleList']);
			Route::post('/role/create', [RolesController::class,'create']);
			Route::get('/role/edit/{id}', [RolesController::class,'edit']);
			Route::post('/role/update', [RolesController::class,'update']);
			Route::get('/role/delete/{id}', [RolesController::class,'delete']);
		});

		//only those have manage_permission permission will get access
		Route::group(['middleware' => 'can:manage_permission|manage_user'], function(){
			Route::get('/permission', [PermissionController::class,'index']);
			Route::get('/permission/get-list', [PermissionController::class,'getPermissionList']);
			Route::post('/permission/create', [PermissionController::class,'create']);
			Route::get('/permission/update', [PermissionController::class,'update']);
			Route::get('/permission/delete/{id}', [PermissionController::class,'delete']);
		});
		//only those have manage_grades grades will get access
		Route::group(['middleware' => 'can:manage_grades|manage_user'], function(){
			Route::get('/grades', [GradesController::class,'index']);
			Route::get('/grades/get-list', [GradesController::class,'getGradesList']);
			Route::post('/grades/create', [GradesController::class,'create']);
			Route::post('/grades/update', [GradesController::class,'update']);
			Route::get('/grades/delete/{id}', [GradesController::class,'delete']);
		});
		//only those have manage_categories categories will get access
		Route::group(['middleware' => 'can:manage_categories|manage_user'], function(){
			Route::get('/categories', [CategoriesController::class,'index']);
			Route::get('/categories/get-list', [CategoriesController::class,'getCategoriesList']);
			Route::post('/categories/create', [CategoriesController::class,'create']);
			Route::post('/categories/update', [CategoriesController::class,'update']);
			Route::get('/categories/delete/{id}', [CategoriesController::class,'delete']);
		});
		//only those have manage_elections elections will get access
		Route::group(['middleware' => 'can:manage_elections|manage_user'], function(){
			Route::get('/elections', [ElectionsController::class,'index']);
			Route::get('/elections/get-list', [ElectionsController::class,'getElectionsList']);
			Route::post('/elections/create', [ElectionsController::class,'create']);
			Route::post('/elections/update-etat', [ElectionsController::class,'update_etat']);
			Route::post('/elections/update', [ElectionsController::class,'update']);
			Route::get('/elections/delete/{id}', [ElectionsController::class,'delete']);
			Route::get('/get-data-user-by-grade-categorie/{id_grade}/{id_categorie}', [ElectionsController::class,'getUserTete2ListeByGradeWithCategorie']);
			Route::get('/get-data-user-by-grade/{id_grade}', [ElectionsController::class,'getUserTete2ListeByGrade']);
			Route::get('/get-data-user-by-categorie/{id_categorie}', [ElectionsController::class,'getUserTete2ListeByCategorie']);
		});

		// get permissions
		Route::get('get-role-permissions-badge', [PermissionController::class,'getPermissionBadgeByRole']);
	});

	//Route::get('/', function () { return view('home'); });
	Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
	Route::post('login', [LoginController::class,'login']);
	Route::post('register', [RegisterController::class,'register']);
	Route::get('/register', function () { return view('pages.register'); });
	Route::get('/login-1', function () { return view('pages.login'); });

	Route::get('/', [HomeController::class,'index']);
	Route::get('password/forget',  function () { 
		return view('pages.forgot-password'); 
	})->name('password.forget');
	Route::get('password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');

	Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
	Route::post('password/reset', [ResetPasswordController::class,'reset'])->name('password.update');
