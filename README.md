# Foro en PHP y CSS

## 📌 Descripción
Este es un foro web construido con PHP y CSS. Permite a los usuarios registrarse, iniciar sesión y participar en discusiones. También incluye funcionalidades para gestionar temas y respuestas.

## 📂 Estructura del Proyecto

### 🔹 Archivos principales
- `index.php` → Página principal del foro.
- `login.php` → Página de inicio de sesión.
- `registro.css` → Estilos para el formulario de registro.
- `perfil.php` → Página del perfil de usuario.
- `config.php` → Configuración del foro.

### 🛠️ Funcionalidades
- **Gestión de Usuarios**
  - `crearusuario.php` → Crea nuevos usuarios en la base de datos.
  - `login.php` → Permite la autenticación de los usuarios.
  - `perfil.php` → Muestra la información del usuario registrado.

- **Gestión de Temas y Respuestas**
  - `insertartema.php` → Agrega un nuevo tema al foro.
  - `eliminartema.php` → Permite eliminar temas existentes.
  - `procesar_respuesta.php` → Maneja respuestas a los temas.
  - `eliminar_respuesta.php` → Permite eliminar respuestas.

### 🎨 Archivos CSS
- `style.css` → Estilos generales del foro.
- `perfil.css` → Estilos específicos para la página de perfil.
- `tema.css` → Estilos para los temas del foro.

### 📂 Carpetas
- `images/` → Contiene las imágenes utilizadas en el foro.
- `logs/` → Carpeta para almacenar registros del sistema.
- `usuarios/` → Puede almacenar datos de los usuarios.

## 🚀 Instalación y Configuración
1. **Clonar el repositorio**  
   ```sh
   git clone https://github.com/usuario/foro-php.git
   cd foro-php
