<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!collect(Schema::getForeignKeys('choferes'))->contains('name', 'fk_choferes_personal')) {
            Schema::table('choferes', function (Blueprint $table) {
                $table->foreign('personal_id', 'fk_choferes_personal')->references('id_personal')->on('personal')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('horarios'))->contains('name', 'fk_horarios_rutas')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->foreign('ruta_id', 'fk_horarios_rutas')->references('id_ruta')->on('rutas')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('incidencias'))->contains('name', 'fk_incidencias_personal')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->foreign('personal_id', 'fk_incidencias_personal')->references('id_personal')->on('personal')->onDelete('set null')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('incidencias'))->contains('name', 'fk_incidencias_unidades')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->foreign('unidad_id', 'fk_incidencias_unidades')->references('id_unidad')->on('unidades')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('inventario'))->contains('name', 'fk_inventario_piezas')) {
            Schema::table('inventario', function (Blueprint $table) {
                $table->foreign('pieza_id', 'fk_inventario_piezas')->references('id_pieza')->on('piezas')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('mantenimientos'))->contains('name', 'fk_mantenimientos_incidencias')) {
            Schema::table('mantenimientos', function (Blueprint $table) {
                $table->foreign('incidencia_id', 'fk_mantenimientos_incidencias')->references('id_incidencia')->on('incidencias')->onDelete('set null')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('mantenimientos'))->contains('name', 'fk_mantenimientos_unidades')) {
            Schema::table('mantenimientos', function (Blueprint $table) {
                $table->foreign('unidad_id', 'fk_mantenimientos_unidades')->references('id_unidad')->on('unidades')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('mantenimiento_mecanico'))->contains('name', 'fk_mm_mantenimientos')) {
            Schema::table('mantenimiento_mecanico', function (Blueprint $table) {
                $table->foreign('mantenimiento_id', 'fk_mm_mantenimientos')->references('id_mantenimiento')->on('mantenimientos')->onDelete('cascade')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('mantenimiento_mecanico'))->contains('name', 'fk_mm_personal')) {
            Schema::table('mantenimiento_mecanico', function (Blueprint $table) {
                $table->foreign('mecanico_id', 'fk_mm_personal')->references('id_personal')->on('personal')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('movimientos_inventario'))->contains('name', 'fk_movimientos_mantenimientos')) {
            Schema::table('movimientos_inventario', function (Blueprint $table) {
                $table->foreign('mantenimiento_id', 'fk_movimientos_mantenimientos')->references('id_mantenimiento')->on('mantenimientos')->onDelete('set null')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('movimientos_inventario'))->contains('name', 'fk_movimientos_personal')) {
            Schema::table('movimientos_inventario', function (Blueprint $table) {
                $table->foreign('personal_id', 'fk_movimientos_personal')->references('id_personal')->on('personal')->onDelete('set null')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('movimientos_inventario'))->contains('name', 'fk_movimientos_piezas')) {
            Schema::table('movimientos_inventario', function (Blueprint $table) {
                $table->foreign('pieza_id', 'fk_movimientos_piezas')->references('id_pieza')->on('piezas')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('notificaciones'))->contains('name', 'fk_notificaciones_incidencias')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->foreign('incidencia_id', 'fk_notificaciones_incidencias')->references('id_incidencia')->on('incidencias')->onDelete('set null')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('notificaciones'))->contains('name', 'fk_notificaciones_mantenimientos')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->foreign('mantenimiento_id', 'fk_notificaciones_mantenimientos')->references('id_mantenimiento')->on('mantenimientos')->onDelete('set null')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('notificaciones'))->contains('name', 'fk_notificaciones_personal')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->foreign('personal_id', 'fk_notificaciones_personal')->references('id_personal')->on('personal')->onDelete('cascade')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('personal'))->contains('name', 'fk_personal_roles')) {
            Schema::table('personal', function (Blueprint $table) {
                $table->foreign('rol_id', 'fk_personal_roles')->references('id_rol')->on('roles')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('ruta_parada'))->contains('name', 'fk_ruta_parada_paradas')) {
            Schema::table('ruta_parada', function (Blueprint $table) {
                $table->foreign('parada_id', 'fk_ruta_parada_paradas')->references('id_parada')->on('paradas')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('ruta_parada'))->contains('name', 'fk_ruta_parada_rutas')) {
            Schema::table('ruta_parada', function (Blueprint $table) {
                $table->foreign('ruta_id', 'fk_ruta_parada_rutas')->references('id_ruta')->on('rutas')->onDelete('cascade')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('salidas'))->contains('name', 'fk_salidas_choferes')) {
            Schema::table('salidas', function (Blueprint $table) {
                $table->foreign('chofer_id', 'fk_salidas_choferes')->references('id_chofer')->on('choferes')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('salidas'))->contains('name', 'fk_salidas_horarios')) {
            Schema::table('salidas', function (Blueprint $table) {
                $table->foreign('horario_id', 'fk_salidas_horarios')->references('id_horario')->on('horarios')->onUpdate('cascade');
            });
        }
        if (!collect(Schema::getForeignKeys('salidas'))->contains('name', 'fk_salidas_unidades')) {
            Schema::table('salidas', function (Blueprint $table) {
                $table->foreign('unidad_id', 'fk_salidas_unidades')->references('id_unidad')->on('unidades')->onUpdate('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('salidas', fn (Blueprint $table) => $table->dropForeign('fk_salidas_unidades'));
        Schema::table('salidas', fn (Blueprint $table) => $table->dropForeign('fk_salidas_horarios'));
        Schema::table('salidas', fn (Blueprint $table) => $table->dropForeign('fk_salidas_choferes'));
        Schema::table('ruta_parada', fn (Blueprint $table) => $table->dropForeign('fk_ruta_parada_rutas'));
        Schema::table('ruta_parada', fn (Blueprint $table) => $table->dropForeign('fk_ruta_parada_paradas'));
        Schema::table('personal', fn (Blueprint $table) => $table->dropForeign('fk_personal_roles'));
        Schema::table('notificaciones', fn (Blueprint $table) => $table->dropForeign('fk_notificaciones_personal'));
        Schema::table('notificaciones', fn (Blueprint $table) => $table->dropForeign('fk_notificaciones_mantenimientos'));
        Schema::table('notificaciones', fn (Blueprint $table) => $table->dropForeign('fk_notificaciones_incidencias'));
        Schema::table('movimientos_inventario', fn (Blueprint $table) => $table->dropForeign('fk_movimientos_piezas'));
        Schema::table('movimientos_inventario', fn (Blueprint $table) => $table->dropForeign('fk_movimientos_personal'));
        Schema::table('movimientos_inventario', fn (Blueprint $table) => $table->dropForeign('fk_movimientos_mantenimientos'));
        Schema::table('mantenimiento_mecanico', fn (Blueprint $table) => $table->dropForeign('fk_mm_personal'));
        Schema::table('mantenimiento_mecanico', fn (Blueprint $table) => $table->dropForeign('fk_mm_mantenimientos'));
        Schema::table('mantenimientos', fn (Blueprint $table) => $table->dropForeign('fk_mantenimientos_unidades'));
        Schema::table('mantenimientos', fn (Blueprint $table) => $table->dropForeign('fk_mantenimientos_incidencias'));
        Schema::table('inventario', fn (Blueprint $table) => $table->dropForeign('fk_inventario_piezas'));
        Schema::table('incidencias', fn (Blueprint $table) => $table->dropForeign('fk_incidencias_unidades'));
        Schema::table('incidencias', fn (Blueprint $table) => $table->dropForeign('fk_incidencias_personal'));
        Schema::table('horarios', fn (Blueprint $table) => $table->dropForeign('fk_horarios_rutas'));
        Schema::table('choferes', fn (Blueprint $table) => $table->dropForeign('fk_choferes_personal'));
    }
};
