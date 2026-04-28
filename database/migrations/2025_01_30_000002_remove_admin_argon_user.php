<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eliminar usuario admin@argon.com y reasignar referencias al primer administrador (Martinez).
     */
    public function up(): void
    {
        foreach (['users', 'roles', 'role_user', 'reparaciones', 'estados_reparacion', 'notas_reparacion'] as $table) {
            if (!Schema::hasTable($table)) {
                return;
            }
        }

        $argon = DB::table('users')->where('email', 'admin@argon.com')->first();
        if (!$argon) {
            return;
        }
        $argonId = $argon->id;

        // Primer usuario con rol administrador (típicamente martinez@martinezservice.com)
        $adminRoleId = DB::table('roles')->where('slug', 'administrador')->value('id');
        $newAdminId = null;
        if ($adminRoleId) {
            $newAdminId = DB::table('role_user')
                ->where('role_id', $adminRoleId)
                ->where('user_id', '!=', $argonId)
                ->value('user_id');
        }
        if (!$newAdminId) {
            $newAdminId = DB::table('users')->where('id', '!=', $argonId)->value('id');
        }

        if ($newAdminId) {
            DB::table('reparaciones')->where('recepcionista_id', $argonId)->update(['recepcionista_id' => $newAdminId]);
            DB::table('reparaciones')->where('tecnico_id', $argonId)->update(['tecnico_id' => $newAdminId]);
            DB::table('reparaciones')->where('tecnico_completo_id', $argonId)->update(['tecnico_completo_id' => $newAdminId]);
            DB::table('estados_reparacion')->where('usuario_id', $argonId)->update(['usuario_id' => $newAdminId]);
            DB::table('notas_reparacion')->where('usuario_id', $argonId)->update(['usuario_id' => $newAdminId]);
        } else {
            DB::table('reparaciones')->where('recepcionista_id', $argonId)->update(['recepcionista_id' => null]);
            DB::table('reparaciones')->where('tecnico_id', $argonId)->update(['tecnico_id' => null]);
            DB::table('reparaciones')->where('tecnico_completo_id', $argonId)->update(['tecnico_completo_id' => null]);
            DB::table('estados_reparacion')->where('usuario_id', $argonId)->update(['usuario_id' => null]);
            DB::table('notas_reparacion')->where('usuario_id', $argonId)->update(['usuario_id' => null]);
        }

        DB::table('role_user')->where('user_id', $argonId)->delete();
        DB::table('users')->where('id', $argonId)->delete();
    }

    public function down(): void
    {
        // No se restaura el usuario argon por seguridad
    }
};
