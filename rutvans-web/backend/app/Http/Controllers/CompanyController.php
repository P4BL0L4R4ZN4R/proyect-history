<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('can:manage-companies-and-sites');
    }

    public function index()
    {
        $companies = Company::with(['locality', 'sites', 'users' => function($query) {
            $query->where('company_users.role', 'admin');
        }])->paginate(10);

        foreach ($companies as $company) {
            $admin = CompanyUser::where('company_id', $company->id)
                ->where('role', 'admin')
                ->first();
            if ($admin) {
                $user = User::find($admin->user_id);
                $company->admin_id = $user->id ?? null;
                $company->admin_name = $user->name ?? '';
                $company->admin_email = $user->email ?? '';
            } else {
                // Cuando no hay admin, establecer valores por defecto
                $company->admin_id = null;
                $company->admin_name = '';
                $company->admin_email = '';
            }
        }
        $localities = Locality::orderBy('locality')->get();
        
        return view('rutvans.companies.index', compact('companies', 'localities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:13',
            'locality_id' => 'nullable|exists:localities,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            // Datos del usuario admin principal
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($request) {
            // Crear la empresa/sindicato
            $company = Company::create($request->only([
                'name', 'business_name', 'rfc', 'locality_id', 
                'address', 'phone', 'email', 'status', 'notes'
            ]));

            // Crear el usuario admin principal
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'email_verified_at' => now()
            ]);

            // Asignar rol de admin en el sistema
            $user->assignRole('admin');

            // Vincular el usuario como dueño de la compañía
            CompanyUser::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'role' => 'admin',
                'status' => 'active'
            ]);
        });

        return redirect()->route('companies.index')
            ->with('success', 'Empresa/Sindicato creado exitosamente.');
    }

    public function show(Company $company)
    {
        $company->load(['locality', 'sites']);
        
        // Calcular estadísticas
        $stats = [
            'sites' => $company->sites->count(),
            'active_sites' => $company->sites->where('status', 'active')->count(),
            'total_users' => \App\Models\CompanyUser::where('company_id', $company->id)->count()
        ];
        
        return response()->json([
            'company' => $company,
            'stats' => $stats
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:13',
            'locality_id' => 'nullable|exists:localities,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $company->update($request->only([
            'name', 'business_name', 'rfc', 'locality_id', 
            'address', 'phone', 'email', 'status', 'notes'
        ]));

        return redirect()->route('companies.index')
            ->with('success', 'Empresa/Sindicato actualizado exitosamente.');
    }

    public function updateAdmin(Request $request, Company $company)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email,' . $request->input('admin_id'),
            'admin_password' => 'nullable|string|min:6',
        ]);

        $user = User::find($request->input('admin_id'));

        if ($user) {
            $user->name = $request->admin_name;
            $user->email = $request->admin_email;

            if ($request->filled('admin_password')) {
                $user->password = Hash::make($request->admin_password);
            }

            $user->save();
        }

        return response()->json(['success' => 'Administrador actualizado exitosamente.']);
    }

    public function storeAdmin(Request $request, Company $company)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($request, $company) {
            // Crear el nuevo usuario admin
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'email_verified_at' => now()
            ]);

            // Asignar rol de admin en el sistema
            $user->assignRole('admin');

            // Vincular el usuario como admin de la compañía
            CompanyUser::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'role' => 'admin',
                'status' => 'active'
            ]);
        });

        return response()->json(['success' => 'Administrador agregado exitosamente.']);
    }

    public function destroy(Company $company)
    {
        DB::transaction(function () use ($company) {
            // Eliminar todos los sitios de la empresa (cascade eliminará usuarios asociados)
            $company->sites()->delete();
            
            // Eliminar la empresa
            $company->delete();
        });

        return redirect()->route('companies.index')
            ->with('success', 'Empresa/Sindicato eliminado exitosamente.');
    }
}
