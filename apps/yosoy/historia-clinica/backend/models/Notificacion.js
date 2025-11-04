const { DataTypes } = require('sequelize');

module.exports = (sequelize) => {
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
      allowNull: true,
      comment: 'ID del recurso relacionado (cita, paciente, etc.)'
    },
    tipoRecurso: {
      type: DataTypes.STRING(50),
      allowNull: true,
      comment: 'Tipo de recurso relacionado (cita, paciente, historia_clinica, etc.)'
    },
    metadatos: {
      type: DataTypes.JSON,
      allowNull: true,
      comment: 'Información adicional para la notificación'
    },
    esRecurrente: {
      type: DataTypes.BOOLEAN,
      defaultValue: false
    },
    configuracionRecurrencia: {
      type: DataTypes.JSON,
      allowNull: true,
      comment: 'Configuración para notificaciones recurrentes'
    },
    canalEnvio: {
      type: DataTypes.JSON,
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
    timestamps: true,
    indexes: [
      {
        fields: ['usuarioId']
      },
      {
        fields: ['tipo']
      },
      {
        fields: ['estado']
      },
      {
        fields: ['prioridad']
      },
      {
        fields: ['fechaProgramada']
      }
    ]
  });

  Notificacion.associate = (models) => {
    Notificacion.belongsTo(models.Usuario, {
      foreignKey: 'usuarioId',
      as: 'usuario'
    });
  };

  return Notificacion;
};