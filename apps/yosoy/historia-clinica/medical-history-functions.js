        // FUNCIONES DE HISTORIAL MÉDICO MEJORADAS
        
        // Función para ver historial completo con resumen médico
        function viewHistoryList() {
            if (!selectedPatient) {
                showNotification('Error', 'Seleccione una paciente primero', 'error');
                return;
            }

            const historyDetails = document.getElementById('history-details');
            historyDetails.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i><p>Cargando historial completo...</p></div>';
            
            // Obtener todos los registros médicos de la paciente
            Promise.all([
                fetch(`/api/historias/paciente/${selectedPatient.id}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                }).then(r => r.ok ? r.json() : []),
                fetch(`/api/formulas/paciente/${selectedPatient.id}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                }).then(r => r.ok ? r.json() : []),
                fetch(`/api/citas/paciente/${selectedPatient.id}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                }).then(r => r.ok ? r.json() : [])
            ])
            .then(([historias, formulas, citas]) => {
                console.log('Datos cargados:', { historias, formulas, citas });
                
                if (historias.length === 0 && formulas.length === 0 && citas.length === 0) {
                    historyDetails.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600">No hay registros médicos para esta paciente</p>
                            <button onclick="showTab('nueva-historia')" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Crear Primera Historia
                            </button>
                        </div>
                    `;
                    return;
                }
                
                // Mostrar historial completo con resumen
                displayCompleteMedicalRecord(selectedPatient, {
                    historias: historias || [],
                    formulas: formulas || [],
                    citas: citas || []
                });
            })
            .catch(error => {
                console.error('Error:', error);
                historyDetails.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                        <p class="text-red-600">Error al cargar el historial médico</p>
                        <button onclick="viewHistoryList()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-redo mr-2"></i>Reintentar
                        </button>
                    </div>
                `;
            });
        }

        // Función para mostrar el registro médico completo
        function displayCompleteMedicalRecord(paciente, data) {
            const historyDetails = document.getElementById('history-details');
            
            // Generar alertas médicas
            const alerts = generateMedicalAlerts(paciente, data);
            
            // Generar cronología
            const timeline = generateMedicalTimeline(data);
            
            historyDetails.innerHTML = `
                <!-- Resumen Médico -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-6 mb-6 border border-blue-200">
                    <h3 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2"></i>Resumen Médico
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-blue-600">${data.historias.length}</p>
                                    <p class="text-sm text-gray-600">Consultas Médicas</p>
                                </div>
                                <i class="fas fa-stethoscope text-blue-400 text-2xl"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-green-600">${data.formulas.length}</p>
                                    <p class="text-sm text-gray-600">Fórmulas Médicas</p>
                                </div>
                                <i class="fas fa-pills text-green-400 text-2xl"></i>
                            </div>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-purple-600">${data.citas.length}</p>
                                    <p class="text-sm text-gray-600">Citas Registradas</p>
                                </div>
                                <i class="fas fa-calendar text-purple-400 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alertas Médicas -->
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>Alertas y Prioridades
                        </h4>
                        <div class="text-sm">
                            ${alerts}
                        </div>
                    </div>
                </div>

                <!-- Cronología Médica -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history mr-2"></i>Cronología Médica Completa
                        </h3>
                        <button onclick="showTab('nueva-historia')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Nueva Consulta
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        ${timeline}
                    </div>
                </div>
            `;
        }

        // Generar alertas médicas inteligentes
        function generateMedicalAlerts(paciente, data) {
            const alerts = [];
            const today = new Date();
            
            // Alert de seguimiento ginecológico
            if (paciente.ultimaMenstruacion) {
                const lastPeriod = new Date(paciente.ultimaMenstruacion);
                const daysSince = Math.floor((today - lastPeriod) / (1000 * 60 * 60 * 24));
                if (daysSince > 35) {
                    alerts.push('<span class="text-orange-600"><i class="fas fa-calendar mr-1"></i>Seguimiento menstrual requerido</span>');
                }
            } else {
                alerts.push('<span class="text-blue-600"><i class="fas fa-info-circle mr-1"></i>Registrar fecha de última menstruación</span>');
            }
            
            // Alert de citas pendientes
            const upcomingAppointments = data.citas.filter(cita => new Date(cita.fecha) > today);
            if (upcomingAppointments.length > 0) {
                alerts.push(`<span class="text-green-600"><i class="fas fa-calendar-check mr-1"></i>${upcomingAppointments.length} cita(s) pendiente(s)</span>`);
            }
            
            // Alert de medicamentos activos
            const recentFormulas = data.formulas.filter(formula => {
                const formulaDate = new Date(formula.created_at);
                const daysSince = Math.floor((today - formulaDate) / (1000 * 60 * 60 * 24));
                return daysSince <= 30;
            });
            if (recentFormulas.length > 0) {
                alerts.push(`<span class="text-purple-600"><i class="fas fa-pills mr-1"></i>Tratamiento activo (${recentFormulas.length} fórmulas recientes)</span>`);
            }
            
            // Alert de seguimiento
            if (data.historias.length > 0) {
                const lastConsult = new Date(data.historias[0].fecha_consulta || data.historias[0].created_at);
                const daysSince = Math.floor((today - lastConsult) / (1000 * 60 * 60 * 24));
                if (daysSince > 90) {
                    alerts.push('<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>Requiere seguimiento urgente (&gt;90 días)</span>');
                } else if (daysSince > 30) {
                    alerts.push('<span class="text-yellow-600"><i class="fas fa-clock mr-1"></i>Considerar seguimiento</span>');
                }
            }
            
            // Alertas específicas de salud femenina
            if (paciente.embarazosPrevios > 0 && !paciente.ultimaMenstruacion) {
                alerts.push('<span class="text-pink-600"><i class="fas fa-baby mr-1"></i>Historial de embarazos - verificar estado actual</span>');
            }
            
            return alerts.length > 0 ? alerts.join('<br>') : '<span class="text-gray-500">Sin alertas activas</span>';
        }

        // Generar cronología médica completa
        function generateMedicalTimeline(data) {
            const allEvents = [];
            
            // Agregar historias clínicas
            data.historias.forEach(historia => {
                allEvents.push({
                    date: new Date(historia.fecha_consulta || historia.created_at),
                    type: 'consulta',
                    title: 'Consulta Médica',
                    content: historia,
                    icon: 'fas fa-stethoscope',
                    color: 'blue'
                });
            });
            
            // Agregar fórmulas médicas
            data.formulas.forEach(formula => {
                allEvents.push({
                    date: new Date(formula.created_at),
                    type: 'formula',
                    title: 'Fórmula Médica',
                    content: formula,
                    icon: 'fas fa-pills',
                    color: 'green'
                });
            });
            
            // Agregar citas
            data.citas.forEach(cita => {
                const citaDate = new Date(cita.fecha);
                allEvents.push({
                    date: citaDate,
                    type: 'cita',
                    title: citaDate < new Date() ? 'Cita Completada' : 'Cita Programada',
                    content: cita,
                    icon: 'fas fa-calendar-check',
                    color: 'purple'
                });
            });
            
            // Ordenar por fecha (más reciente primero)
            allEvents.sort((a, b) => b.date - a.date);
            
            if (allEvents.length === 0) {
                return '<div class="text-center py-8 text-gray-500">No hay registros médicos disponibles</div>';
            }
            
            return allEvents.map(event => `
                <div class="flex gap-4 p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-${event.color}-100 rounded-full flex items-center justify-center">
                            <i class="${event.icon} text-${event.color}-600"></i>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-gray-800">${event.title}</h4>
                            <div class="text-right">
                                <span class="text-sm text-gray-500">${event.date.toLocaleDateString('es-ES')}</span>
                                ${event.type === 'cita' && event.content.hora ? `<br><span class="text-xs text-gray-400">${event.content.hora}</span>` : ''}
                            </div>
                        </div>
                        ${formatEventContent(event)}
                        ${event.type === 'consulta' ? `
                            <div class="mt-3 flex gap-2">
                                <button onclick="viewHistoryDetail(${event.content.id})" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                    Ver Completo
                                </button>
                                <button onclick="printHistory(${event.content.id})" class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    <i class="fas fa-print mr-1"></i>PDF
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        }

        // Formatear contenido del evento según su tipo
        function formatEventContent(event) {
            switch(event.type) {
                case 'consulta':
                    return `
                        <div class="text-sm text-gray-700 space-y-1">
                            <p><strong>Motivo:</strong> ${event.content.motivo_consulta || 'No especificado'}</p>
                            <p><strong>Diagnóstico:</strong> ${event.content.diagnostico || 'No especificado'}</p>
                            ${event.content.plan_tratamiento ? `<p><strong>Tratamiento:</strong> ${event.content.plan_tratamiento.substring(0, 100)}${event.content.plan_tratamiento.length > 100 ? '...' : ''}</p>` : ''}
                        </div>
                    `;
                case 'formula':
                    return `
                        <div class="text-sm text-gray-700 space-y-1">
                            <p><strong>Medicamentos:</strong> ${event.content.medicamentos.substring(0, 80)}${event.content.medicamentos.length > 80 ? '...' : ''}</p>
                            ${event.content.indicaciones ? `<p><strong>Indicaciones:</strong> ${event.content.indicaciones.substring(0, 80)}${event.content.indicaciones.length > 80 ? '...' : ''}</p>` : ''}
                        </div>
                    `;
                case 'cita':
                    return `
                        <div class="text-sm text-gray-700 space-y-1">
                            <p><strong>Tipo:</strong> ${event.content.tipo || 'Presencial'}</p>
                            ${event.content.motivo ? `<p><strong>Motivo:</strong> ${event.content.motivo}</p>` : ''}
                            ${event.content.tipo === 'videoconsulta' && event.content.meetLink ? `<p><a href="${event.content.meetLink}" target="_blank" class="text-blue-600 hover:underline"><i class="fas fa-video mr-1"></i>Unirse a videoconsulta</a></p>` : ''}
                        </div>
                    `;
                default:
                    return '';
            }
        }

        // Función auxiliar para calcular edad
        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            return age;
        }