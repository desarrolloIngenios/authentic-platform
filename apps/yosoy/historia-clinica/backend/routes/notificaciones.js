const express = require('express');
const router = express.Router();
const { Op } = require('sequelize');
const auth = require('../middleware/auth');
const { Notificacion, Usuario } = require('../models');

// Obtener todas las notificaciones del usuario
router.get('/', auth, async (req, res) => {
  try {
    const { page = 1, limit = 50, estado, tipo, prioridad } = req.query;
    const offset = (page - 1) * limit;

    const whereClause = { usuarioId: req.user.id };
    
    if (estado) whereClause.estado = estado;
    if (tipo) whereClause.tipo = tipo;
    if (prioridad) whereClause.prioridad = prioridad;

    const notificaciones = await Notificacion.findAndCountAll({
      where: whereClause,
      include: [{
        model: Usuario,
        as: 'usuario',
        attributes: ['nombre', 'rol']
      }],
      order: [
        ['prioridad', 'DESC'],
        ['fechaProgramada', 'ASC'],
        ['createdAt', 'DESC']
      ],
      limit: parseInt(limit),
      offset: parseInt(offset)
    });

    res.json({
      notificaciones: notificaciones.rows,
      total: notificaciones.count,
      page: parseInt(page),
      pages: Math.ceil(notificaciones.count / limit)
    });
  } catch (error) {
    console.error('Error obteniendo notificaciones:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Obtener notificaciones pendientes para tiempo real
router.get('/pendientes', auth, async (req, res) => {
  try {
    const notificaciones = await Notificacion.findAll({
      where: {
        usuarioId: req.user.id,
        estado: 'pendiente',
        [Op.or]: [
          { fechaProgramada: null },
          { fechaProgramada: { [Op.lte]: new Date() } }
        ]
      },
      include: [{
        model: Usuario,
        as: 'usuario',
        attributes: ['nombre']
      }],
      order: [
        ['prioridad', 'DESC'],
        ['createdAt', 'DESC']
      ],
      limit: 20
    });

    res.json(notificaciones);
  } catch (error) {
    console.error('Error obteniendo notificaciones pendientes:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Crear nueva notificación
router.post('/', auth, async (req, res) => {
  try {
    const {
      usuarioId,
      titulo,
      mensaje,
      tipo = 'sistema',
      prioridad = 'media',
      fechaProgramada,
      recursoId,
      tipoRecurso,
      metadatos,
      esRecurrente = false,
      configuracionRecurrencia,
      canalEnvio = { push: true, email: false, sms: false, inApp: true }
    } = req.body;

    // Validar que el usuario existe
    const usuario = await Usuario.findByPk(usuarioId);
    if (!usuario) {
      return res.status(404).json({ error: 'Usuario no encontrado' });
    }

    const notificacion = await Notificacion.create({
      usuarioId,
      titulo,
      mensaje,
      tipo,
      prioridad,
      fechaProgramada: fechaProgramada ? new Date(fechaProgramada) : null,
      recursoId,
      tipoRecurso,
      metadatos,
      esRecurrente,
      configuracionRecurrencia,
      canalEnvio,
      fechaEnviada: fechaProgramada ? null : new Date()
    });

    const notificacionCompleta = await Notificacion.findByPk(notificacion.id, {
      include: [{
        model: Usuario,
        as: 'usuario',
        attributes: ['nombre', 'email']
      }]
    });

    res.status(201).json(notificacionCompleta);
  } catch (error) {
    console.error('Error creando notificación:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Marcar notificación como leída
router.patch('/:id/leer', auth, async (req, res) => {
  try {
    const notificacion = await Notificacion.findOne({
      where: {
        id: req.params.id,
        usuarioId: req.user.id
      }
    });

    if (!notificacion) {
      return res.status(404).json({ error: 'Notificación no encontrada' });
    }

    await notificacion.update({
      estado: 'leida',
      fechaLeida: new Date()
    });

    res.json(notificacion);
  } catch (error) {
    console.error('Error marcando notificación como leída:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Marcar múltiples notificaciones como leídas
router.patch('/leer-multiples', auth, async (req, res) => {
  try {
    const { ids } = req.body;

    if (!Array.isArray(ids) || ids.length === 0) {
      return res.status(400).json({ error: 'Se requiere un array de IDs' });
    }

    const resultado = await Notificacion.update(
      {
        estado: 'leida',
        fechaLeida: new Date()
      },
      {
        where: {
          id: { [Op.in]: ids },
          usuarioId: req.user.id
        }
      }
    );

    res.json({ 
      mensaje: 'Notificaciones marcadas como leídas',
      actualizadas: resultado[0]
    });
  } catch (error) {
    console.error('Error marcando notificaciones como leídas:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Archivar notificación
router.patch('/:id/archivar', auth, async (req, res) => {
  try {
    const notificacion = await Notificacion.findOne({
      where: {
        id: req.params.id,
        usuarioId: req.user.id
      }
    });

    if (!notificacion) {
      return res.status(404).json({ error: 'Notificación no encontrada' });
    }

    await notificacion.update({ estado: 'archivada' });

    res.json(notificacion);
  } catch (error) {
    console.error('Error archivando notificación:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Eliminar notificación
router.delete('/:id', auth, async (req, res) => {
  try {
    const notificacion = await Notificacion.findOne({
      where: {
        id: req.params.id,
        usuarioId: req.user.id
      }
    });

    if (!notificacion) {
      return res.status(404).json({ error: 'Notificación no encontrada' });
    }

    await notificacion.destroy();

    res.json({ mensaje: 'Notificación eliminada exitosamente' });
  } catch (error) {
    console.error('Error eliminando notificación:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Obtener estadísticas de notificaciones
router.get('/estadisticas', auth, async (req, res) => {
  try {
    const estadisticas = await Notificacion.findAll({
      where: { usuarioId: req.user.id },
      attributes: [
        'estado',
        'tipo',
        'prioridad',
        [sequelize.fn('COUNT', sequelize.col('id')), 'cantidad']
      ],
      group: ['estado', 'tipo', 'prioridad'],
      raw: true
    });

    const resumen = {
      total: 0,
      pendientes: 0,
      leidas: 0,
      archivadas: 0,
      porTipo: {},
      porPrioridad: {}
    };

    estadisticas.forEach(stat => {
      resumen.total += parseInt(stat.cantidad);
      resumen[stat.estado] = (resumen[stat.estado] || 0) + parseInt(stat.cantidad);
      resumen.porTipo[stat.tipo] = (resumen.porTipo[stat.tipo] || 0) + parseInt(stat.cantidad);
      resumen.porPrioridad[stat.prioridad] = (resumen.porPrioridad[stat.prioridad] || 0) + parseInt(stat.cantidad);
    });

    res.json(resumen);
  } catch (error) {
    console.error('Error obteniendo estadísticas:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Crear notificaciones automáticas para citas próximas
router.post('/programar-recordatorios-citas', auth, async (req, res) => {
  try {
    const { CitaMedica } = require('../models');
    
    // Obtener citas programadas para los próximos 7 días
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

    const notificacionesCreadas = [];

    for (const cita of citas) {
      // Recordatorio 24 horas antes
      const fecha24h = new Date(cita.fecha);
      fecha24h.setHours(fecha24h.getHours() - 24);

      // Recordatorio 1 hora antes
      const fecha1h = new Date(cita.fecha);
      fecha1h.setHours(fecha1h.getHours() - 1);

      if (fecha24h > new Date()) {
        const notif24h = await Notificacion.create({
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
            hora: cita.hora
          }
        });
        notificacionesCreadas.push(notif24h);
      }

      if (fecha1h > new Date()) {
        const notif1h = await Notificacion.create({
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
            meetLink: cita.meetLink
          }
        });
        notificacionesCreadas.push(notif1h);
      }
    }

    res.json({
      mensaje: 'Recordatorios de citas programados',
      notificaciones: notificacionesCreadas.length,
      citas: citas.length
    });
  } catch (error) {
    console.error('Error programando recordatorios:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

module.exports = router;