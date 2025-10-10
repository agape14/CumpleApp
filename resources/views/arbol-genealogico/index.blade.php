@extends('layouts.app')

@section('title', 'Árbol Genealógico')

@section('styles')
<style>
    .family-tree-container {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        min-height: 600px;
        position: relative;
        overflow: auto;
    }

    .tree-node {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        margin: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-block;
        min-width: 200px;
        text-align: center;
    }

    .tree-node:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    .tree-node h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .tree-node p {
        margin: 0.25rem 0 0 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .relationship-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: inline-block;
    }

    #tree-canvas {
        width: 100%;
        height: 600px;
    }

    .tree-controls {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 10;
    }

    .tree-legend {
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem;
        border-radius: 10px;
        margin-top: 1rem;
    }

    .legend-item {
        display: inline-block;
        margin-right: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .legend-color {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 0.5rem;
        vertical-align: middle;
    }

    /* Estructura jerárquica del árbol genealógico */
    .family-tree-level {
        margin: 2rem 0;
        position: relative;
    }

    .generation-label {
        text-align: center;
        font-weight: bold;
        font-size: 1.1rem;
        color: #374151;
        margin-bottom: 1rem;
        padding: 0.5rem;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 10px;
        border: 2px solid #d1d5db;
    }

    /* Generación de padres */
    .parents-generation {
        margin-bottom: 2rem;
    }

    .parents-container {
        display: flex;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
    }

    /* Generación principal (pareja) */
    .main-generation {
        margin: 2rem 0;
    }

    .couple-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .couple-connector {
        font-size: 2rem;
        color: #ec4899;
    }

    /* Generación de hermanos */
    .siblings-generation {
        margin: 1rem 0;
    }

    .siblings-container {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Generación de hijos */
    .children-generation {
        margin-top: 2rem;
    }

    .children-container {
        display: flex;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .child-family-group {
        min-width: 300px;
        flex: 1;
    }

    /* Líneas conectoras */
    .vertical-line {
        width: 2px;
        height: 2rem;
        background: linear-gradient(to bottom, #667eea, #764ba2);
        margin: 0 auto;
        position: relative;
    }

    .vertical-line::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 10px solid #764ba2;
    }

    .vertical-line-to-children {
        width: 2px;
        height: 1rem;
        background: linear-gradient(to bottom, #667eea, #764ba2);
        margin: 0 auto;
    }

    /* Estilos específicos para cada tipo de nodo */
    .parent-node {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
        min-width: 200px;
    }

    .main-person {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        transform: scale(1.1);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    .spouse-node {
        background: linear-gradient(135deg, #ec4899 0%, #be185d 100%) !important;
        color: white !important;
        min-width: 200px;
    }

    .sibling-node {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        color: white !important;
        min-width: 180px;
    }

    .tree-node {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .tree-node:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .relationship-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: inline-block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .couple-container {
            flex-direction: column;
        }
        
        .parents-container, .children-container {
            flex-direction: column;
            align-items: center;
        }
        
        .couple-connector {
            transform: rotate(90deg);
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-diagram-3-fill"></i> Árbol Genealógico Familiar
                </h4>
                <div>
                    <button class="btn btn-light btn-sm" onclick="exportarArbol()">
                        <i class="bi bi-download"></i> Exportar
                    </button>
                    <button class="btn btn-light btn-sm" onclick="verVistaLista()">
                        <i class="bi bi-list"></i> Vista Lista
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Selector de familiar raíz -->
                <div class="mb-4">
                    <label for="familiarRaiz" class="form-label">
                        <i class="bi bi-person-circle"></i> Seleccionar familiar principal:
                    </label>
                    <select class="form-select" id="familiarRaiz" onchange="generarArbol()">
                        <option value="">-- Árbol completo --</option>
                        @foreach($familiares as $fam)
                            <option value="{{ $fam->id }}">{{ $fam->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Contenedor del árbol -->
                <div class="family-tree-container" id="treeContainer">
                    <div class="tree-controls">
                        <button class="btn btn-sm btn-primary" onclick="zoomIn()">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="zoomOut()">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="resetZoom()">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                    
                    <canvas id="tree-canvas"></canvas>
                    
                    <div id="treeContent" class="mt-3"></div>
                </div>

                <!-- Leyenda -->
                <div class="tree-legend">
                    <h6><i class="bi bi-info-circle"></i> Leyenda del Árbol Genealógico:</h6>
                    <div class="legend-item">
                        <span class="legend-color" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></span>
                        <span>👤 Familiar Principal</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);"></span>
                        <span>💕 Pareja (Esposo/Esposa)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"></span>
                        <span>👨‍👩‍👧‍👦 Padres</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);"></span>
                        <span>👫 Hermanos</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"></span>
                        <span>👶 Hijos</span>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <i class="bi bi-lightbulb"></i> 
                        <strong>Tip:</strong> Haz clic en cualquier familiar para ver sus detalles
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar relación -->
<div class="modal fade" id="agregarRelacionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-link-45deg"></i> Agregar Relación Familiar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarRelacion">
                    <input type="hidden" id="familiar_id" name="familiar_id">
                    
                    <div class="mb-3">
                        <label for="familiar_relacionado_id" class="form-label">Familiar:</label>
                        <select class="form-select" id="familiar_relacionado_id" name="familiar_relacionado_id" required>
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_relacion" class="form-label">Tipo de Relación:</label>
                        <select class="form-select" id="tipo_relacion" name="tipo_relacion" required>
                            <option value="">Seleccionar...</option>
                            <option value="hijo">Hijo</option>
                            <option value="hija">Hija</option>
                            <option value="esposo">Esposo</option>
                            <option value="esposa">Esposa</option>
                            <option value="pareja">Pareja</option>
                            <option value="padre">Padre</option>
                            <option value="madre">Madre</option>
                            <option value="hermano">Hermano</option>
                            <option value="hermana">Hermana</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción (opcional):</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRelacion()">
                    <i class="bi bi-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
<script>
let arbolData = null;
let zoom = 1;

// Cargar árbol al iniciar
document.addEventListener('DOMContentLoaded', function() {
    generarArbol();
    cargarFamiliaresParaRelaciones();
});

// Cargar familiares para el modal de relaciones
function cargarFamiliaresParaRelaciones() {
    fetch('/api/familiares')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('familiar_relacionado_id');
                select.innerHTML = '<option value="">Seleccionar...</option>';
                
                data.familiares.forEach(familiar => {
                    const option = document.createElement('option');
                    option.value = familiar.id;
                    option.textContent = familiar.nombre;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error al cargar familiares:', error);
        });
}

// Generar árbol
function generarArbol() {
    const familiarId = document.getElementById('familiarRaiz').value;
    const url = familiarId 
        ? `/arbol-genealogico/generar/${familiarId}` 
        : '/arbol-genealogico/completo';

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                arbolData = data;
                renderizarArbol(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorAlert('Error', 'Error al cargar el árbol genealógico');
        });
}

// Renderizar árbol
function renderizarArbol(data) {
    const container = document.getElementById('treeContent');
    
    if (data.arbol) {
        // Vista jerárquica de un familiar específico
        container.innerHTML = renderizarNodo(data.arbol, 0);
    } else if (data.nodos) {
        // Vista de red completa
        renderizarRedCompleta(data.nodos, data.enlaces);
    } else {
        // No hay datos
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-diagram-3" style="font-size: 4rem; color: #cbd5e1;"></i>
                <h5 class="mt-3 text-muted">No hay relaciones familiares registradas</h5>
                <p class="text-muted">Agrega relaciones familiares para ver el árbol genealógico</p>
                <a href="/familiares" class="btn btn-primary">
                    <i class="bi bi-people"></i> Ir a Familiares
                </a>
            </div>
        `;
    }
}

// Renderizar nodo individual con estructura jerárquica
function renderizarNodo(nodo, nivel) {
    if (!nodo || nodo.procesado) return '';
    
    let html = `
        <div class="family-tree-level level-${nivel}">
    `;

    // Nivel 0: Mostrar padres arriba
    if (nivel === 0 && nodo.padres && nodo.padres.length > 0) {
        html += `
            <div class="parents-generation">
                <div class="generation-label">👨‍👩‍👧‍👦 PADRES</div>
                <div class="parents-container">
        `;
        nodo.padres.forEach(padre => {
            html += `
                <div class="tree-node parent-node" onclick="verDetalleFamiliar(${padre.id})">
                    <h6>${padre.nombre}</h6>
                    <small><i class="bi bi-calendar"></i> ${padre.edad || 0} años</small>
                    <br><span class="relationship-badge">${padre.tipo_relacion}</span>
                </div>
            `;
        });
        html += `
                </div>
            </div>
            <div class="vertical-line"></div>
        `;
    }

    // Generación principal: Familiar + Pareja
    html += `
        <div class="main-generation">
            <div class="generation-label">👤 GENERACIÓN PRINCIPAL</div>
            <div class="couple-container">
                <!-- Familiar principal -->
                <div class="tree-node main-person" onclick="verDetalleFamiliar(${nodo.id})">
                    <h5>${nodo.nombre}</h5>
                    <p><i class="bi bi-calendar"></i> ${nodo.edad || 0} años</p>
                    ${nodo.parentesco ? `<span class="relationship-badge">${nodo.parentesco}</span>` : ''}
                </div>
    `;

    // Agregar pareja al lado
    if (nodo.pareja) {
        html += `
                <div class="couple-connector">💕</div>
                <div class="tree-node spouse-node" onclick="verDetalleFamiliar(${nodo.pareja.id})">
                    <h5>${nodo.pareja.nombre}</h5>
                    <p><i class="bi bi-calendar"></i> ${nodo.pareja.edad || 0} años</p>
                    <span class="relationship-badge">${nodo.pareja.tipo_relacion}</span>
                </div>
        `;
    }

    html += `
            </div>
        </div>
    `;

    // Agregar hermanos al mismo nivel
    if (nivel === 0 && nodo.hermanos && nodo.hermanos.length > 0) {
        html += `
            <div class="siblings-generation">
                <div class="generation-label">👫 HERMANOS</div>
                <div class="siblings-container">
        `;
        nodo.hermanos.forEach(hermano => {
            html += `
                <div class="tree-node sibling-node" onclick="verDetalleFamiliar(${hermano.id})">
                    <h6>${hermano.nombre}</h6>
                    <small><i class="bi bi-calendar"></i> ${hermano.edad || 0} años</small>
                    <br><span class="relationship-badge">${hermano.tipo_relacion}</span>
                </div>
            `;
        });
        html += `
                </div>
            </div>
        `;
    }

    // Línea vertical hacia los hijos
    if (nodo.hijos && nodo.hijos.length > 0) {
        html += `<div class="vertical-line-to-children"></div>`;
    }

    // Agregar hijos (generación siguiente)
    if (nodo.hijos && nodo.hijos.length > 0) {
        html += `
            <div class="children-generation">
                <div class="generation-label">👶 HIJOS</div>
                <div class="children-container">
        `;
        
        nodo.hijos.forEach(hijo => {
            html += `
                <div class="child-family-group">
                    ${renderizarNodo(hijo, nivel + 1)}
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }

    html += '</div>';
    return html;
}

// Renderizar red completa con D3.js
function renderizarRedCompleta(nodos, enlaces) {
    // Implementación básica - se puede mejorar con D3.js para visualización de grafos
    const container = document.getElementById('treeContent');
    let html = '<div class="row">';
    
    nodos.forEach(nodo => {
        html += `
            <div class="col-md-4 mb-3">
                <div class="tree-node" onclick="verDetalleFamiliar(${nodo.id})">
                    <h5>${nodo.nombre}</h5>
                    <p><i class="bi bi-calendar"></i> ${nodo.edad || 0} años</p>
                    ${nodo.parentesco ? `<p class="relationship-badge">${nodo.parentesco}</p>` : ''}
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Ver detalle de familiar
function verDetalleFamiliar(familiarId) {
    window.location.href = `/familiares/${familiarId}`;
}

// Vista lista
function verVistaLista() {
    window.location.href = '/familiares';
}

// Controles de zoom
function zoomIn() {
    zoom *= 1.2;
    aplicarZoom();
}

function zoomOut() {
    zoom *= 0.8;
    aplicarZoom();
}

function resetZoom() {
    zoom = 1;
    aplicarZoom();
}

function aplicarZoom() {
    document.getElementById('treeContent').style.transform = `scale(${zoom})`;
    document.getElementById('treeContent').style.transformOrigin = 'top left';
}

// Exportar árbol
function exportarArbol() {
    showInfoAlert('Próximamente', 'La función de exportar árbol genealógico estará disponible pronto');
}

// Guardar relación
function guardarRelacion() {
    const form = document.getElementById('formAgregarRelacion');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Obtener el familiar_id del selector de familiar raíz
    const familiarRaiz = document.getElementById('familiarRaiz').value;
    if (familiarRaiz) {
        data.familiar_id = familiarRaiz;
    } else {
        showWarningAlert('Selección requerida', 'Por favor selecciona un familiar principal primero');
        return;
    }

    fetch('/relaciones-familiares', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessAlert('¡Éxito!', data.message);
            bootstrap.Modal.getInstance(document.getElementById('agregarRelacionModal')).hide();
            generarArbol();
            form.reset();
        } else {
            showErrorAlert('Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert('Error', 'Error al guardar la relación');
    });
}

// Ver detalle del familiar
function verDetalleFamiliar(familiarId) {
    window.open(`/familiares/${familiarId}`, '_blank');
}
</script>
@endsection

