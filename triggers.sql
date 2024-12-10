-- Trigger para actualizar stock de repuestos
DELIMITER //
CREATE TRIGGER actualizar_stock_repuestos
AFTER INSERT ON reparaciones_repuestos
FOR EACH ROW
BEGIN
    UPDATE repuestos
    SET stock = stock - NEW.cantidad
    WHERE id = NEW.repuesto_id;
END //
DELIMITER ;

-- Trigger para registrar historial de servicios
DELIMITER //
CREATE TRIGGER registrar_historial_servicios
AFTER INSERT ON servicios
FOR EACH ROW
BEGIN
    INSERT INTO historial_servicios (servicio_id, nombre, descripcion, precio, fecha)
    VALUES (NEW.id, NEW.nombre, NEW.descripcion, NEW.precio, CURDATE());
END //
DELIMITER ;


-- Trigger para alertar sobre mantenimientos programados
DELIMITER //
CREATE TRIGGER alertar_mantenimientos_programados
AFTER UPDATE ON reparaciones
FOR EACH ROW
BEGIN
    IF NEW.estado = 'En progreso' THEN
        -- Eliminar cualquier alerta existente para evitar duplicados
        DELETE FROM alertas_mantenimiento WHERE vehiculo_id = NEW.vehiculo_id;
        -- Insertar nueva alerta con la fecha de salida
        INSERT INTO alertas_mantenimiento (vehiculo_id, marca, modelo, fecha_alerta)
        VALUES (
            NEW.vehiculo_id,
            (SELECT v.marca FROM vehiculos v WHERE v.id = NEW.vehiculo_id),
            (SELECT v.modelo FROM vehiculos v WHERE v.id = NEW.vehiculo_id),
            NEW.fecha_salida
        );
    END IF;
END //
DELIMITER ;


DELIMITER //
CREATE TRIGGER eliminar_alertas_mantenimiento
AFTER DELETE ON reparaciones
FOR EACH ROW
BEGIN
    DELETE FROM alertas_mantenimiento WHERE vehiculo_id = OLD.vehiculo_id;
END //
DELIMITER ;



