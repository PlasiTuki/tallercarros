CREATE DATABASE IF NOT EXISTS tallercarros;
USE tallercarros;

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100)
);

CREATE TABLE vehiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    anio INT,
    placa VARCHAR(20) UNIQUE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2)
);

CREATE TABLE repuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2),
    stock INT DEFAULT 0
);

CREATE TABLE reparaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehiculo_id INT,
    fecha_ingreso DATE,
    fecha_salida DATE,
    estado ENUM('En progreso', 'Completado', 'Cancelado') DEFAULT 'En progreso',
    descripcion TEXT,
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id)
);

CREATE TABLE reparaciones_servicios (
    reparacion_id INT,
    servicio_id INT,
    FOREIGN KEY (reparacion_id) REFERENCES reparaciones(id),
    FOREIGN KEY (servicio_id) REFERENCES servicios(id),
    PRIMARY KEY (reparacion_id, servicio_id)
);

CREATE TABLE reparaciones_repuestos (
    reparacion_id INT,
    repuesto_id INT,
    cantidad INT,
    FOREIGN KEY (reparacion_id) REFERENCES reparaciones(id),
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id),
    PRIMARY KEY (reparacion_id, repuesto_id)
);

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    vehiculo_id INT,
    fecha DATETIME,
    motivo TEXT,
    estado ENUM('Programada', 'Completada', 'Cancelada') DEFAULT 'Programada',
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id)
);

CREATE TABLE alertas_mantenimiento (
 id INT AUTO_INCREMENT PRIMARY KEY,
 vehiculo_id INT, fecha_alerta DATETIME, 
 FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id) );
 
CREATE TABLE IF NOT EXISTS historial_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT,
    nombre VARCHAR(100),
    descripcion TEXT,
    precio DECIMAL(10, 2),
    fecha DATE,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id)
);



