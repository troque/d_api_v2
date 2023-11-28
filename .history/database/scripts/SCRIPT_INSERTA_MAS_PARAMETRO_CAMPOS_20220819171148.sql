SET DEFINE OFF;

DELETE FROM MAS_PARAMETRO_CAMPOS;
COMMIT;

/* ACTUACIONES PLIEGO DE CARGOS */
INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('1', 'Antecedentes', null, null, null, null, null, 'Antecedentes', null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('2', 'Dependencia', null, null, null, null, null, null, null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('3', 'Investigados (Nombre)', null, null, null, null, null, 'Investigados', null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('4', 'Investigados (Cargo)', null, null, null, null, null, 'CargosInvestigados', null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('5', 'Investigados (Entidad)', null, null, null, null, null, 'EntidadesInvestigados', null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('6', 'Interesados', null, null, null, null, null, 'Interesados', null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('7', 'Fecha de ingreso', null, null, null, null, null, null, null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('8', 'Fecha de registro', null, null, null, null, null, null, null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('9', 'Número de auto (generado despues de aprobación)', null, null, null, null, null, null, '${numero_de_auto}', 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('10', 'Número de radicado', null, null, null, null, null, null, null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('11', 'Dependencia Origen', null, null, null, null, null, 'Dependencia', null, 1);

INSERT INTO "MAS_PARAMETRO_CAMPOS" (ID, NOMBRE_CAMPO, CREATED_USER, UPDATED_USER, DELETED_USER, CREATED_AT, UPDATED_AT, TYPE, VALUE, ESTADO) values
('12', 'Delegada', null, null, null, null, null, 'Dependencia', null, 1);

alter sequence MAS_PARAMETRO_CAMPOS_ID_SEQ restart start with 12;

COMMIT;
