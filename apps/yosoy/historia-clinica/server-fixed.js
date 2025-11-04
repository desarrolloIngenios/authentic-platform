const express = require('express');
const cors = require('cors');
const sqlite3 = require('sqlite3').verbose();
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { v4: uuidv4 } = require('uuid');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3001;
const JWT_SECRET = process.env.JWT_SECRET || 'yosoy-historia-clinica-jwt-secret';
const DB_PATH = process.env.DB_PATH || path.join(__dirname, 'data', 'medical.db');

// Middleware
app.use(cors({
    origin: process.env.CORS_ORIGIN || ['http://localhost:3000', 'https://hc.yo-soy.co'],
    credentials: true
}));
app.use(express.json());

// Inicializar base de datos
const db = new sqlite3.Database(DB_PATH, (err) => {
    if (err) {
        console.error('Error al conectar con la base de datos:', err);
    } else {
        console.log('Conectado a la base de datos SQLite');
        initDatabase();
    }
});

// Crear tablas con esquema corregido
function initDatabase() {
    const queries = [
        `CREATE TABLE IF NOT EXISTS usuarios (
            id TEXT PRIMARY KEY,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            nombre TEXT NOT NULL,
            especialidad TEXT,
            registro TEXT,
            email TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )`,
        `CREATE TABLE IF NOT EXISTS pacientes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombres TEXT NOT NULL,
            apellidos TEXT NOT NULL,
            documento TEXT UNIQUE NOT NULL,
            fecha_nacimiento DATE NOT NULL,
            telefono TEXT,
            email TEXT,
            direccion TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )`,
        `CREATE TABLE IF NOT EXISTS historias_clinicas (
            id TEXT PRIMARY KEY,
            paciente_id INTEGER NOT NULL,
            motivo_consulta TEXT NOT NULL,
            antecedentes TEXT,
            examen_fisico TEXT,
            diagnostico TEXT NOT NULL,
            tratamiento TEXT,
            observaciones TEXT,
            fecha_consulta DATE DEFAULT CURRENT_DATE,
            medico_id TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (paciente_id) REFERENCES pacientes (id),
            FOREIGN KEY (medico_id) REFERENCES usuarios (id)
        )`,
        `CREATE TABLE IF NOT EXISTS formulas_medicas (
            id TEXT PRIMARY KEY,
            paciente_id INTEGER NOT NULL,
            medicamentos TEXT NOT NULL,
            indicaciones TEXT,
            duracion TEXT,
            fecha_emision DATE DEFAULT CURRENT_DATE,
            medico_id TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (paciente_id) REFERENCES pacientes (id),
            FOREIGN KEY (medico_id) REFERENCES usuarios (id)
        )`
    ];

    queries.forEach((query) => {
        db.run(query, (err) => {
            if (err) {
                console.error('Error al crear tabla:', err);
            }
        });
    });

    // Crear datos de prueba
    createTestData();
}

// Crear datos de prueba
function createTestData() {
    console.log('Creando datos de prueba...');
    
    // Crear usuario de prueba
    const testUser = {
        id: 'user-001',
        username: 'admin',
        password: '123456', // Contraseña simple para debug
        nombre: 'Dr. Admin',
        especialidad: 'Medicina General',
        registro: 'REG-001',
        email: 'admin@yosoy.co'
    };

    // Verificar si el usuario ya existe
    db.get('SELECT id FROM usuarios WHERE username = ?', [testUser.username], (err, row) => {
        if (err) {
            console.error('Error verificando usuario:', err);
            return;
        }

        if (!row) {
            // Hashear la contraseña
            bcrypt.hash(testUser.password, 10, (err, hashedPassword) => {
                if (err) {
                    console.error('Error hasheando contraseña:', err);
                    return;
                }

                db.run(
                    'INSERT INTO usuarios (id, username, password, nombre, especialidad, registro, email) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [testUser.id, testUser.username, hashedPassword, testUser.nombre, testUser.especialidad, testUser.registro, testUser.email],
                    (err) => {
                        if (err) {
                            console.error('Error creando usuario de prueba:', err);
                        } else {
                            console.log('Usuario de prueba creado:', testUser.username);
                            createTestPatients();
                        }
                    }
                );
            });
        } else {
            console.log('Usuario de prueba ya existe');
            createTestPatients();
        }
    });
}

function createTestPatients() {
    const testPatients = [
        {
            nombres: 'María',
            apellidos: 'González',
            documento: '12345678',
            fecha_nacimiento: '1990-01-15',
            telefono: '300-123-4567',
            email: 'maria.gonzalez@email.com',
            direccion: 'Calle 123 #45-67'
        },
        {
            nombres: 'Ana',
            apellidos: 'Rodríguez',
            documento: '87654321',
            fecha_nacimiento: '1985-05-20',
            telefono: '300-987-6543',
            email: 'ana.rodriguez@email.com',
            direccion: 'Carrera 78 #90-12'
        },
        {
            nombres: 'Carmen',
            apellidos: 'López',
            documento: '11223344',
            fecha_nacimiento: '1992-08-10',
            telefono: '300-456-7890',
            email: 'carmen.lopez@email.com',
            direccion: 'Avenida 56 #34-78'
        },
        {
            nombres: 'Laura',
            apellidos: 'Martínez',
            documento: '44332211',
            fecha_nacimiento: '1988-12-03',
            telefono: '300-654-3210',
            email: 'laura.martinez@email.com',
            direccion: 'Calle 89 #12-34'
        }
    ];

    testPatients.forEach((patient, index) => {
        db.get('SELECT id FROM pacientes WHERE documento = ?', [patient.documento], (err, row) => {
            if (err) {
                console.error('Error verificando paciente:', err);
                return;
            }

            if (!row) {
                db.run(
                    'INSERT INTO pacientes (nombres, apellidos, documento, fecha_nacimiento, telefono, email, direccion) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [patient.nombres, patient.apellidos, patient.documento, patient.fecha_nacimiento, patient.telefono, patient.email, patient.direccion],
                    function(err) {
                        if (err) {
                            console.error('Error creando paciente de prueba:', err);
                        } else {
                            console.log(`Paciente de prueba creada: ${patient.nombres} ${patient.apellidos}`);
                            
                            // Crear historias clínicas de prueba
                            if (index < 2) { // Solo para las primeras 2 pacientes
                                createTestHistorias(this.lastID, index + 1);
                            }
                        }
                    }
                );
            } else {
                console.log(`Paciente ya existe: ${patient.nombres} ${patient.apellidos}`);
            }
        });
    });
}

function createTestHistorias(pacienteId, historiaNum) {
    const testHistorias = [
        {
            id: `hist00${historiaNum}`,
            motivo_consulta: 'Control prenatal',
            antecedentes: 'Sin antecedentes relevantes',
            examen_fisico: 'Paciente en buen estado general',
            diagnostico: 'Embarazo de 20 semanas',
            tratamiento: 'Vitaminas prenatales',
            observaciones: 'Próxima cita en 4 semanas',
            fecha_consulta: '2024-10-15',
            medico_id: 'user-001'
        },
        {
            id: `hist00${historiaNum + 2}`,
            motivo_consulta: 'Consulta ginecológica',
            antecedentes: 'Últimas menstruación hace 2 meses',
            examen_fisico: 'Abdomen suave, no doloroso',
            diagnostico: 'Amenorrea secundaria',
            tratamiento: 'Exámenes de laboratorio',
            observaciones: 'Pendiente resultados de laboratorio',
            fecha_consulta: '2024-09-20',
            medico_id: 'user-001'
        }
    ];

    testHistorias.forEach(historia => {
        db.get('SELECT id FROM historias_clinicas WHERE id = ?', [historia.id], (err, row) => {
            if (err) {
                console.error('Error verificando historia:', err);
                return;
            }

            if (!row) {
                db.run(
                    'INSERT INTO historias_clinicas (id, paciente_id, motivo_consulta, antecedentes, examen_fisico, diagnostico, tratamiento, observaciones, fecha_consulta, medico_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [historia.id, pacienteId, historia.motivo_consulta, historia.antecedentes, historia.examen_fisico, historia.diagnostico, historia.tratamiento, historia.observaciones, historia.fecha_consulta, historia.medico_id],
                    (err) => {
                        if (err) {
                            console.error('Error creando historia de prueba:', err);
                        } else {
                            console.log(`Historia clínica creada: ${historia.id}`);
                        }
                    }
                );
            }
        });
    });
}

// Middleware de autenticación
function authenticateToken(req, res, next) {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1];

    if (!token) {
        return res.status(401).json({ error: 'Token de acceso requerido' });
    }

    jwt.verify(token, JWT_SECRET, (err, user) => {
        if (err) {
            return res.status(403).json({ error: 'Token inválido' });
        }
        req.user = user;
        next();
    });
}

// Rutas de autenticación
app.post('/api/auth/login', (req, res) => {
    const { username, password } = req.body;

    console.log('=== LOGIN ATTEMPT ===');
    console.log('Username:', username);
    console.log('Password provided:', password);

    if (!username || !password) {
        return res.status(400).json({ error: 'Usuario y contraseña requeridos' });
    }

    db.get('SELECT * FROM usuarios WHERE username = ?', [username], (err, user) => {
        if (err) {
            console.error('Error al buscar usuario:', err);
            return res.status(500).json({ error: 'Error interno del servidor' });
        }

        if (!user) {
            console.log('Usuario no encontrado');
            return res.status(401).json({ error: 'Credenciales inválidas' });
        }

        console.log('Usuario encontrado:', user.username);

        bcrypt.compare(password, user.password, (err, isMatch) => {
            if (err) {
                console.error('Error al verificar contraseña:', err);
                return res.status(500).json({ error: 'Error interno del servidor' });
            }

            console.log('Contraseña coincide:', isMatch);

            if (!isMatch) {
                return res.status(401).json({ error: 'Credenciales inválidas' });
            }

            const token = jwt.sign(
                { 
                    id: user.id, 
                    username: user.username, 
                    nombre: user.nombre,
                    especialidad: user.especialidad,
                    registro: user.registro
                },
                JWT_SECRET,
                { expiresIn: '24h' }
            );

            console.log('Login exitoso, token generado');

            res.json({
                token,
                user: {
                    id: user.id,
                    username: user.username,
                    nombre: user.nombre,
                    especialidad: user.especialidad,
                    registro: user.registro,
                    email: user.email
                }
            });
        });
    });
});

// Ruta para verificar token
app.get('/api/auth/verify', authenticateToken, (req, res) => {
    res.json(req.user);
});

// Rutas de pacientes
app.get('/api/patients', authenticateToken, (req, res) => {
    console.log('=== OBTENIENDO PACIENTES ===');
    
    db.all('SELECT * FROM pacientes ORDER BY apellidos, nombres', (err, rows) => {
        if (err) {
            console.error('Error al obtener pacientes:', err);
            return res.status(500).json({ error: 'Error interno del servidor' });
        }
        
        console.log(`Pacientes encontradas: ${rows.length}`);
        res.json(rows);
    });
});

app.get('/api/patients/:id', authenticateToken, (req, res) => {
    const { id } = req.params;
    
    db.get('SELECT * FROM pacientes WHERE id = ?', [id], (err, row) => {
        if (err) {
            console.error('Error al obtener paciente:', err);
            return res.status(500).json({ error: 'Error interno del servidor' });
        }
        
        if (!row) {
            return res.status(404).json({ error: 'Paciente no encontrada' });
        }
        
        res.json(row);
    });
});

// Ruta corregida para historias clínicas por paciente
app.get('/api/patients/:id/historias', authenticateToken, (req, res) => {
    const { id } = req.params;
    
    console.log('=== OBTENIENDO HISTORIAS DE PACIENTE ===');
    console.log('ID de paciente:', id);
    
    // Consulta corregida usando paciente_id en lugar de pacienteId
    const query = `
        SELECT h.*, u.nombre as medicoNombre 
        FROM historias_clinicas h 
        JOIN usuarios u ON h.medico_id = u.id 
        WHERE h.paciente_id = ? 
        ORDER BY h.fecha_consulta DESC, h.created_at DESC
    `;
    
    console.log('Ejecutando consulta:', query);
    console.log('Parámetros:', [id]);
    
    db.all(query, [id], (err, rows) => {
        if (err) {
            console.error('Error al obtener historias de paciente:', err);
            return res.status(500).json({ error: 'Error interno del servidor' });
        }
        
        console.log(`Historias encontradas: ${rows.length}`);
        console.log('Historias:', rows);
        
        res.json(rows);
    });
});

// Rutas de historias clínicas
app.get('/api/historias', authenticateToken, (req, res) => {
    const query = `
        SELECT h.*, p.nombres, p.apellidos, p.documento 
        FROM historias_clinicas h 
        JOIN pacientes p ON h.paciente_id = p.id 
        ORDER BY h.created_at DESC
    `;
    
    db.all(query, (err, rows) => {
        if (err) {
            console.error('Error al obtener historias clínicas:', err);
            return res.status(500).json({ error: 'Error interno del servidor' });
        }
        res.json(rows);
    });
});

app.post('/api/historias', authenticateToken, (req, res) => {
    const { 
        paciente_id, motivo_consulta, antecedentes, examen_fisico, 
        diagnostico, tratamiento, observaciones 
    } = req.body;

    const historiaId = `hist-${Date.now()}`;

    db.run(
        `INSERT INTO historias_clinicas (
            id, paciente_id, motivo_consulta, antecedentes, examen_fisico,
            diagnostico, tratamiento, observaciones, medico_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [
            historiaId, paciente_id, motivo_consulta, antecedentes, examen_fisico,
            diagnostico, tratamiento, observaciones, req.user.id
        ],
        function(err) {
            if (err) {
                console.error('Error al crear historia clínica:', err);
                return res.status(500).json({ error: 'Error interno del servidor' });
            }
            res.status(201).json({ 
                id: historiaId, 
                message: 'Historia clínica creada exitosamente' 
            });
        }
    );
});

// Ruta de salud
app.get('/api/health', (req, res) => {
    res.json({ 
        status: 'OK', 
        timestamp: new Date().toISOString(),
        version: '1.0.0-fixed',
        database: 'Connected'
    });
});

// Manejar errores
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: 'Algo salió mal!' });
});

// Iniciar servidor
app.listen(PORT, '0.0.0.0', () => {
    console.log(`Servidor backend ejecutándose en puerto ${PORT}`);
    console.log(`Entorno: ${process.env.NODE_ENV || 'development'}`);
    console.log(`Base de datos: ${DB_PATH}`);
});

// Manejar cierre graceful
process.on('SIGINT', () => {
    console.log('Cerrando servidor...');
    db.close((err) => {
        if (err) {
            console.error('Error al cerrar base de datos:', err);
        } else {
            console.log('Base de datos cerrada.');
        }
        process.exit(0);
    });
});

module.exports = app;