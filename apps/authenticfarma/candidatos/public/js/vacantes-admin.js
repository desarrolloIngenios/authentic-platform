// document.addEventListener('DOMContentLoaded', () => {
    
    let filtros = {
        cargo: '',
        ciudad: '',
        sector: '',
        area: '',
        nivel_educacion: '',
        salario: ''
    };

    let paginaActual = 1;
    const vacantesPorPagina = 10; // Reducido para mejor experiencia
    let totalPaginas = 1;

    // Referencias a elementos del DOM
    const filtrosActivos = document.getElementById('filtros-activos');
    const vacantesList = document.querySelector('.vacantes-list');
    const btnCargarMas = document.getElementById('btn-cargar-mas');

    // Función para normalizar texto (eliminar tildes y convertir a minúsculas)
    function normalizeText(text) {
        return text.toLowerCase()
            .trim()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, ''); // Elimina diacríticos
    }

    // Función para actualizar los chips de filtros activos
    function actualizarChips() {
        filtrosActivos.innerHTML = '';
        Object.entries(filtros).forEach(([key, value]) => {
            if (value) {
                const chip = document.createElement('div');
                chip.className = 'chip';
                chip.innerHTML = `${obtenerTextoFiltro(key, value)} <span onclick="eliminarFiltro('${key}')">&times;</span>`;
                filtrosActivos.appendChild(chip);
            }
        });
    }

    // Función para obtener el texto descriptivo del filtro
    function obtenerTextoFiltro(key, value) {
        // Caso especial para el campo de búsqueda por cargo/título
        if (key === 'cargo') {
            return `Búsqueda: ${value}`;
        }
        
        const select = document.getElementById(`filtro-${key === 'nivel_educacion' ? 'nivel' : key}`);
        if (!select) {
            return `${key}: ${value}`;
        }
        
        const option = select.querySelector(`option[value="${value}"]`);
        if (!option) {
            const firstOption = select.options[0];
            return firstOption ? `${firstOption.text.split(' ')[0]}: ${value}` : `${key}: ${value}`;
        }
        
        const prefix = key === 'nivel_educacion' ? 'Educación' : select.options[0].text.split(' ')[0];
        return `${prefix}: ${option.text}`;
    }

    // Función para eliminar un filtro
    function eliminarFiltro(key) {
        const elemento = document.getElementById(`filtro-${key}`);
        if (elemento) {
            elemento.value = '';
        }
        filtros[key] = '';
        actualizarChips();
        aplicarFiltros();
    }

    // Función para aplicar los filtros
    function aplicarFiltros() {
        const vacantes = document.querySelectorAll('.vacante-card');
        let vacantesFiltradas = Array.from(vacantes);
        
        // Resetear paginación
        paginaActual = 1;
        
        
        // Aplicar filtros en orden
        if (filtros.cargo) {
            const searchTerm = normalizeText(filtros.cargo);
            vacantesFiltradas = vacantesFiltradas.filter(vacante => {
                const cargo = normalizeText(vacante.dataset.cargo);
                const titulo = normalizeText(vacante.dataset.titulo);
                return cargo.includes(searchTerm) || titulo.includes(searchTerm);
            });
        }

        if (filtros.ciudad) {
            vacantesFiltradas = vacantesFiltradas.filter(vacante => 
                vacante.dataset.ciudad === filtros.ciudad
            );
        }

        if (filtros.sector) {
            vacantesFiltradas = vacantesFiltradas.filter(vacante => {
                const sectores = vacante.dataset.sectores.split(',');
                return sectores.includes(filtros.sector);
            });
        }

        if (filtros.area) {
            vacantesFiltradas = vacantesFiltradas.filter(vacante => {
                const areas = vacante.dataset.areas.split(',');
                return areas.includes(filtros.area);
            });
        }

        if (filtros.nivel_educacion) {
            vacantesFiltradas = vacantesFiltradas.filter(vacante => 
                vacante.dataset.nivelId && String(vacante.dataset.nivelId) === String(filtros.nivel_educacion)
            );
        }

        if (filtros.salario) {
            vacantesFiltradas = vacantesFiltradas.filter(vacante => 
                vacante.dataset.salarioId && String(vacante.dataset.salarioId) === String(filtros.salario)
            );
        }

        // Ocultar todas las vacantes primero
        vacantes.forEach(vacante => {
            vacante.style.display = 'none';
        });

        // Mostrar las vacantes filtradas según la paginación
        mostrarVacantes(vacantesFiltradas);

        // Actualizar contador de resultados
        const hayFiltrosActivos = Object.values(filtros).some(valor => valor !== '');
        document.querySelector('.text-muted i').textContent = hayFiltrosActivos 
            ? `${vacantesFiltradas.length} vacantes encontradas` 
            : 'Encuentra tu próxima oportunidad laboral';
        // Actualizar la paginación
        actualizarPaginacion(vacantesFiltradas);
    }

    // Función para actualizar la paginación
    function actualizarPaginacion(vacantesFiltradas) {
        const pageNumbers = document.querySelector('.page-numbers');
        const prevPage = document.getElementById('prev-page');
        const nextPage = document.getElementById('next-page');
        
        totalPaginas = Math.ceil(vacantesFiltradas.length / vacantesPorPagina);
        
        // Actualizar estado de los botones anterior/siguiente
        prevPage.classList.toggle('disabled', paginaActual === 1);
        nextPage.classList.toggle('disabled', paginaActual === totalPaginas);
        
        // Generar números de página
        pageNumbers.innerHTML = '';
        
        let startPage = Math.max(1, paginaActual - 2);
        let endPage = Math.min(totalPaginas, startPage + 4);
        
        // Ajustar el rango si estamos cerca del final
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }
        
        // Agregar primera página si no está en el rango
        if (startPage > 1) {
            pageNumbers.innerHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
                ${startPage > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
            `;
        }
        
        // Agregar páginas del rango
        for (let i = startPage; i <= endPage; i++) {
            pageNumbers.innerHTML += `
                <li class="page-item">
                    <a class="page-link ${i === paginaActual ? 'active' : ''}" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Agregar última página si no está en el rango
        if (endPage < totalPaginas) {
            pageNumbers.innerHTML += `
                ${endPage < totalPaginas - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPaginas}">${totalPaginas}</a>
                </li>
            `;
        }
    }

    // Función para mostrar las vacantes según la paginación
    function mostrarVacantes(vacantesFiltradas) {
        
        
        const inicio = (paginaActual - 1) * vacantesPorPagina;
        const fin = inicio + vacantesPorPagina;
        
        vacantesFiltradas.forEach((vacante, index) => {
            vacante.style.display = (index >= inicio && index < fin) ? 'flex' : 'none';
        });
        
        actualizarPaginacion(vacantesFiltradas);
    }

    // Event listeners para la paginación
    document.querySelector('.pagination').addEventListener('click', (e) => {
        e.preventDefault();
        const target = e.target.closest('a');
        
        if (!target || !target.dataset.page) return;
        if (target.parentElement.classList.contains('disabled')) return;
        
        const newPage = parseInt(target.dataset.page);
        if (newPage === paginaActual) return;
        
        paginaActual = newPage;
        const vacantesFiltradas = Array.from(document.querySelectorAll('.vacante-card')).filter(vacante => {
            let visible = true;
            
            if (filtros.cargo) {
                const searchTerm = normalizeText(filtros.cargo);
                const cargo = normalizeText(vacante.dataset.cargo);
                const titulo = normalizeText(vacante.dataset.titulo);
                if (!cargo.includes(searchTerm) && !titulo.includes(searchTerm)) visible = false;
            }

            if (filtros.ciudad && visible) {
                visible = vacante.dataset.ciudad === filtros.ciudad;
            }

            if (filtros.sector && visible) {
                const sectores = vacante.dataset.sectores.split(',');
                visible = sectores.includes(filtros.sector);
            }

            if (filtros.area && visible) {
                const areas = vacante.dataset.areas.split(',');
                visible = areas.includes(filtros.area);
            }

            if (filtros.nivel_educacion && visible) {
                if (!vacante.dataset.nivelId) {
                    visible = false;
                } else {
                    visible = String(vacante.dataset.nivelId) === String(filtros.nivel_educacion);
                }
            }

            if (filtros.salario && visible) {
                if (!vacante.dataset.salarioId) {
                    visible = false;
                } else {
                    visible = String(vacante.dataset.salarioId) === String(filtros.salario);
                }
            }

            return visible;    });

        mostrarVacantes(vacantesFiltradas);
    });

    document.getElementById('prev-page').addEventListener('click', (e) => {
        e.preventDefault();
        if (paginaActual > 1) {
            paginaActual--;
            aplicarFiltros();
        }
    });

    document.getElementById('next-page').addEventListener('click', (e) => {
        e.preventDefault();
        if (paginaActual < totalPaginas) {
            paginaActual++;
            aplicarFiltros();
        }
    });

    // Event listeners para los filtros (solo frontend)
    document.getElementById('filtro-cargo').addEventListener('input', (e) => {
        filtros.cargo = e.target.value;
        actualizarChips();
        aplicarFiltros();
    });

    ['ciudad', 'sector', 'area', 'nivel', 'salario'].forEach(filtro => {
        document.getElementById(`filtro-${filtro}`).addEventListener('change', (e) => {
            const key = filtro === 'nivel' ? 'nivel_educacion' : filtro;
            filtros[key] = e.target.value;
            actualizarChips();
            aplicarFiltros();
        });
    });

    // Inicialización
    actualizarChips();
    aplicarFiltros();

// });