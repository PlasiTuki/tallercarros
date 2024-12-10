-- Stored Procedure para registro de nuevos vehículos
DELIMITER //
CREATE PROCEDURE registrar_vehiculo(
    IN p_cliente_id INT,
    IN p_marca VARCHAR(50),
    IN p_modelo VARCHAR(50),
    IN p_anio INT,
    IN p_placa VARCHAR(20)
)
BEGIN
    INSERT INTO vehiculos (cliente_id, marca, modelo, anio, placa)
    VALUES (p_cliente_id, p_marca, p_modelo, p_anio, p_placa);
END //
DELIMITER ;

-- Stored Procedure para programación de servicios
DELIMITER //
CREATE PROCEDURE programar_servicio(
    IN p_cliente_id INT,
    IN p_vehiculo_id INT,
    IN p_fecha DATETIME,
    IN p_motivo TEXT
)
BEGIN
    INSERT INTO citas (cliente_id, vehiculo_id, fecha, motivo)
    VALUES (p_cliente_id, p_vehiculo_id, p_fecha, p_motivo);
END //
DELIMITER ;

-- Stored Procedure para control de reparaciones
DELIMITER //
CREATE PROCEDURE iniciar_reparacion(
    IN p_vehiculo_id INT,
    IN p_descripcion TEXT
)
BEGIN
    INSERT INTO reparaciones (vehiculo_id, fecha_ingreso, descripcion)
    VALUES (p_vehiculo_id, CURDATE(), p_descripcion);
END //
DELIMITER ;

-- Stored Procedure para gestión de garantías
DELIMITER //
CREATE PROCEDURE aplicar_garantia(
    IN p_reparacion_id INT
)
BEGIN
    UPDATE reparaciones
    SET estado = 'En progreso', fecha_ingreso = CURDATE(), fecha_salida = NULL
    WHERE id = p_reparacion_id AND fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH);
END //
DELIMITER ;