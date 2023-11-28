SELECT
pd.radicado radicado,
pd.uuid AS id,
pd.vigencia AS vigencia,
pd.created_at AS fecha,
UPPER(mepd.nombre) AS estado,
UPPER(mdo.nombre) AS dependencia,
UPPER(me.nombre) AS etapa,
UPPER(a.descripcion) AS descripcion,
UPPER(ei.nombre_investigado) AS nombre_investigado,
UPPER(ei.cargo) AS cargo_investigado,
UPPER(i.nombre) AS tipo_quejoso,
UPPER(CONCAT(CONCAT(i.primer_nombre, ' '), i.primer_apellido)) AS nombre_quejoso,
i.numero_documento as numero_documento,
UPPER(CONCAT(CONCAT(u.nombre, ' '), u.apellido)) AS funcionario_actual,
UPPER(ev.nombre) AS evaluacion,
UPPER(tcpd.nombre) AS tipo_de_conducta,
act.auto
FROM proceso_disciplinario pd
INNER JOIN log_proceso_disciplinario lpd on lpd.id_proceso_disciplinario = pd.uuid
OUTER APPLY (SELECT a.descripcion FROM antecedente a WHERE a.id_proceso_disciplinario = pd.uuid) a
INNER JOIN mas_etapa me on me.id = pd.id_etapa
INNER JOIN mas_dependencia_origen mdo on mdo.id = pd.id_dependencia
INNER JOIN mas_estado_proceso_disciplinario mepd on mepd.id = pd.estado
OUTER APPLY (SELECT u.nombre, u.apellido FROM users u WHERE u.name = lpd.id_funcionario_actual) u
OUTER APPLY (SELECT ei.nombre_investigado FROM entidad_investigado ei WHERE ei.id_proceso_disciplinario = pd.uuid) ei
OUTER APPLY (SELECT i.primer_nombre, i.segundo_nombre, i.primer_apellido, i.segundo_apellido, mti.nombre FROM interesado i INNER JOIN mas_tipo_interesado mti on mti.id = i.id_tipo_interesao WHERE i.id_proceso_disciplinario = pd.uuid) i
OUTER APPLY (SELECT ev.tipo_conducta, mre.nombre FROM evaluacion ev INNER JOIN mas_resultado_evaluacion mre on mre.id = ev.resultado_evaluacion WHERE ev.id_proceso_disciplinario = pd.uuid AND (ev.estado = 2 or ev.estado = 3)) ev
OUTER APPLY (SELECT mtc.nombre FROM tipo_conducta_proceso_disciplinario tcpd INNER JOIN mas_tipo_conducta mtc on mtc.id = tcpd.id_tipo_conducta WHERE tcpd.id_proceso_disciplinario = pd.uuid) tcpd
OUTER APPLY (SELECT act.auto FROM actuaciones act WHERE act.uuid_proceso_disciplinario = pd.uuid) act
WHERE
pd.radicado LIKE '%%'
AND pd.vigencia LIKE '%%'
AND mepd.id LIKE '%%'
AND mdo.id LIKE '%%'
AND me.id LIKE '%%'
AND UPPER(TRANSLATE(a.descripcion, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou'))
AND (ei.nombre_investigado IS NULL OR UPPER(TRANSLATE(ei.nombre_investigado, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
AND (ei.cargo IS NULL OR UPPER(TRANSLATE(ei.cargo, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
AND UPPER(TRANSLATE(i.primer_nombre, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou'))
AND UPPER(TRANSLATE(i.segundo_nombre, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou'))
AND UPPER(TRANSLATE(i.primer_apellido, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou'))
AND UPPER(TRANSLATE(i.segundo_apellido, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou'))
AND UPPER(TRANSLATE(i.numero_documento, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou'))
AND (tcpd.nombre IS NULL OR UPPER(TRANSLATE(tcpd.nombre, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
AND (act.auto IS NULL OR UPPER(TRANSLATE(act.auto, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
ORDER BY lpd.created_at DESC, a.created_at DESC, ei.created_at DESC, i.created_at DESC, ev.created_at DESC, tcpd.created_at DESC, act.created_at DESC
