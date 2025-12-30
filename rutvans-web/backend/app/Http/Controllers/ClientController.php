<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Coordinate;
use App\Models\User;
use App\Models\Locality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    public function __construct()
    {
        // Solo admin puede acceder (no super-admin ni coordinate)
        $this->middleware(['auth', 'verified']);
        $this->middleware('can:admin'); // Cambio: solo admin
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Site::with(['locality', 'users', 'company']);

        // Solo admin puede acceder, así que siempre filtramos por su empresa
        $userCompany = CompanyUser::where('user_id', $user->id)
            ->where('role', 'admin')
            ->first();
        
        if ($userCompany) {
            $query->where('company_id', $userCompany->company_id);
        } else {
            // Si no tiene empresa asignada, no ve ningún site
            $query->where('company_id', -1);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $sites = $query->orderBy('name')->paginate(10);
        
        // Solo mostrar la empresa del admin
        $companies = $userCompany ? 
            Company::where('id', $userCompany->company_id)->where('status', 'active')->get() : 
            collect();
        
        $localities = Locality::orderBy('locality')->get();

        return view('rutvans.sites.index', compact('sites', 'companies', 'localities'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validationRules = [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'locality_id' => 'required|exists:localities,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
        ];

        $request->validate($validationRules);

        // Solo admin puede acceder, verificar que el company_id corresponde a su empresa
        $userCompany = CompanyUser::where('user_id', $user->id)
            ->where('role', 'admin')
            ->first();
        
        if (!$userCompany || $userCompany->company_id != $request->company_id) {
            return response()->json([
                'message' => 'No tienes permisos para crear sitios en esta empresa.'
            ], 403);
        }

        Site::create([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'route_name' => $request->route_name,
            'locality_id' => $request->locality_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        // Devolver JSON en lugar de redirigir para peticiones AJAX
        return response()->json(['message' => 'Sitio/Terminal creado exitosamente.']);
    }

    public function update(Request $request, Site $client)
    {
        $user = Auth::user();
        
        $validationRules = [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'locality_id' => 'required|exists:localities,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
        ];

        $request->validate($validationRules);

        // Solo admin puede acceder, verificar que el company_id corresponde a su empresa
        $userCompany = CompanyUser::where('user_id', $user->id)
            ->where('role', 'admin')
            ->first();
        
        if (!$userCompany || $userCompany->company_id != $request->company_id) {
            return response()->json([
                'message' => 'No tienes permisos para editar sitios de esta empresa.'
            ], 403);
        }
        
        // También verificar que el site actual pertenece a su empresa
        if ($client->company_id != $userCompany->company_id) {
            return response()->json([
                'message' => 'No tienes permisos para editar este sitio.'
            ], 403);
        }

        $client->update([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'route_name' => $request->route_name,
            'locality_id' => $request->locality_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        // Devolver JSON en lugar de redirigir para peticiones AJAX
        return response()->json(['message' => 'Sitio/Terminal actualizado exitosamente.']);
    }

    public function destroy(Site $client)
    {
        DB::transaction(function () use ($client) {
            // Desasociar usuarios para evitar problemas (si quieres puedes quitar esta parte)
            $client->users()->detach();

            $client->delete();
        });

        return redirect()->route('clients.index')
            ->with('success', 'Sitio/Terminal eliminado exitosamente.');
    }

    public function show(Site $client)
    {
        $client->load(['locality', 'company', 'users.roles']);

        $coordinators = $client->users->filter(function ($user) {
            return $user->hasRole('coordinate');
        });

        $coordinatorUserId = DB::table('site_users')
            ->where('site_id', $client->id)
            ->where('role', 'coordinator')
            ->value('user_id');

        $assignedCoordinator = null;

        if ($coordinatorUserId) {
            $assignedCoordinator = User::with('roles')
                ->where('id', $coordinatorUserId)
                ->first();

            if ($assignedCoordinator) {
                $coordinateData = Coordinate::where('user_id', $assignedCoordinator->id)->first();

                $assignedCoordinator->employee_code = $coordinateData->employee_code ?? null;
                // Usar foto de coordinates, si no existe usar la de users
                $assignedCoordinator->photo = $coordinateData->photo ?? $assignedCoordinator->profile_photo_path ?? null;
                $assignedCoordinator->coordinate_id = $coordinateData->id ?? null; // 👈 necesario para el update
            }
        }

        return view('rutvans.sites.asignar.index', [
            'site' => $client,
            'coordinators' => $coordinators,
            'assignedCoordinator' => $assignedCoordinator,
        ]);
    }



    // public function show(Site $client)
    // {
    //     $client->load(['locality', 'users', 'company']);

    //     $stats = [
    //         'drivers' => $client->users()->whereHas('roles', function ($query) {
    //             $query->where('name', 'driver');
    //         })->count(),
    //         'cashiers' => $client->users()->whereHas('roles', function ($query) {
    //             $query->where('name', 'cashier');
    //         })->count(),
    //         'coordinates' => 0,
    //         'units' => 0,
    //     ];

    //     return response()->json([
    //         'site' => $client,
    //         'stats' => $stats
    //     ]);
    // }
}
