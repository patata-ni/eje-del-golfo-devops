<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Estructura del SQL proporcionado; no incluye registros, contraseñas ni sesiones.
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('choferes')) {
            Schema::create('choferes', function (Blueprint $table) {
                $table->bigIncrements('id_chofer');
                $table->unsignedBigInteger('personal_id');
                $table->string('numero_empleado', 30);
                $table->enum('estado', ['disponible','en_ruta','vacaciones','enfermo','de_baja'])->default('disponible');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['personal_id'], 'uq_choferes_personal');
                $table->unique(['numero_empleado'], 'uq_choferes_numero_empleado');
            });
        }
        if (!Schema::hasTable('horarios')) {
            Schema::create('horarios', function (Blueprint $table) {
                $table->bigIncrements('id_horario');
                $table->unsignedBigInteger('ruta_id');
                $table->time('hora_salida');
                $table->string('dias_operacion', 100)->nullable();
                $table->enum('estado', ['activo','inactivo'])->default('activo');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['ruta_id', 'hora_salida'], 'idx_horarios_ruta_hora');
            });
        }
        if (!Schema::hasTable('incidencias')) {
            Schema::create('incidencias', function (Blueprint $table) {
                $table->bigIncrements('id_incidencia');
                $table->unsignedBigInteger('unidad_id');
                $table->unsignedBigInteger('personal_id')->nullable();
                $table->dateTime('fecha_reporte')->useCurrent();
                $table->string('tipo', 100);
                $table->text('descripcion');
                $table->enum('prioridad', ['baja','media','alta','critica'])->default('media');
                $table->enum('estado', ['reportada','en_revision','atendida','cerrada'])->default('reportada');
                $table->dateTime('fecha_cierre')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['estado', 'prioridad'], 'idx_incidencias_estado_prioridad');
                $table->index(['unidad_id'], 'idx_incidencias_unidad');
                $table->index(['personal_id'], 'fk_incidencias_personal');
            });
        }
        if (!Schema::hasTable('inventario')) {
            Schema::create('inventario', function (Blueprint $table) {
                $table->bigIncrements('id_inventario');
                $table->unsignedBigInteger('pieza_id');
                $table->unsignedInteger('cantidad_actual')->default(0);
                $table->string('ubicacion', 150)->nullable();
                $table->enum('estado', ['disponible','agotado','inactivo'])->default('disponible');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['pieza_id', 'ubicacion'], 'uq_inventario_pieza_ubicacion');
            });
        }
        if (!Schema::hasTable('mantenimientos')) {
            Schema::create('mantenimientos', function (Blueprint $table) {
                $table->bigIncrements('id_mantenimiento');
                $table->unsignedBigInteger('unidad_id');
                $table->unsignedBigInteger('incidencia_id')->nullable();
                $table->enum('tipo', ['preventivo','correctivo']);
                $table->date('fecha_inicio');
                $table->date('fecha_fin')->nullable();
                $table->text('diagnostico')->nullable();
                $table->text('trabajo_realizado')->nullable();
                $table->text('observaciones')->nullable();
                $table->enum('estado', ['pendiente','en_proceso','finalizado','cancelado'])->default('pendiente');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['unidad_id', 'estado'], 'idx_mantenimientos_unidad_estado');
                $table->index(['incidencia_id'], 'idx_mantenimientos_incidencia');
            });
        }
        if (!Schema::hasTable('mantenimiento_mecanico')) {
            Schema::create('mantenimiento_mecanico', function (Blueprint $table) {
                $table->bigIncrements('id_mantenimiento_mecanico');
                $table->unsignedBigInteger('mantenimiento_id');
                $table->unsignedBigInteger('mecanico_id');
                $table->string('funcion', 150)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['mantenimiento_id', 'mecanico_id'], 'uq_mantenimiento_mecanico');
                $table->index(['mecanico_id'], 'fk_mm_personal');
            });
        }
        if (!Schema::hasTable('movimientos_inventario')) {
            Schema::create('movimientos_inventario', function (Blueprint $table) {
                $table->bigIncrements('id_movimiento_inventario');
                $table->unsignedBigInteger('pieza_id');
                $table->unsignedBigInteger('personal_id')->nullable();
                $table->unsignedBigInteger('mantenimiento_id')->nullable();
                $table->enum('tipo_movimiento', ['entrada','salida','ajuste']);
                $table->unsignedInteger('cantidad');
                $table->dateTime('fecha_movimiento')->useCurrent();
                $table->text('observaciones')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['pieza_id', 'fecha_movimiento'], 'idx_movimientos_pieza_fecha');
                $table->index(['personal_id'], 'fk_movimientos_personal');
                $table->index(['mantenimiento_id'], 'fk_movimientos_mantenimientos');
            });
        }
        if (!Schema::hasTable('notificaciones')) {
            Schema::create('notificaciones', function (Blueprint $table) {
                $table->bigIncrements('id_notificacion');
                $table->unsignedBigInteger('personal_id');
                $table->string('tipo', 80);
                $table->string('titulo', 150);
                $table->text('mensaje');
                $table->dateTime('fecha')->useCurrent();
                $table->tinyInteger('leida')->default(0);
                $table->unsignedBigInteger('mantenimiento_id')->nullable();
                $table->unsignedBigInteger('incidencia_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['personal_id', 'leida'], 'idx_notificaciones_personal_leida');
                $table->index(['mantenimiento_id'], 'fk_notificaciones_mantenimientos');
                $table->index(['incidencia_id'], 'fk_notificaciones_incidencias');
            });
        }
        if (!Schema::hasTable('paradas')) {
            Schema::create('paradas', function (Blueprint $table) {
                $table->bigIncrements('id_parada');
                $table->string('nombre', 150);
                $table->decimal('latitud', 10,7)->nullable();
                $table->decimal('longitud', 10,7)->nullable();
                $table->string('localidad', 120)->nullable();
                $table->enum('estado', ['activa','inactiva'])->default('activa');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['nombre'], 'idx_paradas_nombre');
            });
        }
        if (!Schema::hasTable('personal')) {
            Schema::create('personal', function (Blueprint $table) {
                $table->bigIncrements('id_personal');
                $table->unsignedBigInteger('rol_id');
                $table->string('nombre', 100);
                $table->string('apellido_paterno', 100);
                $table->string('apellido_materno', 100)->nullable();
                $table->string('telefono', 25)->nullable();
                $table->string('correo', 255);
                $table->string('contrasena', 255);
                $table->timestamp('fecha_registro')->useCurrent();
                $table->enum('estado', ['activo','inactivo'])->default('activo');
                $table->timestamp('email_verificado_en')->nullable();
                $table->string('remember_token', 100)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['correo'], 'uq_personal_correo');
                $table->index(['rol_id'], 'idx_personal_rol');
            });
        }
        if (!Schema::hasTable('piezas')) {
            Schema::create('piezas', function (Blueprint $table) {
                $table->bigIncrements('id_pieza');
                $table->string('nombre', 150);
                $table->text('descripcion')->nullable();
                $table->string('unidad_medida', 40)->default('pieza');
                $table->unsignedInteger('stock_minimo')->default(0);
                $table->enum('estado', ['activa','inactiva'])->default('activa');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['nombre'], 'idx_piezas_nombre');
            });
        }
        if (!Schema::hasTable('rutas')) {
            Schema::create('rutas', function (Blueprint $table) {
                $table->bigIncrements('id_ruta');
                $table->string('origen', 150);
                $table->string('destino', 150);
                $table->unsignedSmallInteger('duracion_minutos')->nullable();
                $table->unsignedSmallInteger('duracion_maxima_minutos')->nullable();
                $table->decimal('precio', 10,2)->nullable();
                $table->enum('estado', ['activa','inactiva'])->default('activa');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['origen', 'destino'], 'idx_rutas_origen_destino');
            });
        }
        if (!Schema::hasTable('ruta_parada')) {
            Schema::create('ruta_parada', function (Blueprint $table) {
                $table->bigIncrements('id_ruta_parada');
                $table->unsignedBigInteger('ruta_id');
                $table->unsignedBigInteger('parada_id');
                $table->unsignedSmallInteger('orden');
                $table->unsignedSmallInteger('minutos_desde_inicio')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['ruta_id', 'orden'], 'uq_ruta_parada_orden');
                $table->unique(['ruta_id', 'parada_id'], 'uq_ruta_parada_parada');
                $table->index(['parada_id'], 'fk_ruta_parada_paradas');
            });
        }
        if (!Schema::hasTable('salidas')) {
            Schema::create('salidas', function (Blueprint $table) {
                $table->bigIncrements('id_salida');
                $table->unsignedBigInteger('horario_id');
                $table->date('fecha');
                $table->unsignedBigInteger('unidad_id')->nullable();
                $table->unsignedBigInteger('chofer_id')->nullable();
                $table->dateTime('hora_salida_real')->nullable();
                $table->dateTime('llegada_terminal_real')->nullable();
                $table->dateTime('llegada_estimada_minima')->nullable();
                $table->dateTime('llegada_estimada_maxima')->nullable();
                $table->dateTime('llegada_real')->nullable();
                $table->enum('estado', ['programada','retrasada','en_curso','completada','cancelada'])->default('programada');
                $table->text('observaciones')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['fecha', 'estado'], 'idx_salidas_fecha_estado');
                $table->index(['horario_id', 'fecha'], 'idx_salidas_horario_fecha');
                $table->index(['unidad_id'], 'idx_salidas_unidad');
                $table->index(['chofer_id'], 'idx_salidas_chofer');
            });
        }
        if (!Schema::hasTable('unidades')) {
            Schema::create('unidades', function (Blueprint $table) {
                $table->bigIncrements('id_unidad');
                $table->string('numero_unidad', 30);
                $table->string('placas', 20);
                $table->string('marca', 80)->nullable();
                $table->string('modelo', 80)->nullable();
                $table->unsignedSmallInteger('anio')->nullable();
                $table->unsignedSmallInteger('capacidad');
                $table->enum('estado', ['disponible','en_servicio','en_mantenimiento','fuera_de_servicio','inactiva'])->default('disponible');
                $table->text('observaciones')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['numero_unidad'], 'uq_unidades_numero');
                $table->unique(['placas'], 'uq_unidades_placas');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades');
        Schema::dropIfExists('salidas');
        Schema::dropIfExists('ruta_parada');
        Schema::dropIfExists('rutas');
        Schema::dropIfExists('piezas');
        Schema::dropIfExists('personal');
        Schema::dropIfExists('paradas');
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('mantenimiento_mecanico');
        Schema::dropIfExists('mantenimientos');
        Schema::dropIfExists('inventario');
        Schema::dropIfExists('incidencias');
        Schema::dropIfExists('horarios');
        Schema::dropIfExists('choferes');
    }
};
