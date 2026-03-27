<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Course;

trait ScopesByTrainingPartner
{
    /**
     * Get training partner ID for current user. Admin/Reception always have one.
     */
    protected function getTrainingPartnerId(): ?int
    {
        return auth()->user()->training_partner_id;
    }

    /**
     * Apply TP scope to Student query when user is TP-scoped.
     */
    protected function scopeStudents($query)
    {
        $tpId = $this->getTrainingPartnerId();
        return $tpId !== null ? $query->where('training_partner_id', $tpId) : $query;
    }

    /**
     * Apply TP scope to Payment query (via student).
     */
    protected function scopePayments($query)
    {
        $tpId = $this->getTrainingPartnerId();
        return $tpId !== null
            ? $query->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))
            : $query;
    }

    /**
     * Apply TP scope to Enrollment query (via student).
     */
    protected function scopeEnrollments($query)
    {
        $tpId = $this->getTrainingPartnerId();
        return $tpId !== null
            ? $query->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))
            : $query;
    }

    /**
     * Apply TP scope to AssessmentResult query (via student).
     */
    protected function scopeAssessmentResults($query)
    {
        $tpId = $this->getTrainingPartnerId();
        return $tpId !== null
            ? $query->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))
            : $query;
    }

    /**
     * Apply TP scope to Certificate query (via student).
     */
    protected function scopeCertificates($query)
    {
        $tpId = $this->getTrainingPartnerId();
        return $tpId !== null
            ? $query->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))
            : $query;
    }

    /**
     * TP admins may only access courses owned by their partner (not legacy platform rows).
     */
    protected function ensureCourseAccessible(Course $course): void
    {
        $tpId = $this->getTrainingPartnerId();
        if ($tpId === null) {
            return;
        }
        if ($course->training_partner_id === null || (int) $course->training_partner_id !== $tpId) {
            abort(404);
        }
    }
}
