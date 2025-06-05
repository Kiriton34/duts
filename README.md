# Plataforma DUTS - UTS

Sistema de Dinero Digital UTS (DUTS) para gestión de usuarios, cuentas, transferencias, eventos y estadísticas.

---

## Instalación y Configuración

1. **Clona el repositorio**
   ```sh
   git clone https://github.com/tu_usuario/tu_repositorio.git
   ```

2. **Coloca la carpeta en tu servidor local**
   - Copia la carpeta del proyecto a `c:\xampp\htdocs\web` (o la ruta de tu servidor local).

3. **Importa la base de datos**
   - Abre **phpMyAdmin**.
   - Crea una base de datos llamada `duts_platform`.
   - Importa el archivo `duts_platform.sql` incluido en el proyecto.

4. **Configura la conexión a la base de datos**
   - Edita el archivo `config/database.php` y ajusta los datos de acceso a tu MySQL:
     ```php
     private $host = "localhost";
     private $database_name = "duts_platform";
     private $username = "root";
     private $password = "";
     ```

5. **Inicia Apache y MySQL en XAMPP**

6. **Accede a la API**
   - Abre tu navegador y ve a:  
     [http://localhost/web/](http://localhost/web/)

---

## Endpoints principales

### **Autenticación**
- `POST /auth/login` — Iniciar sesión

### **Usuarios**
- `POST /users` — Crear usuario
- `GET /users/{id}` — Obtener usuario por ID
- `GET /users/profile/{id}` — Ver perfil de usuario
- `PUT /users/{id}` — Actualizar usuario
- `DELETE /users/{id}` — Eliminar usuario

### **DUTS**
- `GET /duts/balance/{id}` — Consultar saldo
- `POST /duts/transfer` — Transferir DUTS
- `GET /duts/history/{id}` — Historial de transacciones
- `GET /duts/stats/{periodo}` — Promedios por día, semana, mes, año, semestre

### **Eventos**
- `POST /eventos` — Crear evento
- `GET /eventos` — Listar eventos (puedes filtrar por tipo: `?tipo=CIINATIC`)
- `GET /eventos/{id}` — Ver evento por ID
- `PUT /eventos/{id}` — Actualizar evento
- `DELETE /eventos/{id}` — Eliminar evento
- `POST /eventos/{id}/inscribir` — Inscribirse a evento
- `POST /eventos/{id}/cancelar` — Darse de baja de evento
- `GET /eventos/{id}/inscritos` — Listar inscritos a evento

---

## Ejemplo de uso con Postman

### 1. **Crear usuario**
- **POST** `http://localhost/web/users`
- **Body (JSON):**
  ```json
  {
    "nombres": "Juan",
    "apellidos": "Pérez",
    "email": "juan@example.com",
    "ciudad": "Bogotá",
    "pais": "Colombia",
    "descripcion": "Estudiante",
    "lista_intereses": "Finanzas, Tecnología",
    "programa": "Ingeniería",
    "semestre": "5",
    "username": "juan123",
    "password": "123456",
    "tipo_usuario": "estudiante"
  }
  ```

### 2. **Login**
- **POST** `http://localhost/web/auth/login`
- **Body (JSON):**
  ```json
  {
    "username": "juan123",
    "password": "123456"
  }
  ```
- **Respuesta:** Copia el campo `token`.

### 3. **Usar endpoints protegidos**
- En la pestaña **Headers** de Postman, agrega:
  ```
  Authorization: Bearer TU_TOKEN
  ```

---

## Funcionalidades principales

- CRUD de usuarios (estudiante, docente, administrativo)
- CRUD de eventos (UTSmart, CIINATIC, Grados UTS, Expobienestar, Otro)
- Inscripción y baja en eventos
- Transferencia de DUTS entre usuarios
- Consulta de saldo y estadísticas de DUTS (por día, semana, mes, año, semestre)
- Historial de transacciones
- Filtros avanzados de eventos
- Seguridad con JWT

---

## Funcionalidades adicionales sugeridas

- Recuperación y verificación de contraseña por email
- Notificaciones de eventos
- Ranking de usuarios por saldo DUTS
- Exportar historial de transacciones
- Comentarios en eventos
- Sistema de roles y permisos avanzados
- Panel de administración (API)
- Reportes gráficos (API)
- Logs de errores y auditoría
- Filtros avanzados y búsqueda

---

## Notas

- **No compartas tu archivo `config/database.php` con contraseñas reales en público.**
- Puedes modificar y ampliar los endpoints según tus necesidades.
- Si tienes dudas, revisa el código fuente y los modelos en la carpeta `/models`.

---

**¡Listo! Tu profesor podrá instalar, probar y revisar todo el sistema siguiendo este README.**
