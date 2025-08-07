// Funciones globales para el sistema Agnes

// Inicializar DataTable
function initDataTable(tableId) {
    if ($.fn.DataTable && !$.fn.DataTable.isDataTable(`#${tableId}`)) {
        $(`#${tableId}`).DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-ES.json'
            },
            responsive: true,
            pageLength: 10,
            order: [[0, 'asc']]
        });
    }
}

// Inicializar Quill
function initQuill(editorId) {
    const editorElement = document.getElementById(editorId);
    if (editorElement && !document.querySelector(`#${editorId} .ql-editor`)) {
        return new Quill(`#${editorId}`, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            },
            placeholder: 'Escribe aquí...'
        });
    }
    return null;
}

// Configurar formularios de eliminación
function initDeleteForms() {
    const formsEliminar = document.querySelectorAll('.form-eliminar');
    formsEliminar.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
}

// Mostrar toast
function showToast(message, type = 'info') {
    const colors = {
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f59e42',
        info: '#3b82f6'
    };
    
    Toastify({
        text: message,
        duration: 3500,
        gravity: "top",
        position: "right",
        backgroundColor: colors[type] || colors.info,
        stopOnFocus: true,
        close: true
    }).showToast();
}

// Cerrar modal
function closeModal(modalName) {
    const modal = document.querySelector(`[data-modal="${modalName}"]`);
    if (modal) {
        modal.close();
    }
}

// Filtrar materias por grupo
function filtrarMaterias(grupoId) {
    if (grupoId) {
        fetch(`/api/materias/${grupoId}`)
            .then(response => response.json())
            .then(data => {
                const materiaSelect = document.getElementById('materia');
                const materiaEditSelect = document.getElementById('edit_materia_id');
                
                if (materiaSelect) {
                    materiaSelect.innerHTML = '<option value="">Selecciona una materia</option>';
                    data.forEach(materia => {
                        materiaSelect.innerHTML += `<option value="${materia.id}">${materia.nombre}</option>`;
                    });
                }
                
                if (materiaEditSelect) {
                    materiaEditSelect.innerHTML = '<option value="">Selecciona una materia</option>';
                    data.forEach(materia => {
                        materiaEditSelect.innerHTML += `<option value="${materia.id}">${materia.nombre}</option>`;
                    });
                }
            });
    }
}

// Función principal para inicializar componentes
function iniciarComponentes() {
    // Inicializar DataTables
    initDataTable('myTable');
    
    // Inicializar formularios de eliminación
    initDeleteForms();
    
    // Mostrar toast si existe mensaje de sesión
    @if(session('toast'))
        showToast("{{ session('toast.message') }}", "{{ session('toast.type') }}");
    @endif
}

// Exportar funciones al objeto window
window.initDataTable = initDataTable;
window.initQuill = initQuill;
window.initDeleteForms = initDeleteForms;
window.showToast = showToast;
window.closeModal = closeModal;
window.filtrarMaterias = filtrarMaterias;
window.iniciarComponentes = iniciarComponentes;
