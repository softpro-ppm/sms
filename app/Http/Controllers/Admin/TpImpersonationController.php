<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TpImpersonationController extends Controller
{
    /**
     * Super Admin only: sign in as an active centre admin for this training partner.
     */
    public function start(Request $request, TrainingPartner $trainingPartner)
    {
        if (! $request->user()->is_super_admin) {
            abort(403);
        }

        if (! in_array($trainingPartner->status, ['active', 'suspended'], true)) {
            return redirect()
                ->back()
                ->with('error', 'You can only open centres that are active or suspended.');
        }

        if ($request->session()->has('impersonation')) {
            return redirect()
                ->back()
                ->with('error', 'You are already viewing as a centre. Use “Return to Super Admin” first.');
        }

        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $userId = $validated['user_id'] ?? null;

        if ($userId !== null) {
            $target = User::query()
                ->where('id', $userId)
                ->where('training_partner_id', $trainingPartner->id)
                ->where('role', 'admin')
                ->where('is_active', true)
                ->first();
        } else {
            $target = User::query()
                ->where('training_partner_id', $trainingPartner->id)
                ->where('role', 'admin')
                ->where('is_active', true)
                ->orderBy('id')
                ->first();
        }

        if (! $target) {
            return redirect()
                ->back()
                ->with('error', 'No active centre admin found for this partner. Add staff from this partner’s page first.');
        }

        $superAdminId = (int) $request->user()->id;

        $auditId = null;
        if (DB::getSchemaBuilder()->hasTable('impersonation_audit_logs')) {
            $auditId = DB::table('impersonation_audit_logs')->insertGetId([
                'super_admin_user_id' => $superAdminId,
                'target_user_id' => $target->id,
                'training_partner_id' => $trainingPartner->id,
                'started_at' => now(),
                'ended_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $request->session()->put('impersonation', [
            'original_user_id' => $superAdminId,
            'training_partner_id' => $trainingPartner->id,
            'training_partner_name' => $trainingPartner->name,
            'target_user_id' => $target->id,
            'audit_id' => $auditId,
        ]);

        Auth::login($target);
        $request->session()->regenerate();

        Log::info('tp_impersonation.start', [
            'super_admin_id' => $superAdminId,
            'target_user_id' => $target->id,
            'training_partner_id' => $trainingPartner->id,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "You are viewing {$trainingPartner->name} as {$target->name}. Use the banner to return when done.");
    }

    /**
     * End TP impersonation and restore the Super Admin session.
     */
    public function leave(Request $request)
    {
        $imp = $request->session()->get('impersonation');

        if (! is_array($imp) || empty($imp['original_user_id']) || empty($imp['target_user_id'])) {
            return redirect()->route('admin.dashboard');
        }

        if ((int) $imp['target_user_id'] !== (int) $request->user()->id) {
            abort(403, 'Invalid impersonation session.');
        }

        $original = User::find($imp['original_user_id']);
        if (! $original || ! $original->is_super_admin) {
            $request->session()->forget('impersonation');
            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Your Super Admin session is no longer valid. Please sign in again.');
        }

        $tpId = $imp['training_partner_id'] ?? null;
        $auditId = $imp['audit_id'] ?? null;
        if ($auditId && DB::getSchemaBuilder()->hasTable('impersonation_audit_logs')) {
            DB::table('impersonation_audit_logs')
                ->where('id', (int) $auditId)
                ->update(['ended_at' => now(), 'updated_at' => now()]);
        }
        $request->session()->forget('impersonation');
        Auth::login($original);
        $request->session()->regenerate();

        Log::info('tp_impersonation.end', [
            'super_admin_id' => $original->id,
            'training_partner_id' => $tpId,
        ]);

        if ($tpId) {
            return redirect()
                ->route('admin.super.training-partners.show', $tpId)
                ->with('success', 'Returned to Super Admin.');
        }

        return redirect()
            ->route('admin.super.dashboard')
            ->with('success', 'Returned to Super Admin.');
    }
}
