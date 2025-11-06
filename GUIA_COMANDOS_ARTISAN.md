# 🖥️ Guía de Comandos Artisan - CumpleApp

## 📋 Comandos Disponibles

Se han creado **3 comandos personalizados** para facilitar la gestión de familiares desde la consola.

---

## 1️⃣ **Listar Todos los Familiares**

### Comando:
```bash
php artisan familiar:listar
```

### Descripción:
Lista todos los familiares con su información principal.

### Salida:
```
📋 Total de familiares: 12

┌────┬─────────────────┬───────────┬───────────┬────────┬────────────┐
│ ID │ Nombre          │ DNI       │ Parentesco│ Acceso │ Teléfono   │
├────┼─────────────────┼───────────┼───────────┼────────┼────────────┤
│ 1  │ AGAPITO         │ 70123456  │ Hermano   │ ✅ Sí  │ +52123...  │
│ 2  │ German De la cr │ 45678901  │ Hermano   │ ✅ Sí  │ -          │
│ 3  │ Janis Roncal    │ ❌ Sin DNI│ Hermano   │ ❌ No  │ +52456...  │
└────┴─────────────────┴───────────┴───────────┴────────┴────────────┘

📊 Estadísticas:
   Con DNI: 8
   Sin DNI: 4
   Con Acceso: 5
```

### Opciones:
```bash
# Mostrar solo hermanos
php artisan familiar:listar --solo-hermanos

# Mostrar solo los que tienen acceso
php artisan familiar:listar --con-acceso

# Mostrar solo los que NO tienen DNI
php artisan familiar:listar --sin-dni
```

---

## 2️⃣ **Buscar Familiar por Nombre**

### Comando:
```bash
php artisan familiar:buscar [nombre]
```

### Ejemplos:
```bash
# Buscar "AGAPITO"
php artisan familiar:buscar AGAPITO

# Buscar cualquiera que tenga "German"
php artisan familiar:buscar German

# Sin parámetro (te pedirá el nombre)
php artisan familiar:buscar
```

### Salida:
```
✅ Se encontraron 2 familiar(es):

┌────┬─────────────────┬───────────┬───────────┬────────────┐
│ ID │ Nombre          │ DNI       │ Parentesco│ Puede Acces│
├────┼─────────────────┼───────────┼───────────┼────────────┤
│ 1  │ AGAPITO         │ 70123456  │ Hermano   │ ✅ Sí      │
│ 5  │ AGAPITO Jr      │ (Sin DNI) │ Hijo      │ ❌ No      │
└────┴─────────────────┴───────────┴───────────┴────────────┘

💡 Tips:
• Para actualizar DNI: php artisan familiar:dni {id} {dni}
• Para dar acceso: Editar en la aplicación web
```

---

## 3️⃣ **Actualizar DNI de un Familiar**

### Comando Básico:
```bash
php artisan familiar:dni {id} {dni}
```

### Ejemplos:
```bash
# Asignar DNI 70123456 al familiar con ID 1
php artisan familiar:dni 1 70123456

# Asignar DNI y habilitar acceso al sistema
php artisan familiar:dni 1 70123456 --acceso

# Sin DNI (te pedirá el DNI interactivamente)
php artisan familiar:dni 1
```

### Salida:
```
📋 Familiar Encontrado:
   ID: 1
   Nombre: AGAPITO
   DNI Actual: (Sin DNI)
   Puede Acceder: ❌ No

Ingresa el DNI a asignar: 70123456

¿Deseas actualizar el DNI de 'AGAPITO' a '70123456'? (yes/no) [no]:
> yes

✅ DNI actualizado exitosamente!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
👤 Familiar: AGAPITO
🆔 DNI: 70123456
🔐 Puede Acceder: ❌ No
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

⚠️  Para habilitar el acceso, ejecuta:
   php artisan familiar:dni 1 70123456 --acceso
```

### Con la opción `--acceso`:
```bash
php artisan familiar:dni 1 70123456 --acceso
```

Salida:
```
✅ DNI actualizado exitosamente!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
👤 Familiar: AGAPITO
🆔 DNI: 70123456
🔐 Puede Acceder: ✅ Sí
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🎉 Credenciales de Acceso:
   Usuario: 70123456
   Contraseña: 70123456 (por defecto)
   URL: http://localhost:8000/login
```

---

## 🎯 Flujo de Trabajo Recomendado

### **Configurar Acceso para los 10 Hermanos:**

#### Paso 1: Listar hermanos sin DNI
```bash
php artisan familiar:listar --solo-hermanos --sin-dni
```

#### Paso 2: Para cada hermano, asignar DNI
```bash
php artisan familiar:dni 1 70123456 --acceso
php artisan familiar:dni 2 45678901 --acceso
php artisan familiar:dni 3 12345678 --acceso
# ... etc
```

#### Paso 3: Verificar
```bash
php artisan familiar:listar --solo-hermanos --con-acceso
```

---

## 💡 Casos de Uso

### Caso 1: Buscar a un Hermano
```bash
# No recuerdas el ID de German
php artisan familiar:buscar German

# Resultado: ID 2
# Ahora puedes asignarle DNI
php artisan familiar:dni 2 45678901 --acceso
```

### Caso 2: Ver Quién No Tiene DNI
```bash
php artisan familiar:listar --sin-dni

# Muestra todos los que necesitan DNI
# Asignas DNI a cada uno
```

### Caso 3: Ver Hermanos con Acceso
```bash
php artisan familiar:listar --solo-hermanos --con-acceso

# Muestra los 10 hermanos que ya pueden hacer login
```

### Caso 4: Configuración Rápida de los 10 Hermanos
```bash
# 1. Ver la lista completa
php artisan familiar:listar --solo-hermanos

# 2. Asignar DNI a todos
php artisan familiar:dni 1 70123456 --acceso
php artisan familiar:dni 2 45678901 --acceso
php artisan familiar:dni 3 12345678 --acceso
php artisan familiar:dni 4 78901234 --acceso
php artisan familiar:dni 5 23456789 --acceso
php artisan familiar:dni 6 89012345 --acceso
php artisan familiar:dni 7 34567890 --acceso
php artisan familiar:dni 8 90123456 --acceso
php artisan familiar:dni 9 56789012 --acceso
php artisan familiar:dni 10 67890123 --acceso

# 3. Verificar
php artisan familiar:listar --con-acceso
```

---

## 🎨 **Mejoras de Diseño Implementadas**

### **Nuevos Estilos de Stat-Cards (Estilo ElaAdmin):**

#### **1. Diseño Moderno con Ícono de Fondo:**
```css
.stat-card.bg-primary   - Morado/Azul con gradiente
.stat-card.bg-success   - Verde con gradiente
.stat-card.bg-danger    - Rojo con gradiente
.stat-card.bg-warning   - Amarillo/Naranja con gradiente
.stat-card.bg-info      - Azul con gradiente
.stat-card.bg-purple    - Morado vibrante
.stat-card.bg-pink      - Rosa vibrante
```

**Características:**
- ✅ **Fondo sólido** con gradiente de color
- ✅ **Ícono grande semi-transparente** de fondo (20% opacidad)
- ✅ **Texto blanco** para máximo contraste
- ✅ **Número grande y bold** (2.5rem)
- ✅ **Label en mayúsculas** con espaciado de letras
- ✅ **Sombras sutiles** y efecto hover

#### **2. Estructura HTML:**
```html
<div class="stat-card bg-success">
    <div class="stat-icon">
        <i class="bi bi-check-circle-fill"></i>
    </div>
    <div class="stat-content">
        <div class="stat-number">245</div>
        <div class="stat-label">Cuotas Pagadas</div>
    </div>
</div>
```

#### **3. Ejemplo Visual:**
```
┌─────────────────────────────┐
│               👥            │  ← Ícono grande semi-transparente (fondo)
│  10                         │  ← Número (blanco, grande)
│  TOTAL HERMANOS             │  ← Label (blanco, mayúsculas)
└─────────────────────────────┘
   ↑ Fondo sólido morado con gradiente
```

---

## 📊 Clases CSS Disponibles

### **Stat-Cards con Gradientes (Recomendado):**
- `bg-primary` - Morado/Azul (#667eea → #764ba2)
- `bg-success` - Verde (#10b981 → #059669)
- `bg-danger` - Rojo (#ef4444 → #dc2626)
- `bg-warning` - Amarillo/Naranja (#f59e0b → #d97706)
- `bg-info` - Azul (#3b82f6 → #2563eb)
- `bg-purple` - Morado (#a855f7 → #9333ea)
- `bg-pink` - Rosa (#ec4899 → #db2777)
- `bg-indigo` - Índigo (#6366f1 → #4f46e5)

---

## 🚀 Resumen de Comandos

| Comando | Uso |
|---------|-----|
| `familiar:listar` | Lista todos los familiares |
| `familiar:buscar [nombre]` | Busca por nombre |
| `familiar:dni {id} {dni}` | Asigna DNI |
| `familiar:dni {id} {dni} --acceso` | Asigna DNI y da acceso |

---

## ✅ Ventajas de los Comandos

### 1. **Rapidez**
```
✅ No necesitas entrar a la web
✅ Cambios masivos rápidos
✅ Scripting automatizado
```

### 2. **Facilidad**
```
✅ Comandos intuitivos
✅ Confirmaciones de seguridad
✅ Mensajes claros
```

### 3. **Información Clara**
```
✅ Tablas organizadas
✅ Colores y emojis
✅ Estadísticas automáticas
```

### 4. **Seguridad**
```
✅ Valida DNI duplicados
✅ Pide confirmación
✅ Muestra estado actual
```

---

## 🎊 ¡Listo para Usar!

**Stat-Cards Estilo ElaAdmin:**
- ✅ Fondo sólido con gradientes vibrantes
- ✅ Ícono de fondo grande semi-transparente
- ✅ Texto blanco con excelente contraste
- ✅ Números grandes y bold
- ✅ Labels en mayúsculas con espaciado
- ✅ Efecto hover suave

**Comandos Artisan:**
- ✅ `familiar:listar` - Ver todos con filtros
- ✅ `familiar:buscar` - Buscar por nombre
- ✅ `familiar:dni` - Asignar DNI y acceso

**¡Prueba los comandos ahora mismo!** 🚀

---

**Versión:** 2.3  
**Comandos:** 3 nuevos comandos Artisan  
**Estilos:** Diseño moderno estilo ElaAdmin con gradientes  
**Última actualización:** Noviembre 2025

