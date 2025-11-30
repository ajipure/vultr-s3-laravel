# Laravel Vultr S3 Connection Checker

## 📋 Descripción

Comando de consola de Laravel para verificar y probar la conexión al almacenamiento de objetos S3 de Vultr. Este comando ejecuta una serie de pruebas exhaustivas para asegurar que tu configuración de S3 funciona correctamente.

## ✨ Características

- ✅ Verificación de acceso al bucket
- ✅ Prueba de escritura de archivos
- ✅ Prueba de lectura de archivos
- ✅ Generación de URLs públicas
- ✅ Verificación de existencia de archivos
- ✅ Obtención de metadatos (tamaño, fecha de modificación)
- ✅ Prueba de eliminación de archivos
- ✅ Limpieza automática de archivos de prueba
- ✅ Modo verbose para debugging detallado

## 📦 Requisitos

- PHP >= 8.0
- Laravel >= 9.x
- Composer
- Cuenta de Vultr con Object Storage habilitado
- Paquete AWS SDK para PHP

## 🚀 Instalación

### 1. Instalar dependencias

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### 2. Crear el comando

Crea el archivo `app/Console/Commands/CheckVultrS3Connection.php` y copia el código del comando.

### 3. Configurar filesystems.php

Añade la configuración de Vultr en `config/filesystems.php`:

```php
'disks' => [
    // ... otros discos

    'vultr' => [
        'driver' => 's3',
        'key' => env('VULTR_ACCESS_KEY'),
        'secret' => env('VULTR_SECRET_KEY'),
        'region' => env('VULTR_REGION', 'ewr1'),
        'bucket' => env('VULTR_BUCKET'),
        'endpoint' => env('VULTR_ENDPOINT'),
        'use_path_style_endpoint' => false,
        'throw' => false,
    ],
],
```

### 4. Configurar variables de entorno

Añade las siguientes variables a tu archivo `.env`:

```env
VULTR_ACCESS_KEY=tu_access_key_aqui
VULTR_SECRET_KEY=tu_secret_key_aqui
VULTR_REGION=ewr1
VULTR_BUCKET=nombre-de-tu-bucket
VULTR_ENDPOINT=https://ewr1.vultrobjects.com
```

## 🌍 Regiones disponibles de Vultr

| Código | Ubicación | Endpoint |
|--------|-----------|----------|
| `ewr1` | Nueva Jersey, EE.UU. | `https://ewr1.vultrobjects.com` |
| `sjc1` | Silicon Valley, EE.UU. | `https://sjc1.vultrobjects.com` |
| `ams1` | Ámsterdam, Países Bajos | `https://ams1.vultrobjects.com` |
| `sgp1` | Singapur | `https://sgp1.vultrobjects.com` |
| `blr1` | Bangalore, India | `https://blr1.vultrobjects.com` |
| `del1` | Delhi NCR, India | `https://del1.vultrobjects.com` |

## 💻 Uso

### Comando básico

```bash
php artisan vultr:check-s3
```

### Con información detallada

```bash
php artisan vultr:check-s3 --verbose
```

### Especificar un disco diferente

```bash
php artisan vultr:check-s3 --disk=mi-disco-personalizado
```

### Combinando opciones

```bash
php artisan vultr:check-s3 --disk=vultr --verbose
```

## 📊 Salida del comando

### Modo normal

```
🔍 Verificando conexión a Vultr S3...

📦 Prueba 1: Verificando acceso al bucket...
   ✅ Bucket 'mi-bucket' está accesible
📝 Prueba 2: Escribiendo archivo de prueba...
   ✅ Archivo 'test-connection-1234567890.txt' creado exitosamente
📖 Prueba 3: Leyendo archivo de prueba...
   ✅ Archivo leído exitosamente
🔗 Prueba 4: Obteniendo URL del archivo...
   ✅ URL: https://ewr1.vultrobjects.com/mi-bucket/test-connection-1234567890.txt
🔍 Prueba 5: Verificando existencia del archivo...
   ✅ El archivo existe
📊 Prueba 6: Obteniendo metadatos del archivo...
   ✅ Tamaño: 52 bytes
   ✅ Última modificación: 2025-11-30 14:30:45
🗑️  Prueba 7: Eliminando archivo de prueba...
   ✅ Archivo eliminado exitosamente

✨ ¡Todas las pruebas pasaron exitosamente!
🎉 ¡La conexión a Vultr S3 funciona correctamente!
```

### Modo verbose

Incluye información adicional de configuración:

```
⚙️  Configuración del disco 'vultr':
+-----------+----------------------------------+
| Parámetro | Valor                            |
+-----------+----------------------------------+
| Driver    | s3                               |
| Región    | ewr1                             |
| Bucket    | mi-bucket                        |
| Endpoint  | https://ewr1.vultrobjects.com    |
| Key       | ABCDEFGHIJ...                    |
+-----------+----------------------------------+
```

## 🔧 Solución de problemas

### Error: "Bucket no encontrado"

**Causa:** El bucket no existe o el nombre es incorrecto.

**Solución:**
1. Verifica que el bucket existe en tu panel de Vultr
2. Confirma que el nombre del bucket en `.env` es correcto
3. Asegúrate de que no hay espacios o caracteres especiales

### Error: "Access Denied"

**Causa:** Credenciales incorrectas o permisos insuficientes.

**Solución:**
1. Verifica que `VULTR_ACCESS_KEY` y `VULTR_SECRET_KEY` son correctos
2. Asegúrate de que las credenciales tienen permisos de lectura/escritura
3. Regenera las credenciales en el panel de Vultr si es necesario

### Error: "Could not resolve host"

**Causa:** Endpoint incorrecto o problema de red.

**Solución:**
1. Verifica que `VULTR_ENDPOINT` coincide con tu región
2. Comprueba tu conexión a internet
3. Asegúrate de que el formato del endpoint es correcto (incluye `https://`)

### Error: "Region is missing"

**Causa:** La región no está configurada correctamente.

**Solución:**
1. Añade `VULTR_REGION` a tu archivo `.env`
2. Usa el código de región correcto (ej: `ewr1`, `sjc1`)

## 📝 Configuración avanzada

### Múltiples buckets

Puedes configurar múltiples discos para diferentes buckets:

```php
'vultr-public' => [
    'driver' => 's3',
    'key' => env('VULTR_ACCESS_KEY'),
    'secret' => env('VULTR_SECRET_KEY'),
    'region' => env('VULTR_REGION'),
    'bucket' => env('VULTR_PUBLIC_BUCKET'),
    'endpoint' => env('VULTR_ENDPOINT'),
    'visibility' => 'public',
],

'vultr-private' => [
    'driver' => 's3',
    'key' => env('VULTR_ACCESS_KEY'),
    'secret' => env('VULTR_SECRET_KEY'),
    'region' => env('VULTR_REGION'),
    'bucket' => env('VULTR_PRIVATE_BUCKET'),
    'endpoint' => env('VULTR_ENDPOINT'),
    'visibility' => 'private',
],
```

Luego prueba cada uno:

```bash
php artisan vultr:check-s3 --disk=vultr-public
php artisan vultr:check-s3 --disk=vultr-private
```

### Configurar timeout personalizado

```php
'vultr' => [
    'driver' => 's3',
    // ... otras configuraciones
    'options' => [
        'http' => [
            'timeout' => 30,
            'connect_timeout' => 10,
        ],
    ],
],
```

## 🔒 Seguridad

- ⚠️ **Nunca** commits tu archivo `.env` al control de versiones
- 🔐 Usa variables de entorno para todas las credenciales sensibles
- 🔑 Rota tus claves de acceso regularmente
- 👥 Usa credenciales con permisos mínimos necesarios
- 📋 Mantén un registro de auditoría de accesos a S3

## 🧪 Testing

Para incluir este comando en tus tests:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class VultrS3CommandTest extends TestCase
{
    public function test_vultr_s3_connection_command()
    {
        $this->artisan('vultr:check-s3')
            ->expectsOutput('✨ ¡Todas las pruebas pasaron exitosamente!')
            ->assertExitCode(0);
    }
}
```

## 📚 Recursos adicionales

- [Documentación de Vultr Object Storage](https://www.vultr.com/docs/vultr-object-storage/)
- [Documentación de Laravel Filesystem](https://laravel.com/docs/filesystem)
- [AWS SDK para PHP](https://docs.aws.amazon.com/sdk-for-php/)

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT.

## 👨‍💻 Autor

Creado con ❤️ para la comunidad de Laravel

## 🆘 Soporte

Si encuentras algún problema o tienes preguntas:

1. Revisa la sección de [Solución de problemas](#-solución-de-problemas)
2. Busca en los issues existentes
3. Crea un nuevo issue con detalles completos del problema

---

**¿Te resultó útil?** ⭐ Dale una estrella al repositorio!
