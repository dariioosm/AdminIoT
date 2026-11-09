<div class="mb-3">
    <label for="Nombre" class="form-label">Nombre</label>
    <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control"
        value="{{ old('nombre',$sensor->nombre??'') }}"
        required
        >
</div>
<div class="mb-3">
    <label for="tipo" class="form-label">Tipo</label>
    <input
        type="text"
        id="tipo"
        name="tipo"
        class="form-control"
        value="{{ old('tipo',$sensor->tipo??'') }}"
        required
        >
</div><div class="mb-3">
    <label for="tipo_dato" class="form-label">Tipo de dato</label>

    <select id="tipo_dato" name="tipo_dato" class="form-select">
        <option value="numerico"
            @selected(old('tipo_dato', $sensor->tipo_dato ?? 'numerico') === 'numerico')>
            Numérico
        </option>

        <option value="logico"
            @selected(old('tipo_dato', $sensor->tipo_dato ?? 'numerico') === 'logico')>
            Lógico
        </option>
    </select>
</div>

<div id="umbrales">
    <div class="mb-3">
        <label for="umbral_minimo" class="form-label">Umbral mínimo</label>
        <input
            type="number"
            step="0.0001"
            id="umbral_minimo"
            name="umbral_minimo"
            class="form-control"
            value="{{ old('umbral_minimo', $sensor->umbral_minimo ?? '') }}"
        >
    </div>

    <div class="mb-3">
        <label for="umbral_maximo" class="form-label">Umbral máximo</label>
        <input
            type="number"
            step="0.0001"
            id="umbral_maximo"
            name="umbral_maximo"
            class="form-control"
            value="{{ old('umbral_maximo', $sensor->umbral_maximo ?? '') }}"
        >
    </div>
</div>

<script>
    function actualizarUmbrales() {
        const esLogico = document.getElementById('tipo_dato').value === 'logico';
        const umbrales = document.getElementById('umbrales');

        umbrales.hidden = esLogico;

        umbrales.querySelectorAll('input').forEach(input => {
            input.disabled = esLogico;
        });
    }

    document.getElementById('tipo_dato')
        .addEventListener('change', actualizarUmbrales);

    actualizarUmbrales();
</script>
