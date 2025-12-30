<!-- Modal para Crear Coordinador -->
<div class="modal fade" id="createCoordinatorModal" tabindex="-1" aria-labelledby="createCoordinatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('coordinates.store') }}" method="POST" id="createCoordinatorForm" enctype="multipart/form-data">
                @csrf

                <!-- Encabezado del Modal -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createCoordinatorModalLabel">
                        <i class="fas fa-user-plus me-2"></i>
                        Nuevo Coordinador
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Errores de validación:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="required">Nombre Completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required placeholder="Ej: Juan Pérez">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="required">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required placeholder="Ej: juan@correo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="required">Contraseña</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required placeholder="Mínimo 8 caracteres">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="required">Confirmar Contraseña</label>
                                <input type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation" name="password_confirmation" required placeholder="Repite la contraseña">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="address" class="required">Dirección</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ old('address') }}" required placeholder="Dirección del coordinador">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="phone_number" class="required">Teléfono</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                    id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required
                                    placeholder="Ej: 1234567890">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_path">Foto del Coordinador</label>
                                <input type="file" class="form-control @error('photo_path') is-invalid @enderror"
                                    id="photo_path" name="photo_path" accept="image/*">
                                @error('photo_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <img id="photoPreview" src="#" alt="Vista previa" class="img-fluid mt-2"
                                    style="max-height: 150px; display:none; border-radius: 6px;">
                            </div>

                            <input type="hidden" name="site_id" id="site_id" value="{{ $site->id }}">

                        </div>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cancelar">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" aria-label="Crear Coordinador">
                        <i class="fas fa-save me-1"></i>
                        Crear Coordinador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
