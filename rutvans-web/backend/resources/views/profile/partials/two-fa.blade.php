<div class="container mt-5">
    <h2 class="mb-4">
        <i class="bi bi-shield-lock-fill me-2"></i> Autenticación en Dos Factores (2FA)
    </h2>

    {{-- Estado con SweetAlert --}}
    @php
        $mensaje = session('status');
        if ($mensaje === 'two-factor-authentication-disabled') {
            $mensaje = 'La autenticación en dos factores ha sido desactivada.';
        } elseif ($mensaje === 'two-factor-authentication-enabled') {
            $mensaje = 'La autenticación en dos factores ha sido activada.';
        } elseif ($mensaje === 'recovery-codes-regenerated') {
            $mensaje = 'Los códigos de recuperación han sido regenerados.';
        }
    @endphp

    @if (session('status'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: @json($mensaje),
                        confirmButtonText: 'Aceptar'
                    });
                });
            </script>
        @endpush
    @endif

    @if ($errors->any())
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        confirmButtonText: 'Cerrar'
                    });
                });
            </script>
        @endpush
    @endif

    {{-- 2FA no activado --}}
    @if (! auth()->user()->two_factor_secret)
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <p class="mb-3">La autenticación en dos factores no está activada.</p>
                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shield-lock-fill me-1"></i> Activar 2FA
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-success">
                    <i class="bi bi-shield-check me-2"></i> 2FA está activado
                </h5>

                {{-- Mostrar QR si no ha sido confirmado --}}
                @if (is_null(auth()->user()->two_factor_confirmed_at))
                    <hr>
                    <p><strong>Escanea este código QR con tu app de autenticación:</strong></p>
                    <div class="border p-3 d-inline-block bg-light rounded shadow-sm mb-3">
                        {!! auth()->user()->twoFactorQrCodeSvg() !!}
                    </div>

                    {{-- Confirmar código 2FA --}}
                    <form method="POST" action="{{ route('two-factor.confirm') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Ingresa un código 2FA para confirmar:</label>
                            <input type="text" name="code" id="code" class="form-control" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle me-1"></i> Confirmar 2FA
                        </button>
                    </form>
                @endif

                {{-- Códigos de recuperación (solo si está confirmado) --}}
                @if (!is_null(auth()->user()->two_factor_confirmed_at))
                    <hr>
                    <p><strong>Códigos de recuperación:</strong></p>
                    <ul class="list-group list-group-flush mb-3">
                        @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                            <li class="list-group-item">{{ $code }}</li>
                        @endforeach
                    </ul>

                    {{-- Botón para regenerar los códigos --}}
                    <form method="POST" action="{{ route('two-factor.recovery-codes') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i> Regenerar códigos
                        </button>
                    </form>
                @endif

                {{-- Desactivar 2FA --}}
                <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-shield-x me-1"></i> Desactivar 2FA
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

@section('scripts')
    <!-- SweetAlert2 CDN -->
     
    @stack('scripts')
@endsection
