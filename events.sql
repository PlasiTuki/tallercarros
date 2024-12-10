-- Evento para recordatorios de mantenimiento
DELIMITER //
CREATE EVENT recordatorios_mantenimiento
ON SCHEDULE EVERY 30 SECOND
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    INSERT INTO notificaciones (cliente_id, mensaje)
    SELECT c.id, CONCAT('Recordatorio: Su vehículo necesita mantenimiento el ', DATE_FORMAT(am.fecha_alerta, '%d/%m/%Y'))
    FROM alertas_mantenimiento am
    JOIN vehiculos v ON am.vehiculo_id = v.id
    JOIN clientes c ON v.cliente_id = c.id
    WHERE am.fecha_alerta = DATE_ADD(CURDATE(), INTERVAL 7 DAY);
END //
DELIMITER ;

-- Evento para actualización de estado de reparaciones
DELIMITER //
CREATE EVENT actualizar_estado_reparaciones
ON SCHEDULE EVERY 1 MINUTE
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE reparaciones
    SET estado = 'Completado'
    WHERE fecha_salida <= CURDATE() AND estado = 'En progreso';
END //
DELIMITER ;

-- Evento para reporte diario de servicios realizados
DELIMITER //
CREATE EVENT reporte_diario_servicios
ON SCHEDULE EVERY 1 MINUTE
STARTS CURRENT_TIMESTAMP + INTERVAL 1 MINUTE
DO
BEGIN
    INSERT INTO reportes_diarios (fecha, total_servicios, total_ingresos)
    SELECT 
        CURDATE() - INTERVAL 1 MINUTE,
        COUNT(*),
        SUM(s.precio)
    FROM reparaciones_servicios rs
    JOIN servicios s ON rs.servicio_id = s.id
    JOIN reparaciones r ON rs.reparacion_id = r.id
    WHERE DATE(r.fecha_salida) = CURDATE() - INTERVAL 1 DAY;
END //
DELIMITER ;