<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Aws\S3\Exception\S3Exception;

class CheckVultrS3Connection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vultr:check-s3
                            {--disk=vultr : Nombre del disco desde config/filesystems.php}
                            {--verbose : Mostrar información detallada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar la conexión al almacenamiento S3 de Vultr';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $diskName = $this->option('disk');
        $verbose = $this->option('verbose');

        $this->info("🔍 Verificando conexión a Vultr S3...");
        $this->newLine();

        try {
            // Obtener el disco
            $disk = Storage::disk($diskName);
            
            if ($verbose) {
                $this->displayConfiguration($diskName);
            }

            // Prueba 1: Verificar existencia del bucket
            $this->info("📦 Prueba 1: Verificando acceso al bucket...");
            $client = $disk->getAdapter()->getClient();
            $bucket = config("filesystems.disks.{$diskName}.bucket");
            
            if ($client->doesBucketExist($bucket)) {
                $this->line("   ✅ Bucket '{$bucket}' está accesible");
            } else {
                $this->error("   ❌ Bucket '{$bucket}' no encontrado");
                return Command::FAILURE;
            }

            // Prueba 2: Crear archivo de prueba
            $this->info("📝 Prueba 2: Escribiendo archivo de prueba...");
            $testFileName = 'test-connection-' . time() . '.txt';
            $testContent = 'Prueba de Conexión S3 Laravel - ' . now()->toDateTimeString();
            
            $disk->put($testFileName, $testContent);
            $this->line("   ✅ Archivo '{$testFileName}' creado exitosamente");

            // Prueba 3: Leer archivo
            $this->info("📖 Prueba 3: Leyendo archivo de prueba...");
            $content = $disk->get($testFileName);
            
            if ($content === $testContent) {
                $this->line("   ✅ Archivo leído exitosamente");
            } else {
                $this->error("   ❌ El contenido del archivo no coincide");
                return Command::FAILURE;
            }

            // Prueba 4: Obtener URL del archivo
            $this->info("🔗 Prueba 4: Obteniendo URL del archivo...");
            $url = $disk->url($testFileName);
            $this->line("   ✅ URL: {$url}");

            // Prueba 5: Verificar existencia del archivo
            $this->info("🔍 Prueba 5: Verificando existencia del archivo...");
            if ($disk->exists($testFileName)) {
                $this->line("   ✅ El archivo existe");
            } else {
                $this->error("   ❌ Archivo no encontrado");
                return Command::FAILURE;
            }

            // Prueba 6: Obtener metadatos
            $this->info("📊 Prueba 6: Obteniendo metadatos del archivo...");
            $size = $disk->size($testFileName);
            $lastModified = $disk->lastModified($testFileName);
            $this->line("   ✅ Tamaño: {$size} bytes");
            $this->line("   ✅ Última modificación: " . date('Y-m-d H:i:s', $lastModified));

            // Prueba 7: Eliminar archivo
            $this->info("🗑️  Prueba 7: Eliminando archivo de prueba...");
            $disk->delete($testFileName);
            
            if (!$disk->exists($testFileName)) {
                $this->line("   ✅ Archivo eliminado exitosamente");
            } else {
                $this->error("   ❌ No se pudo eliminar el archivo");
                return Command::FAILURE;
            }

            // Mensaje final
            $this->newLine();
            $this->info("✨ ¡Todas las pruebas pasaron exitosamente!");
            $this->info("🎉 ¡La conexión a Vultr S3 funciona correctamente!");

            return Command::SUCCESS;

        } catch (S3Exception $e) {
            $this->newLine();
            $this->error("❌ Error de S3: " . $e->getMessage());
            
            if ($verbose) {
                $this->error("Código de error: " . $e->getAwsErrorCode());
                $this->error("Estado: " . $e->getStatusCode());
            }
            
            return Command::FAILURE;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error("❌ Error general: " . $e->getMessage());
            
            if ($verbose) {
                $this->error("Traza: " . $e->getTraceAsString());
            }
            
            return Command::FAILURE;
        }
    }

    /**
     * Mostrar la configuración del disco
     *
     * @param string $diskName
     * @return void
     */
    private function displayConfiguration(string $diskName): void
    {
        $this->info("⚙️  Configuración del disco '{$diskName}':");
        
        $config = config("filesystems.disks.{$diskName}");
        
        $this->table(
            ['Parámetro', 'Valor'],
            [
                ['Driver', $config['driver'] ?? 'N/A'],
                ['Región', $config['region'] ?? 'N/A'],
                ['Bucket', $config['bucket'] ?? 'N/A'],
                ['Endpoint', $config['endpoint'] ?? 'N/A'],
                ['Key', substr($config['key'] ?? '', 0, 10) . '...'],
            ]
        );
        
        $this->newLine();
    }
}
