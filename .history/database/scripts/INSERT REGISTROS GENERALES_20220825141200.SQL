
INSERT INTO MAS_ESTADO_PROCESO_DISCIPLINARIO (ID, NOMBRE, ESTADO) VALUES (1, 'Activo', 1);
INSERT INTO MAS_ESTADO_PROCESO_DISCIPLINARIO (ID, NOMBRE, ESTADO) VALUES (2, 'Cerrado', 1);
INSERT INTO MAS_ESTADO_PROCESO_DISCIPLINARIO (ID, NOMBRE, ESTADO) VALUES (3, 'Archivado', 1);

COMMIT;

/*INSERTA QUEJA INTERNA*/
INSERT INTO MAS_DEPENDENCIA_ACCESO (ID, NOMBRE, ESTADO) VALUES ('9', 'Queja interna', '1');
COMMIT;

UPDATE mas_dependencia_origen set codigo_homologado = id;
COMMIT;

UPDATE users u SET u.numero_casos = 0;
COMMIT;

alter sequence ROLES_ID_SEQ restart start with 7;
COMMIT;


UPDATE mas_dependencia_origen set codigo_homologado = id;
COMMIT;
