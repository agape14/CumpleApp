# 📖 Ejemplos de Uso - CumpleApp

Esta guía te mostrará cómo usar CumpleApp con ejemplos prácticos.

## 🎯 Casos de Uso Comunes

### 1. Agregar tu primer familiar

**Escenario**: Quieres agregar a tu mamá al sistema.

**Pasos**:
1. Ve a "Familiares" en el menú
2. Haz clic en "Agregar Familiar"
3. Completa el formulario:
   - **Nombre**: María García
   - **Parentesco**: Madre
   - **Fecha de Nacimiento**: 15/05/1965
   - **Teléfono**: +52 123 456 7890
   - **Email**: maria.garcia@email.com
   - **Notificar**: ✓ (activado)
   - **Notas**: Le encantan las flores y la jardinería
4. Haz clic en "Guardar Familiar"

**Resultado**: El sistema calculará automáticamente su edad (59 años) y su signo zodiacal (Tauro).

---

### 2. Agregar ideas de regalos

**Escenario**: Quieres guardar ideas de regalos para tu hermano.

**Pasos**:
1. Ve a "Familiares"
2. Haz clic en el botón "Ver" (ícono de ojo) de tu hermano
3. En la sección "Ideas de Regalos", haz clic en "Agregar"
4. Completa el formulario:
   - **Idea de Regalo**: Auriculares inalámbricos
   - **Precio Estimado**: 1500.00
   - **Link de Compra**: https://amazon.com.mx/auriculares
5. Haz clic en "Guardar Idea"

**Resultado**: La idea se guardará y podrás marcarla como comprada cuando la adquieras.

---

### 3. Ver próximos cumpleaños

**Escenario**: Quieres saber quién cumple años próximamente.

**Pasos**:
1. Ve al Dashboard (página principal)
2. Busca la sección "Próximo Cumpleaños"
3. Revisa la tarjeta "Próximos Cumpleaños"

**Resultado**: Verás una lista de los próximos 5 cumpleaños ordenados por fecha.

---

### 4. Contactar rápidamente a un familiar

**Escenario**: Es el cumpleaños de tu tía y quieres llamarla.

**Pasos**:
1. Ve a "Familiares"
2. Busca a tu tía en la lista
3. Haz clic en el botón "Ver" (ícono de ojo)
4. En la sección de información, haz clic en "Llamar"

**Resultado**: Se abrirá tu aplicación de teléfono con el número ya marcado.

---

### 5. Marcar un regalo como comprado

**Escenario**: Ya compraste un regalo que tenías en la lista.

**Pasos**:
1. Ve al perfil del familiar
2. En "Ideas de Regalos", encuentra el regalo que compraste
3. Haz clic en el botón verde con ícono de check (✓)

**Resultado**: El regalo se marcará como comprado y aparecerá tachado.

---

### 6. Editar información de un familiar

**Escenario**: Tu primo cambió de número de teléfono.

**Pasos**:
1. Ve a "Familiares"
2. Busca a tu primo
3. Haz clic en el botón "Editar" (ícono de lápiz)
4. Actualiza el número de teléfono
5. Haz clic en "Actualizar Familiar"

**Resultado**: La información se actualizará en el sistema.

---

### 7. Desactivar notificaciones para un familiar

**Escenario**: No quieres recibir notificaciones del cumpleaños de un amigo.

**Pasos**:
1. Ve a "Familiares"
2. Haz clic en "Editar" del familiar
3. Desmarca la casilla "Recibir notificaciones de cumpleaños"
4. Guarda los cambios

**Resultado**: No recibirás emails de recordatorio para ese cumpleaños.

---

### 8. Ver estadísticas de cumpleaños

**Escenario**: Quieres saber en qué mes hay más cumpleaños.

**Pasos**:
1. Ve al Dashboard
2. Revisa el gráfico de barras "Distribución de Cumpleaños por Mes"

**Resultado**: Verás un gráfico que muestra cuántos cumpleaños hay cada mes.

---

### 9. Eliminar una idea de regalo

**Escenario**: Ya no quieres regalarle eso a tu hermana.

**Pasos**:
1. Ve al perfil de tu hermana
2. En "Ideas de Regalos", encuentra la idea que quieres eliminar
3. Haz clic en el botón rojo con ícono de basura
4. Confirma la eliminación

**Resultado**: La idea se eliminará de la lista.

---

### 10. Probar el sistema de notificaciones

**Escenario**: Quieres verificar que los emails funcionan.

**Pasos**:
1. Abre una terminal en la carpeta del proyecto
2. Ejecuta: `php artisan birthdays:send-reminders`
3. Revisa tu email

**Resultado**: Si hay cumpleaños hoy, recibirás un email de recordatorio.

---

## 💡 Consejos y Trucos

### Organización de Familiares

**Tip 1: Usa las notas**
Aprovecha el campo de notas para guardar información útil:
- Gustos y preferencias
- Tallas de ropa
- Colores favoritos
- Hobbies
- Alergias

Ejemplo:
```
Le encanta leer novelas de misterio.
Talla: M
Color favorito: Azul
Alérgico a los mariscos
```

---

**Tip 2: Agrupa por tipo de parentesco**
La vista de lista te permite ver todos tus familiares agrupados. Usa parentescos consistentes para facilitar la búsqueda.

---

### Gestión de Ideas de Regalos

**Tip 3: Agrega ideas durante todo el año**
No esperes a que se acerque el cumpleaños. Cuando alguien mencione algo que le gusta, agrégalo inmediatamente.

---

**Tip 4: Usa los links de compra**
Guarda el link directo del producto. Así cuando llegue el cumpleaños, solo haces clic y compras.

---

**Tip 5: Establece presupuestos**
Usa el campo de precio estimado para planificar tu presupuesto de regalos del año.

---

### Notificaciones

**Tip 6: Configura un email específico**
Si compartes la app con tu familia, configura un email compartido para recibir todos los recordatorios.

---

**Tip 7: Verifica el scheduler regularmente**
En la primera semana de uso, revisa que el scheduler funcione correctamente:

```bash
# Prueba manual
php artisan birthdays:send-reminders

# Verifica los logs
tail -f storage/logs/laravel.log
```

---

## 🎨 Personalizaciones Sugeridas

### Agregar más parentescos

Si necesitas agregar más tipos de parentesco:

1. Ve a phpMyAdmin o tu cliente MySQL
2. Abre la tabla `parentescos`
3. Inserta nuevos registros:

```sql
INSERT INTO parentescos (nombre_parentesco, created_at, updated_at)
VALUES ('Padrino', NOW(), NOW());
```

O desde la línea de comandos:

```bash
php artisan tinker

>>> App\Models\Parentesco::create(['nombre_parentesco' => 'Padrino']);
>>> App\Models\Parentesco::create(['nombre_parentesco' => 'Madrina']);
>>> App\Models\Parentesco::create(['nombre_parentesco' => 'Compañero de trabajo']);
```

---

## 📊 Reportes Útiles

### Cumpleaños del mes (Consulta SQL)

```sql
SELECT 
    f.nombre,
    f.fecha_nacimiento,
    p.nombre_parentesco,
    TIMESTAMPDIFF(YEAR, f.fecha_nacimiento, CURDATE()) as edad
FROM familiares f
INNER JOIN parentescos p ON f.parentesco_id = p.id
WHERE MONTH(f.fecha_nacimiento) = MONTH(CURDATE())
ORDER BY DAY(f.fecha_nacimiento);
```

---

### Total de gastos estimados en regalos

```sql
SELECT 
    f.nombre,
    COUNT(ir.id) as total_ideas,
    SUM(ir.precio_estimado) as gasto_estimado,
    SUM(CASE WHEN ir.comprado = 1 THEN ir.precio_estimado ELSE 0 END) as gasto_real
FROM familiares f
LEFT JOIN ideas_regalos ir ON f.id = ir.familiar_id
GROUP BY f.id, f.nombre
ORDER BY gasto_estimado DESC;
```

---

## 🔧 Mantenimiento

### Limpieza anual

Al final de cada año, considera:

1. Revisar y actualizar información desactualizada
2. Eliminar ideas de regalos antiguas
3. Actualizar teléfonos y emails
4. Hacer un backup de la base de datos:

```bash
php artisan db:backup
```

O manualmente:

```bash
mysqldump -u root -p cumpleapp > backup_cumpleapp_2024.sql
```

---

### Restaurar backup

```bash
mysql -u root -p cumpleapp < backup_cumpleapp_2024.sql
```

---

## 🎁 Ideas de Regalos por Categoría

Aquí algunas categorías útiles para organizar tus ideas:

### Tecnología
- Auriculares
- Smartwatch
- Tablet
- Accesorios para celular

### Hogar
- Decoración
- Plantas
- Velas aromáticas
- Organizadores

### Experiencias
- Cena en restaurante
- Spa
- Curso o taller
- Entrada a concierto/teatro

### Personalizados
- Álbum de fotos
- Taza personalizada
- Camiseta con diseño
- Joyería con grabado

### Libros
- Novela de su género favorito
- Libro de cocina
- Biografía
- Libro de fotografía

---

**¿Tienes más ideas o sugerencias?** ¡Compártelas con la comunidad!

