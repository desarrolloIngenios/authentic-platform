const { Notificacion, Usuario, CitaMedica } = require('../models');
const { Op } = require('sequelize');

class NotificationService {
  constructor() {
    this.clients = new Map(); // Almacenar conexiones WebSocket/SSE
  }

  // Registrar cliente para notificaciones en tiempo real
  registerClient(userId, connection) {
    if (!this.clients.has(userId)) {
      this.clients.set(userId, new Set());
    }
    this.clients.get(userId).add(connection);
    
    connection.on('close', () => {
      this.clients.get(userId)?.delete(connection);
      if (this.clients.get(userId)?.size === 0) {
        this.clients.delete(userId);
      }
    });
  }

  // Enviar notificación en tiempo real
  async sendRealTimeNotification(userId, notification) {
    const userConnections = this.clients.get(userId);
    if (userConnections) {
      const message = JSON.stringify({
        type: 'notification',
        data: notification
      });
      
      userConnections.forEach(connection => {
        if (connection.readyState === 1) { // WebSocket.OPEN
          connection.send(message);
        }
      });
    }
  }

  // Crear y enviar notificación
  async createNotification(data) {
    try {
      const notificacion = await Notificacion.create({
        usuarioId: data.usuarioId,
        titulo: data.titulo,
        mensaje: data.mensaje,
        tipo: data.tipo || 'sistema',
        prioridad: data.prioridad || 'media',
        fechaProgramada: data.fechaProgramada ? new Date(data.fechaProgramada) : null,
        recursoId: data.recursoId,
        tipoRecurso: data.tipoRecurso,
        metadatos: data.metadatos,
        esRecurrente: data.esRecurrente || false,
        configuracionRecurrencia: data.configuracionRecurrencia,
        canalEnvio: data.canalEnvio || { push: true, email: false, sms: false, inApp: true },
        fechaEnviada: data.fechaProgramada ? null : new Date()
      });

      const notificacionCompleta = await Notificacion.findByPk(notificacion.id, {
        include: [{
          model: Usuario,
          as: 'usuario',
          attributes: ['nombre', 'email']
        }]
      });

      // Enviar en tiempo real si no está programada
      if (!data.fechaProgramada) {
        await this.sendRealTimeNotification(data.usuarioId, notificacionCompleta);
      }

      return notificacionCompleta;
    } catch (error) {
      console.error('Error creando notificación:', error);
      throw error;
    }
  }

  // Programar recordatorios automáticos para citas
  async scheduleAppointmentReminders() {
    try {
      const fechaInicio = new Date();
      const fechaFin = new Date();
      fechaFin.setDate(fechaFin.getDate() + 7);

      const citas = await CitaMedica.findAll({
        where: {
          fecha: {
            [Op.between]: [fechaInicio, fechaFin]
          },
          estado: 'programada'
        },
        include: [{
          model: require('../models').Paciente,
          as: 'pacienteInfo'
        }]
      });

      const recordatorios = [];

      for (const cita of citas) {
        // Verificar si ya existen recordatorios para esta cita
        const existingReminders = await Notificacion.findAll({
          where: {
            recursoId: cita.id,
            tipoRecurso: 'cita',
            tipo: 'cita'
          }
        });

        if (existingReminders.length === 0) {
          const fechaCita = new Date(cita.fecha + ' ' + cita.hora);

          // Recordatorio 24 horas antes
          const fecha24h = new Date(fechaCita);
          fecha24h.setHours(fecha24h.getHours() - 24);

          // Recordatorio 1 hora antes
          const fecha1h = new Date(fechaCita);
          fecha1h.setHours(fecha1h.getHours() - 1);

          if (fecha24h > new Date()) {
            const reminder24h = await this.createNotification({
              usuarioId: cita.medicoId,
              titulo: 'Recordatorio de Cita - 24 horas',
              mensaje: `Tiene una cita programada mañana a las ${cita.hora} con ${cita.paciente}`,
              tipo: 'cita',
              prioridad: 'media',
              fechaProgramada: fecha24h,
              recursoId: cita.id,
              tipoRecurso: 'cita',
              metadatos: {
                citaId: cita.id,
                paciente: cita.paciente,
                fecha: cita.fecha,
                hora: cita.hora,
                tipoRecordatorio: '24h'
              }
            });
            recordatorios.push(reminder24h);
          }

          if (fecha1h > new Date()) {
            const reminder1h = await this.createNotification({
              usuarioId: cita.medicoId,
              titulo: 'Recordatorio de Cita - 1 hora',
              mensaje: `Su cita con ${cita.paciente} es en 1 hora (${cita.hora})`,
              tipo: 'cita',
              prioridad: 'alta',
              fechaProgramada: fecha1h,
              recursoId: cita.id,
              tipoRecurso: 'cita',
              metadatos: {
                citaId: cita.id,
                paciente: cita.paciente,
                fecha: cita.fecha,
                hora: cita.hora,
                meetLink: cita.meetLink,
                tipoRecordatorio: '1h'
              }
            });
            recordatorios.push(reminder1h);
          }
        }
      }

      return recordatorios;
    } catch (error) {
      console.error('Error programando recordatorios automáticos:', error);
      throw error;
    }
  }

  // Procesar notificaciones programadas
  async processScheduledNotifications() {
    try {
      const notificacionesPendientes = await Notificacion.findAll({
        where: {
          estado: 'pendiente',
          fechaProgramada: {
            [Op.lte]: new Date()
          },
          fechaEnviada: null
        },
        include: [{
          model: Usuario,
          as: 'usuario'
        }]
      });

      for (const notificacion of notificacionesPendientes) {
        // Marcar como enviada
        await notificacion.update({ fechaEnviada: new Date() });
        
        // Enviar en tiempo real
        await this.sendRealTimeNotification(notificacion.usuarioId, notificacion);
        
        console.log(`Notificación enviada: ${notificacion.titulo} -> ${notificacion.usuario.nombre}`);
      }

      return notificacionesPendientes.length;
    } catch (error) {
      console.error('Error procesando notificaciones programadas:', error);
      throw error;
    }
  }

  // Crear notificación de urgencia médica
  async createUrgentMedicalAlert(usuarioId, titulo, mensaje, recursoId = null, tipoRecurso = null) {
    return await this.createNotification({
      usuarioId,
      titulo,
      mensaje,
      tipo: 'urgente',
      prioridad: 'critica',
      recursoId,
      tipoRecurso,
      canalEnvio: { push: true, email: true, sms: true, inApp: true }
    });
  }

  // Crear notificación de resultado de examen
  async createExamResultNotification(usuarioId, paciente, tipoExamen, resultado) {
    return await this.createNotification({
      usuarioId,
      titulo: 'Resultado de Examen Disponible',
      mensaje: `Resultados de ${tipoExamen} para ${paciente}: ${resultado}`,
      tipo: 'examen',
      prioridad: 'alta',
      metadatos: {
        paciente,
        tipoExamen,
        resultado,
        fechaResultado: new Date()
      }
    });
  }

  // Crear notificación de medicamento
  async createMedicationReminder(usuarioId, medicamento, dosis, proximaToma) {
    return await this.createNotification({
      usuarioId,
      titulo: 'Recordatorio de Medicamento',
      mensaje: `Es hora de tomar ${medicamento} - Dosis: ${dosis}`,
      tipo: 'medicamento',
      prioridad: 'alta',
      fechaProgramada: proximaToma,
      metadatos: {
        medicamento,
        dosis,
        proximaToma
      },
      esRecurrente: true
    });
  }

  // Obtener resumen de notificaciones para dashboard
  async getNotificationSummary(usuarioId) {
    try {
      const resumen = await Notificacion.findAll({
        where: { usuarioId },
        attributes: [
          'estado',
          'tipo',
          'prioridad',
          [require('sequelize').fn('COUNT', require('sequelize').col('id')), 'cantidad']
        ],
        group: ['estado', 'tipo', 'prioridad'],
        raw: true
      });

      const stats = {
        total: 0,
        pendientes: 0,
        leidas: 0,
        criticas: 0,
        porTipo: {},
        recientes: []
      };

      resumen.forEach(item => {
        stats.total += parseInt(item.cantidad);
        if (item.estado === 'pendiente') stats.pendientes += parseInt(item.cantidad);
        if (item.estado === 'leida') stats.leidas += parseInt(item.cantidad);
        if (item.prioridad === 'critica') stats.criticas += parseInt(item.cantidad);
        
        stats.porTipo[item.tipo] = (stats.porTipo[item.tipo] || 0) + parseInt(item.cantidad);
      });

      // Obtener notificaciones recientes
      const recientes = await Notificacion.findAll({
        where: { usuarioId },
        order: [['createdAt', 'DESC']],
        limit: 5,
        attributes: ['id', 'titulo', 'tipo', 'prioridad', 'estado', 'createdAt']
      });

      stats.recientes = recientes;

      return stats;
    } catch (error) {
      console.error('Error obteniendo resumen de notificaciones:', error);
      throw error;
    }
  }

  // Limpiar notificaciones antiguas
  async cleanupOldNotifications(diasAntiguedad = 30) {
    try {
      const fechaLimite = new Date();
      fechaLimite.setDate(fechaLimite.getDate() - diasAntiguedad);

      const eliminadas = await Notificacion.destroy({
        where: {
          estado: 'archivada',
          updatedAt: {
            [Op.lt]: fechaLimite
          }
        }
      });

      console.log(`Eliminadas ${eliminadas} notificaciones antiguas`);
      return eliminadas;
    } catch (error) {
      console.error('Error limpiando notificaciones antiguas:', error);
      throw error;
    }
  }
}

module.exports = new NotificationService();