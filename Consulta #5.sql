SET GLOBAL event_scheduler=ON

SELECT * FROM historial_servicios

DROP TRIGGER IF EXISTS registrar_historial_servicios;

ALTER TABLE alertas_mantenimiento DROP FOREIGN KEY alertas_mantenimiento_ibfk_1; 
ALTER TABLE alertas_mantenimiento ADD CONSTRAINT alertas_mantenimiento_ibfk_1 
FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id) ON DELETE CASCADE;

ALTER TABLE alertas_mantenimiento ADD COLUMN marca VARCHAR(50); ALTER TABLE alertas_mantenimiento ADD COLUMN modelo VARCHAR(50);

ALTER TABLE historial_servicios
DROP FOREIGN KEY historial_servicios_ibfk_1;

SELECT * FROM historial_servicios WHERE servicio_id IN (SELECT id FROM servicios);
SET FOREIGN_KEY_CHECKS = 0;
SET FOREIGN_KEY_CHECKS = 1;
ALTER TABLE historial_servicios
ADD CONSTRAINT historial_servicios_ibfk_1
FOREIGN KEY (servicio_id) REFERENCES servicios(id)
ON DELETE CASCADE
ON UPDATE CASCADE;
