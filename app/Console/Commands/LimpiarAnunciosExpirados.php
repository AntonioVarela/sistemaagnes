<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\anuncio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LimpiarAnunciosExpirados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anuncios:limpiar-expirados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina automáticamente los anuncios que han expirado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando limpieza de anuncios expirados...');
        
        // Obtener anuncios expirados
        $anunciosExpirados = anuncio::where('fecha_expiracion', '<', Carbon::today())->get();
        
        $contador = 0;
        
        foreach ($anunciosExpirados as $anuncio) {
            // Eliminar archivo asociado si existe
            if ($anuncio->archivo) {
                try {
                    Storage::disk('s3')->delete($anuncio->archivo);
                    $this->line("Archivo eliminado: {$anuncio->archivo}");
                } catch (\Exception $e) {
                    $this->warn("No se pudo eliminar el archivo: {$anuncio->archivo}");
                }
            }
            
            // Eliminar el anuncio
            $anuncio->delete();
            $contador++;
            
            $this->line("Anuncio eliminado: {$anuncio->titulo}");
        }
        
        $this->info("Limpieza completada. Se eliminaron {$contador} anuncios expirados.");
        
        return 0;
    }
}
