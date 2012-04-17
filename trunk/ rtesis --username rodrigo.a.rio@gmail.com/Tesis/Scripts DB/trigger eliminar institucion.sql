CREATE
    TRIGGER eliminar_institucion BEFORE DELETE ON instituciones 
    FOR EACH ROW BEGIN
    UPDATE personas
    SET personas.`instituciones_id` = NULL
    WHERE `id` = personas.`instituciones_id`;
    END;