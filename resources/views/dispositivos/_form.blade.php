<div class="mb-3">
    <label for="nombre" class="form-label">Nombre</label>

    <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control"
        value="{{ old('nombre', $dispositivo->nombre ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label for="ubicacion" class="form-label">Ubicación</label>

    <input
        type="text"
        id="ubicacion"
        name="ubicacion"
        class="form-control"
        value="{{ old('ubicacion', $dispositivo->ubicacion ?? '') }}"
        required
    >
</div>
