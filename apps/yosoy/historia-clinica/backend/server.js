const express = require('express');
const helmet = require('helmet');
const cors = require('cors');
const rateLimit = require('express-rate-limit');
const compression = require('compression');
const morgan = require('morgan');
const { Sequelize, DataTypes } = require('sequelize');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const crypto = require('crypto');
const { body, validationResult } = require('express-validator');
const moment = require('moment');
require('dotenv').config();

// Configuración de la aplicación
const app = express();
const PORT = process.env.PORT || 3001;

// ===== CONFIGURACIÓN DE SEGURIDAD =====

app.use(helmet({
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'", "https://fonts.googleapis.com", "https://cdn.tailwindcss.com"],
      scriptSrc: ["'self'", "https://unpkg.com", "https://cdn.jsdelivr.net", "https://cdnjs.cloudflare.com"],
      imgSrc: ["'self'", "data:", "https:"],
      connectSrc: ["'self'", "http://localhost:3001", "https://meet.google.com"],
      fontSrc: ["'self'", "https://fonts.gstatic.com"],
      objectSrc: ["'none'"],
      mediaSrc: ["'self'"],
      frameSrc: ["'self'", "https://meet.google.com"],
    },
  },
  crossOriginEmbedderPolicy: false
}));

// Rate limiting
const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutos
  max: 200, // máximo 200 requests por ventana (más permisivo para desarrollo)
  message: {
    error: 'Demasiadas solicitudes, intente de nuevo más tarde.',
    retryAfter: '15 minutos'
  },
  standardHeaders: true,
  legacyHeaders: false,
});

app.use('/api/', limiter);

// CORS configurado para desarrollo y producción
const corsOptions = {
  origin: process.env.NODE_ENV === 'production' 
    ? ['https://hc.yo-soy.co', 'https://yo-soy.co'] 
    : ['http://localhost:8080', 'http://localhost:3000', 'http://127.0.0.1:8080'],
  credentials: true,
  optionsSuccessStatus: 200,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
};

app.use(cors(corsOptions));
app.use(compression());
app.use(morgan('combined'));
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// ===== CONFIGURACIÓN SSE PARA NOTIFICACIONES EN TIEMPO REAL =====

// Mapa para gestionar conexiones SSE de usuarios
const sseConnections = new Map();

// Middleware para validar token en SSE
const authenticateSSE = (req, res, next) => {
  const token = req.query.token || req.headers.authorization?.split(' ')[1];
  
  if (!token) {
    return res.status(401).json({ error: 'Token de autenticación requerido' });
  }

  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    req.user = decoded;
    next();
  } catch (error) {
    console.error('Token inválido para SSE:', error);
    return res.status(401).json({ error: 'Token inválido' });
  }
};

// Endpoint SSE para notificaciones en tiempo real
app.get('/api/notificaciones/stream', authenticateSSE, (req, res) => {
  // Configurar headers SSE
  res.writeHead(200, {
    'Content-Type': 'text/event-stream',
    'Cache-Control': 'no-cache',
    'Connection': 'keep-alive',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Cache-Control'
  });

  const userId = req.user.id;
  
  // Agregar conexión al mapa
  sseConnections.set(userId, res);
  
  // Enviar evento de conexión
  res.write(`data: ${JSON.stringify({
    type: 'connected',
    message: 'Conectado al stream de notificaciones',
    timestamp: new Date().toISOString()
  })}\n\n`);

  // Heartbeat cada 30 segundos
  const heartbeat = setInterval(() => {
    if (!res.finished) {
      res.write(`data: ${JSON.stringify({
        type: 'heartbeat',
        timestamp: new Date().toISOString()
      })}\n\n`);
    } else {
      clearInterval(heartbeat);
    }
  }, 30000);

  // Manejar desconexión
  req.on('close', () => {
    console.log(`Usuario ${userId} desconectado del stream SSE`);
    sseConnections.delete(userId);
    clearInterval(heartbeat);
  });

  req.on('error', (error) => {
    console.error('Error en conexión SSE:', error);
    sseConnections.delete(userId);
    clearInterval(heartbeat);
  });
});

// Función para enviar notificación en tiempo real
const enviarNotificacionTiempoReal = async (usuarioId, notificacion) => {
  const connection = sseConnections.get(usuarioId);
  if (connection && !connection.finished) {
    try {
      connection.write(`data: ${JSON.stringify({
        type: 'nueva_notificacion',
        notificacion: notificacion,
        timestamp: new Date().toISOString()
      })}\n\n`);
    } catch (error) {
      console.error('Error enviando notificación SSE:', error);
      sseConnections.delete(usuarioId);
    }
  }
};

// Función para verificar y enviar notificaciones programadas
const verificarNotificacionesProgramadas = async () => {
  try {
    const notificacionesPendientes = await Notificacion.findAll({
      where: {
        estado: 'pendiente',
        fechaProgramada: {
          [sequelize.Op.lte]: new Date()
        },
        fechaEnviada: null
      },
      include: [{
        model: Usuario,
        attributes: ['id', 'nombre', 'email']
      }]
    });

    for (const notificacion of notificacionesPendientes) {
      // Marcar como enviada
      await notificacion.update({
        fechaEnviada: new Date()
      });

      // Enviar por SSE si el usuario está conectado
      await enviarNotificacionTiempoReal(notificacion.usuarioId, notificacion);

      console.log(`Notificación enviada: ${notificacion.titulo} a usuario ${notificacion.usuarioId}`);
    }
  } catch (error) {
    console.error('Error verificando notificaciones programadas:', error);
  }
};

// Verificar notificaciones programadas cada 30 segundos
setInterval(verificarNotificacionesProgramadas, 30000);

// ===== CONFIGURACIÓN DE BASE DE DATOS =====

// Usar SQLite para desarrollo local
const sequelize = new Sequelize({
  dialect: 'sqlite',
  storage: process.env.NODE_ENV === 'production' 
    ? '/app/data/yosoy_hc.sqlite' 
    : './yosoy_hc.sqlite',
  logging: process.env.NODE_ENV === 'development' ? console.log : false,
  pool: {
    max: 5,
    min: 0,
    acquire: 30000,
    idle: 10000
  }
});

// ===== MODELOS DE BASE DE DATOS =====

// Modelo de Usuario (compatible con sistema actual)
const Usuario = sequelize.define('Usuario', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  username: {
    type: DataTypes.STRING(50),
    allowNull: false,
    unique: true
  },
  password: {
    type: DataTypes.STRING(255),
    allowNull: false
  },
  nombre: {
    type: DataTypes.STRING(100),
    allowNull: false
  },
  email: {
    type: DataTypes.STRING(100),
    allowNull: false,
    unique: true
  },
  rol: {
    type: DataTypes.ENUM('admin', 'medico', 'enfermera'),
    allowNull: false,
    defaultValue: 'medico'
  },
  especialidad: {
    type: DataTypes.STRING(100),
    allowNull: true
  },
  isActive: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  },
  lastLogin: {
    type: DataTypes.DATE,
    allowNull: true
  }
}, {
  tableName: 'usuarios',
  timestamps: true
});

// Modelo de Paciente
const Paciente = sequelize.define('Paciente', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  nombre: {
    type: DataTypes.STRING(100),
    allowNull: false
  },
  telefono: {
    type: DataTypes.STRING(20),
    allowNull: false
  },
  email: {
    type: DataTypes.STRING(100),
    allowNull: true
  },
  fechaNacimiento: {
    type: DataTypes.DATEONLY,
    allowNull: true
  },
  genero: {
    type: DataTypes.ENUM('M', 'F', 'Otro'),
    allowNull: true
  },
  direccion: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  contactoEmergencia: {
    type: DataTypes.STRING(100),
    allowNull: true
  },
  telefonoEmergencia: {
    type: DataTypes.STRING(20),
    allowNull: true
  },
  isActive: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  },
  creadoPor: {
    type: DataTypes.INTEGER,
    references: {
      model: Usuario,
      key: 'id'
    }
  }
}, {
  tableName: 'pacientes',
  timestamps: true
});

// Modelo de Historia Clínica
const HistoriaClinica = sequelize.define('HistoriaClinica', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  pacienteId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: Paciente,
      key: 'id'
    }
  },
  medicoId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: Usuario,
      key: 'id'
    }
  },
  motivo: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  antecedentes: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  examenFisico: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  diagnostico: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  analisis: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  planManejo: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  version: {
    type: DataTypes.INTEGER,
    defaultValue: 1
  },
  isActive: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  }
}, {
  tableName: 'historias_clinicas',
  timestamps: true
});

// Modelo de Fórmula Médica
const FormulaMedica = sequelize.define('FormulaMedica', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  pacienteId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: Paciente,
      key: 'id'
    }
  },
  medicoId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: Usuario,
      key: 'id'
    }
  },
  nombrePaciente: {
    type: DataTypes.STRING(100),
    allowNull: false
  },
  medicamento: {
    type: DataTypes.STRING(100),
    allowNull: false
  },
  dosis: {
    type: DataTypes.STRING(50),
    allowNull: false
  },
  frecuencia: {
    type: DataTypes.STRING(50),
    allowNull: false
  },
  duracion: {
    type: DataTypes.STRING(50),
    allowNull: false
  },
  indicaciones: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  fechaEmision: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW
  },
  isActive: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  }
}, {
  tableName: 'formulas_medicas',
  timestamps: true
});

// Modelo de Cita Médica
const CitaMedica = sequelize.define('CitaMedica', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  pacienteId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: Paciente,
      key: 'id'
    }
  },
  medicoId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: Usuario,
      key: 'id'
    }
  },
  paciente: {
    type: DataTypes.STRING(100),
    allowNull: false
  },
  fecha: {
    type: DataTypes.DATEONLY,
    allowNull: false
  },
  hora: {
    type: DataTypes.TIME,
    allowNull: false
  },
  motivo: {
    type: DataTypes.TEXT,
    allowNull: false
  },
  tipo: {
    type: DataTypes.ENUM('presencial', 'videoconsulta'),
    allowNull: false,
    defaultValue: 'presencial'
  },
  duracion: {
    type: DataTypes.STRING(10),
    allowNull: false,
    defaultValue: '30'
  },
  recordatorio: {
    type: DataTypes.STRING(10),
    allowNull: false,
    defaultValue: '15'
  },
  notas: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  meetLink: {
    type: DataTypes.STRING(500),
    allowNull: true
  },
  estado: {
    type: DataTypes.ENUM('programada', 'en_curso', 'completada', 'cancelada'),
    defaultValue: 'programada'
  },
  isActive: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  }
}, {
  tableName: 'citas_medicas',
  timestamps: true
});

// Modelo de Auditoría
const AuditoriaLog = sequelize.define('AuditoriaLog', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  usuarioId: {
    type: DataTypes.INTEGER,
    allowNull: true,
    references: {
      model: Usuario,
      key: 'id'
    }
  },
  accion: {
    type: DataTypes.STRING(100),
    allowNull: false
  },
  tipoRecurso: {
    type: DataTypes.STRING(50),
    allowNull: true
  },
  recursoId: {
    type: DataTypes.INTEGER,
    allowNull: true
  },
  detalles: {
    type: DataTypes.JSONB,
    allowNull: true
  },
  ipAddress: {
    type: DataTypes.STRING(45),
    allowNull: true
  },
  userAgent: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  sessionId: {
    type: DataTypes.STRING(100),
    allowNull: true
  },
  timestamp: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW
  }
}, {
  tableName: 'auditoria_logs',
  timestamps: false
});

// Modelo de Notificación
const Notificacion = sequelize.define('Notificacion', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  usuarioId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: 'usuarios',
      key: 'id'
    }
  },
  titulo: {
    type: DataTypes.STRING(100),
    allowNull: false
  },
  mensaje: {
    type: DataTypes.TEXT,
    allowNull: false
  },
  tipo: {
    type: DataTypes.ENUM('cita', 'examen', 'medicamento', 'urgente', 'sistema', 'mensaje'),
    allowNull: false,
    defaultValue: 'sistema'
  },
  prioridad: {
    type: DataTypes.ENUM('baja', 'media', 'alta', 'critica'),
    allowNull: false,
    defaultValue: 'media'
  },
  estado: {
    type: DataTypes.ENUM('pendiente', 'leida', 'archivada'),
    allowNull: false,
    defaultValue: 'pendiente'
  },
  fechaProgramada: {
    type: DataTypes.DATE,
    allowNull: true
  },
  fechaEnviada: {
    type: DataTypes.DATE,
    allowNull: true
  },
  fechaLeida: {
    type: DataTypes.DATE,
    allowNull: true
  },
  recursoId: {
    type: DataTypes.INTEGER,
    allowNull: true
  },
  tipoRecurso: {
    type: DataTypes.STRING(50),
    allowNull: true
  },
  metadatos: {
    type: DataTypes.JSONB,
    allowNull: true
  },
  esRecurrente: {
    type: DataTypes.BOOLEAN,
    defaultValue: false
  },
  configuracionRecurrencia: {
    type: DataTypes.JSONB,
    allowNull: true
  },
  canalEnvio: {
    type: DataTypes.JSONB,
    allowNull: true,
    defaultValue: {
      push: true,
      email: false,
      sms: false,
      inApp: true
    }
  },
  isActive: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  }
}, {
  tableName: 'notificaciones',
  timestamps: true
});

// Definir asociaciones
Usuario.hasMany(Paciente, { foreignKey: 'creadoPor' });
Paciente.belongsTo(Usuario, { foreignKey: 'creadoPor' });

Paciente.hasMany(HistoriaClinica, { foreignKey: 'pacienteId' });
HistoriaClinica.belongsTo(Paciente, { foreignKey: 'pacienteId' });

Usuario.hasMany(HistoriaClinica, { foreignKey: 'medicoId' });
HistoriaClinica.belongsTo(Usuario, { foreignKey: 'medicoId' });

Paciente.hasMany(FormulaMedica, { foreignKey: 'pacienteId' });
FormulaMedica.belongsTo(Paciente, { foreignKey: 'pacienteId' });

Usuario.hasMany(FormulaMedica, { foreignKey: 'medicoId' });
FormulaMedica.belongsTo(Usuario, { foreignKey: 'medicoId' });

Paciente.hasMany(CitaMedica, { foreignKey: 'pacienteId' });
CitaMedica.belongsTo(Paciente, { foreignKey: 'pacienteId' });

Usuario.hasMany(CitaMedica, { foreignKey: 'medicoId' });
CitaMedica.belongsTo(Usuario, { foreignKey: 'medicoId' });

Usuario.hasMany(AuditoriaLog, { foreignKey: 'usuarioId' });
AuditoriaLog.belongsTo(Usuario, { foreignKey: 'usuarioId' });

Usuario.hasMany(Notificacion, { foreignKey: 'usuarioId' });
Notificacion.belongsTo(Usuario, { foreignKey: 'usuarioId' });

// ===== UTILIDADES DE SEGURIDAD =====

class SecurityUtils {
  static encrypt(data) {
    const algorithm = 'aes-256-cbc';
    const key = crypto.scryptSync(process.env.ENCRYPTION_KEY || 'yosoy-default-key', 'salt', 32);
    const iv = crypto.randomBytes(16);
    const cipher = crypto.createCipher(algorithm, key);
    
    let encrypted = cipher.update(JSON.stringify(data), 'utf8', 'hex');
    encrypted += cipher.final('hex');
    
    return {
      encrypted,
      iv: iv.toString('hex')
    };
  }

  static decrypt(encryptedData) {
    const algorithm = 'aes-256-cbc';
    const key = crypto.scryptSync(process.env.ENCRYPTION_KEY || 'yosoy-default-key', 'salt', 32);
    const decipher = crypto.createDecipher(algorithm, key);
    
    let decrypted = decipher.update(encryptedData.encrypted, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    
    return JSON.parse(decrypted);
  }

  static sanitizeInput(input) {
    if (typeof input !== 'string') return input;
    return input
      .replace(/[<>]/g, '')
      .replace(/['"]/g, '')
      .trim();
  }

  static generateToken() {
    return crypto.randomBytes(32).toString('hex');
  }
}

// Middleware de autenticación
const authenticateToken = async (req, res, next) => {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];

  if (!token) {
    await logAuditEvent(null, 'AUTH_FAILED', 'No token provided', req);
    return res.status(401).json({ error: 'Token de acceso requerido' });
  }

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'yosoy-jwt-secret-2024');
    const user = await Usuario.findByPk(decoded.userId);
    
    if (!user || !user.isActive) {
      await logAuditEvent(decoded.userId, 'AUTH_FAILED', 'User not found or inactive', req);
      return res.status(401).json({ error: 'Usuario no válido' });
    }

    req.user = user;
    next();
  } catch (error) {
    await logAuditEvent(null, 'AUTH_FAILED', 'Invalid token', req);
    return res.status(403).json({ error: 'Token inválido' });
  }
};

// Función de auditoría
const logAuditEvent = async (userId, action, details, req) => {
  try {
    await AuditoriaLog.create({
      usuarioId: userId,
      accion: action,
      detalles: typeof details === 'string' ? { message: details } : details,
      ipAddress: req.ip || req.connection.remoteAddress,
      userAgent: req.get('User-Agent'),
      sessionId: req.sessionId || 'unknown'
    });
  } catch (error) {
    console.error('Error logging audit event:', error);
  }
};

// ===== RUTAS DE AUTENTICACIÓN =====

app.post('/api/auth/login', [
  body('username').isLength({ min: 3 }).trim().escape(),
  body('password').isLength({ min: 4 })
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      await logAuditEvent(null, 'LOGIN_VALIDATION_FAILED', errors.array(), req);
      return res.status(400).json({ errors: errors.array() });
    }

    const { username, password } = req.body;
    
    const user = await Usuario.findOne({ where: { username: username.toLowerCase() } });
    
    if (!user) {
      await logAuditEvent(null, 'LOGIN_FAILED', `Username not found: ${username}`, req);
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }

    const isValidPassword = await bcrypt.compare(password, user.password);
    
    if (!isValidPassword) {
      await logAuditEvent(user.id, 'LOGIN_FAILED', 'Invalid password', req);
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }

    // Login exitoso
    await user.update({ lastLogin: new Date() });

    const token = jwt.sign(
      { userId: user.id, role: user.rol },
      process.env.JWT_SECRET || 'yosoy-jwt-secret-2024',
      { expiresIn: '8h' }
    );

    await logAuditEvent(user.id, 'LOGIN_SUCCESS', 'Successful authentication', req);

    res.json({
      token,
      user: {
        id: user.id,
        username: user.username,
        nombre: user.nombre,
        rol: user.rol,
        email: user.email,
        especialidad: user.especialidad
      }
    });

  } catch (error) {
    console.error('Login error:', error);
    await logAuditEvent(null, 'LOGIN_ERROR', error.message, req);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// ===== RUTAS DE PACIENTES =====

app.get('/api/pacientes', authenticateToken, async (req, res) => {
  try {
    const pacientes = await Paciente.findAll({
      where: { isActive: true },
      include: [{
        model: Usuario,
        attributes: ['nombre', 'username']
      }],
      order: [['createdAt', 'DESC']]
    });

    await logAuditEvent(req.user.id, 'PATIENTS_LIST_ACCESSED', `Retrieved ${pacientes.length} patients`, req);

    res.json(pacientes);
  } catch (error) {
    console.error('Error fetching patients:', error);
    await logAuditEvent(req.user.id, 'PATIENTS_LIST_ERROR', error.message, req);
    res.status(500).json({ error: 'Error al obtener pacientes' });
  }
});

app.post('/api/pacientes', authenticateToken, [
  body('nombre').isLength({ min: 2 }).trim().escape(),
  body('telefono').isLength({ min: 10, max: 15 }).trim(),
  body('email').optional().isEmail().normalizeEmail()
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      await logAuditEvent(req.user.id, 'PATIENT_CREATE_VALIDATION_FAILED', errors.array(), req);
      return res.status(400).json({ errors: errors.array() });
    }

    const pacienteData = {
      ...req.body,
      creadoPor: req.user.id,
      nombre: SecurityUtils.sanitizeInput(req.body.nombre),
      telefono: SecurityUtils.sanitizeInput(req.body.telefono)
    };

    const paciente = await Paciente.create(pacienteData);

    await logAuditEvent(req.user.id, 'PATIENT_CREATED', { pacienteId: paciente.id, nombre: paciente.nombre }, req);

    res.status(201).json(paciente);
  } catch (error) {
    console.error('Error creating patient:', error);
    await logAuditEvent(req.user.id, 'PATIENT_CREATE_ERROR', error.message, req);
    res.status(500).json({ error: 'Error al crear paciente' });
  }
});

// ===== RUTAS DE HISTORIAS CLÍNICAS =====

app.get('/api/historias-clinicas', authenticateToken, async (req, res) => {
  try {
    const historias = await HistoriaClinica.findAll({
      where: { isActive: true },
      include: [
        {
          model: Paciente,
          attributes: ['nombre', 'telefono']
        },
        {
          model: Usuario,
          attributes: ['nombre', 'especialidad']
        }
      ],
      order: [['createdAt', 'DESC']]
    });

    await logAuditEvent(req.user.id, 'MEDICAL_RECORDS_ACCESSED', `Retrieved ${historias.length} records`, req);

    res.json(historias);
  } catch (error) {
    console.error('Error fetching medical records:', error);
    await logAuditEvent(req.user.id, 'MEDICAL_RECORDS_ERROR', error.message, req);
    res.status(500).json({ error: 'Error al obtener historias clínicas' });
  }
});

app.post('/api/historias-clinicas', authenticateToken, [
  body('nombrePaciente').isLength({ min: 2 }).trim().escape(),
  body('motivo').isLength({ min: 10 }).trim()
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ errors: errors.array() });
    }

    // Buscar o crear paciente
    let paciente = await Paciente.findOne({ where: { nombre: req.body.nombrePaciente } });
    if (!paciente) {
      paciente = await Paciente.create({
        nombre: req.body.nombrePaciente,
        telefono: req.body.telefonoPaciente || 'Sin especificar',
        creadoPor: req.user.id
      });
    }

    const historiaData = {
      ...req.body,
      pacienteId: paciente.id,
      medicoId: req.user.id
    };

    const historia = await HistoriaClinica.create(historiaData);

    await logAuditEvent(req.user.id, 'MEDICAL_RECORD_CREATED', { 
      historiaId: historia.id, 
      paciente: req.body.nombrePaciente 
    }, req);

    res.status(201).json(historia);
  } catch (error) {
    console.error('Error creating medical record:', error);
    res.status(500).json({ error: 'Error al crear historia clínica' });
  }
});

// ===== RUTAS DE FÓRMULAS MÉDICAS =====

app.get('/api/formulas-medicas', authenticateToken, async (req, res) => {
  try {
    const formulas = await FormulaMedica.findAll({
      where: { isActive: true },
      include: [
        {
          model: Paciente,
          attributes: ['nombre', 'telefono']
        },
        {
          model: Usuario,
          attributes: ['nombre', 'especialidad']
        }
      ],
      order: [['createdAt', 'DESC']]
    });

    await logAuditEvent(req.user.id, 'FORMULAS_ACCESSED', `Retrieved ${formulas.length} formulas`, req);

    res.json(formulas);
  } catch (error) {
    console.error('Error fetching formulas:', error);
    res.status(500).json({ error: 'Error al obtener fórmulas médicas' });
  }
});

app.post('/api/formulas-medicas', authenticateToken, [
  body('nombrePaciente').isLength({ min: 2 }).trim().escape(),
  body('medicamento').isLength({ min: 2 }).trim(),
  body('dosis').isLength({ min: 1 }).trim(),
  body('frecuencia').isLength({ min: 1 }).trim(),
  body('duracion').isLength({ min: 1 }).trim()
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ errors: errors.array() });
    }

    // Buscar o crear paciente
    let paciente = await Paciente.findOne({ where: { nombre: req.body.nombrePaciente } });
    if (!paciente) {
      paciente = await Paciente.create({
        nombre: req.body.nombrePaciente,
        telefono: 'Sin especificar',
        creadoPor: req.user.id
      });
    }

    const formulaData = {
      ...req.body,
      pacienteId: paciente.id,
      medicoId: req.user.id,
      fechaEmision: new Date()
    };

    const formula = await FormulaMedica.create(formulaData);

    await logAuditEvent(req.user.id, 'FORMULA_CREATED', { 
      formulaId: formula.id, 
      paciente: req.body.nombrePaciente,
      medicamento: req.body.medicamento
    }, req);

    res.status(201).json(formula);
  } catch (error) {
    console.error('Error creating formula:', error);
    res.status(500).json({ error: 'Error al crear fórmula médica' });
  }
});

// ===== RUTAS DE CITAS MÉDICAS =====

app.get('/api/citas', authenticateToken, async (req, res) => {
  try {
    const citas = await CitaMedica.findAll({
      where: { isActive: true },
      include: [
        {
          model: Paciente,
          attributes: ['nombre', 'telefono', 'email']
        },
        {
          model: Usuario,
          attributes: ['nombre', 'especialidad']
        }
      ],
      order: [['fecha', 'ASC'], ['hora', 'ASC']]
    });

    await logAuditEvent(req.user.id, 'CITAS_ACCESSED', `Retrieved ${citas.length} citas`, req);

    res.json(citas);
  } catch (error) {
    console.error('Error fetching citas:', error);
    res.status(500).json({ error: 'Error al obtener citas' });
  }
});

app.post('/api/citas', authenticateToken, [
  body('paciente').isLength({ min: 2 }).trim().escape(),
  body('fecha').isISO8601(),
  body('hora').matches(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/),
  body('motivo').isLength({ min: 5 }).trim()
], async (req, res) => {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({ errors: errors.array() });
    }

    // Buscar o crear paciente
    let paciente = await Paciente.findOne({ where: { nombre: req.body.paciente } });
    if (!paciente) {
      paciente = await Paciente.create({
        nombre: req.body.paciente,
        telefono: 'Sin especificar',
        creadoPor: req.user.id
      });
    }

    let citaData = {
      ...req.body,
      pacienteId: paciente.id,
      medicoId: req.user.id
    };

    // Generar enlace de Meet si es videoconsulta
    if (req.body.tipo === 'videoconsulta') {
      const meetId = `hc-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
      citaData.meetLink = `https://meet.google.com/${meetId}`;
    }

    const cita = await CitaMedica.create(citaData);

    await logAuditEvent(req.user.id, 'CITA_CREATED', { 
      citaId: cita.id, 
      paciente: req.body.paciente,
      tipo: req.body.tipo,
      fecha: req.body.fecha
    }, req);

    res.status(201).json(cita);
  } catch (error) {
    console.error('Error creating cita:', error);
    res.status(500).json({ error: 'Error al crear cita' });
  }
});

// ===== RUTAS DE ESTADÍSTICAS =====

app.get('/api/estadisticas', authenticateToken, async (req, res) => {
  try {
    const totalPacientes = await Paciente.count({ where: { isActive: true } });
    const totalHistorias = await HistoriaClinica.count({ where: { isActive: true } });
    const totalFormulas = await FormulaMedica.count({ where: { isActive: true } });
    const totalCitas = await CitaMedica.count({ where: { isActive: true } });

    // Estadísticas por género
    const distribucionGenero = await Paciente.findAll({
      attributes: [
        'genero',
        [sequelize.fn('COUNT', sequelize.col('genero')), 'count']
      ],
      where: { isActive: true, genero: { [sequelize.Op.ne]: null } },
      group: ['genero'],
      raw: true
    });

    // Diagnósticos más frecuentes
    const topDiagnosticos = await HistoriaClinica.findAll({
      attributes: [
        'diagnostico',
        [sequelize.fn('COUNT', sequelize.col('diagnostico')), 'count']
      ],
      where: { 
        isActive: true, 
        diagnostico: { [sequelize.Op.ne]: null, [sequelize.Op.ne]: '' } 
      },
      group: ['diagnostico'],
      order: [[sequelize.fn('COUNT', sequelize.col('diagnostico')), 'DESC']],
      limit: 5,
      raw: true
    });

    // Medicamentos más recetados
    const topMedicamentos = await FormulaMedica.findAll({
      attributes: [
        'medicamento',
        [sequelize.fn('COUNT', sequelize.col('medicamento')), 'count']
      ],
      where: { isActive: true },
      group: ['medicamento'],
      order: [[sequelize.fn('COUNT', sequelize.col('medicamento')), 'DESC']],
      limit: 5,
      raw: true
    });

    // Citas por tipo
    const citasPorTipo = await CitaMedica.findAll({
      attributes: [
        'tipo',
        [sequelize.fn('COUNT', sequelize.col('tipo')), 'count']
      ],
      where: { isActive: true },
      group: ['tipo'],
      raw: true
    });

    await logAuditEvent(req.user.id, 'STATS_ACCESSED', 'Dashboard statistics accessed', req);

    res.json({
      totalPacientes,
      totalHistorias,
      totalFormulas,
      totalCitas,
      distribucionGenero: distribucionGenero.reduce((acc, item) => {
        acc[item.genero || 'No especificado'] = parseInt(item.count);
        return acc;
      }, {}),
      topDiagnosticos: topDiagnosticos.map(item => ({
        name: item.diagnostico,
        count: parseInt(item.count)
      })),
      topMedicamentos: topMedicamentos.map(item => ({
        name: item.medicamento,
        count: parseInt(item.count)
      })),
      citasPorTipo: citasPorTipo.reduce((acc, item) => {
        acc[item.tipo] = parseInt(item.count);
        return acc;
      }, {})
    });

  } catch (error) {
    console.error('Error fetching statistics:', error);
    res.status(500).json({ error: 'Error al obtener estadísticas' });
  }
});

// ===== HEALTH CHECK =====

app.get('/health', (req, res) => {
  res.json({ 
    status: 'OK', 
    timestamp: new Date().toISOString(),
    version: '1.0.0',
    service: 'YoSoy Historia Clínica API'
  });
});

app.get('/api/health', (req, res) => {
  res.json({ 
    status: 'OK', 
    database: 'connected',
    timestamp: new Date().toISOString()
  });
});

// ===== RUTAS DE NOTIFICACIONES =====

// Obtener todas las notificaciones del usuario
app.get('/api/notificaciones', authenticateToken, async (req, res) => {
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
app.get('/api/notificaciones/pendientes', authenticateToken, async (req, res) => {
  try {
    const notificaciones = await Notificacion.findAll({
      where: {
        usuarioId: req.user.id,
        estado: 'pendiente',
        [sequelize.Op.or]: [
          { fechaProgramada: null },
          { fechaProgramada: { [sequelize.Op.lte]: new Date() } }
        ]
      },
      include: [{
        model: Usuario,
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
app.post('/api/notificaciones', authenticateToken, async (req, res) => {
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
        attributes: ['nombre', 'email']
      }]
    });

    await logAuditEvent(req.user.id, 'NOTIFICATION_CREATED', 'Notificación creada', req, {
      notificacionId: notificacion.id,
      titulo: titulo,
      tipo: tipo
    });

    // Enviar notificación en tiempo real si no está programada
    if (!fechaProgramada) {
      await enviarNotificacionTiempoReal(usuarioId, notificacionCompleta);
    }

    res.status(201).json(notificacionCompleta);
  } catch (error) {
    console.error('Error creando notificación:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Marcar notificación como leída
app.patch('/api/notificaciones/:id/leer', authenticateToken, async (req, res) => {
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

    await logAuditEvent(req.user.id, 'NOTIFICATION_READ', 'Notificación marcada como leída', req, {
      notificacionId: notificacion.id
    });

    res.json(notificacion);
  } catch (error) {
    console.error('Error marcando notificación como leída:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Marcar múltiples notificaciones como leídas
app.patch('/api/notificaciones/leer-multiples', authenticateToken, async (req, res) => {
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
          id: { [sequelize.Op.in]: ids },
          usuarioId: req.user.id
        }
      }
    );

    await logAuditEvent(req.user.id, 'NOTIFICATIONS_BULK_READ', 'Notificaciones marcadas como leídas en lote', req, {
      notificacionesIds: ids,
      cantidad: resultado[0]
    });

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
app.patch('/api/notificaciones/:id/archivar', authenticateToken, async (req, res) => {
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

    await logAuditEvent(req.user.id, 'NOTIFICATION_ARCHIVED', 'Notificación archivada', req, {
      notificacionId: notificacion.id
    });

    res.json(notificacion);
  } catch (error) {
    console.error('Error archivando notificación:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Eliminar notificación
app.delete('/api/notificaciones/:id', authenticateToken, async (req, res) => {
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

    await logAuditEvent(req.user.id, 'NOTIFICATION_DELETED', 'Notificación eliminada', req, {
      notificacionId: notificacion.id
    });

    res.json({ mensaje: 'Notificación eliminada exitosamente' });
  } catch (error) {
    console.error('Error eliminando notificación:', error);
    res.status(500).json({ error: 'Error interno del servidor' });
  }
});

// Programar recordatorios automáticos para citas
app.post('/api/notificaciones/programar-recordatorios', authenticateToken, async (req, res) => {
  try {
    // Obtener citas programadas para los próximos 7 días
    const fechaInicio = new Date();
    const fechaFin = new Date();
    fechaFin.setDate(fechaFin.getDate() + 7);

    const citas = await CitaMedica.findAll({
      where: {
        fecha: {
          [sequelize.Op.between]: [fechaInicio, fechaFin]
        },
        estado: 'programada'
      }
    });

    const notificacionesCreadas = [];

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
              hora: cita.hora,
              tipoRecordatorio: '24h'
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
              meetLink: cita.meetLink,
              tipoRecordatorio: '1h'
            }
          });
          notificacionesCreadas.push(notif1h);
        }
      }
    }

    await logAuditEvent(req.user.id, 'REMINDERS_SCHEDULED', 'Recordatorios automáticos programados', req, {
      citas: citas.length,
      notificaciones: notificacionesCreadas.length
    });

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

// Obtener estadísticas de notificaciones
app.get('/api/notificaciones/estadisticas', authenticateToken, async (req, res) => {
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

// ===== INICIALIZACIÓN =====

const initializeDatabase = async () => {
  try {
    await sequelize.authenticate();
    console.log('✅ Conexión a base de datos PostgreSQL establecida');

    await sequelize.sync({ alter: true });
    console.log('✅ Modelos de YoSoy sincronizados');

    // Crear usuarios por defecto del sistema YoSoy
    const usuarios = [
      {
        username: 'admin',
        password: await bcrypt.hash('admin123', 12),
        nombre: 'Administrador YoSoy',
        email: 'admin@yo-soy.co',
        rol: 'admin'
      },
      {
        username: 'carlos',
        password: await bcrypt.hash('medico123', 12),
        nombre: 'Dr. Carlos Méndez',
        email: 'carlos@yo-soy.co',
        rol: 'medico',
        especialidad: 'Medicina General'
      },
      {
        username: 'anamaria',
        password: await bcrypt.hash('ana123', 12),
        nombre: 'Dra. Ana María Rodríguez',
        email: 'anamariar@yo-soy.co',
        rol: 'medico',
        especialidad: 'Cardiología'
      }
    ];

    for (const userData of usuarios) {
      const existingUser = await Usuario.findOne({ where: { username: userData.username } });
      if (!existingUser) {
        await Usuario.create(userData);
        console.log(`✅ Usuario ${userData.username} creado`);
      }
    }

  } catch (error) {
    console.error('❌ Error inicializando base de datos:', error);
    process.exit(1);
  }
};

// Iniciar servidor
const startServer = async () => {
  await initializeDatabase();
  
  app.listen(PORT, '0.0.0.0', () => {
    console.log(`🚀 YoSoy Historia Clínica API iniciado en puerto ${PORT}`);
    console.log(`🔒 Modo: ${process.env.NODE_ENV || 'development'}`);
    console.log(`🏥 Sistema médico con base de datos persistente`);
    console.log(`📡 CORS habilitado para: ${corsOptions.origin}`);
  });
};

// Manejo graceful de shutdown
process.on('SIGTERM', async () => {
  console.log('🔄 Cerrando servidor...');
  await sequelize.close();
  process.exit(0);
});

process.on('SIGINT', async () => {
  console.log('🔄 Cerrando servidor...');
  await sequelize.close();
  process.exit(0);
});

startServer().catch(error => {
  console.error('❌ Error iniciando servidor:', error);
  process.exit(1);
});

module.exports = app;