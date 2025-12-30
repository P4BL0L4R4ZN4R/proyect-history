

@hasanyrole(['superadmin' , 'admin'])
@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')

@stop

@section('content')




    <div class="container">
        <h1>Lista de Usuarios</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Agregar Usuario</a>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentUser = auth()->user();
                            $isCurrentAdmin = $currentUser->hasRole('admin');
                            $isCurrentSuperAdmin = $currentUser->hasRole('superadmin');
                        @endphp

                        @foreach($users as $user)
                            @php
                                $isUserSuperAdmin = $user->hasRole('superadmin');
                                $isUserAdmin = $user->hasRole('admin');
                            @endphp

                            @if($isCurrentAdmin && $isUserSuperAdmin)
                                @continue
                            @endif

                            <tr @class([
                                    'table-info' => $isCurrentSuperAdmin && $isUserAdmin,
                                    'table-success' => $isCurrentSuperAdmin && $isUserSuperAdmin,
                                ])>
                                <td>
                                    {{ $user->name }}
                                    @if($isUserAdmin)
                                        <span class="badge bg-info ms-2">Admin</span>
                                    @elseif($isUserSuperAdmin)
                                        <span class="badge bg-success ms-2">Superadmin</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Editar</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


    </div>
@endsection

@endrole
