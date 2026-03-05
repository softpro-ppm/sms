-- Rebuild enrollments/payments from v1 candidate batch fields
USE v2student;

SET @now = NOW();

-- Clear existing imports
DELETE FROM payment_allocations;
DELETE FROM payments;
DELETE FROM enrollments;

DROP TABLE IF EXISTS import_payments;
DROP TABLE IF EXISTS import_candidate_enrollment;

-- Payments summary from v1
CREATE TABLE import_payments AS
SELECT
    p.candidate_id,
    MAX(p.total_fee) AS total_fee,
    MAX(p.paid) AS paid_amount,
    MAX(p.balance) AS balance,
    MAX(p.status) AS status
FROM v1_smis.payment p
GROUP BY p.candidate_id;

-- Enrollments from tblcandidate.batch / tblbatch_id
INSERT INTO enrollments (
    enrollment_number, student_id, batch_id, enrollment_date, status,
    total_fee, paid_amount, outstanding_amount, registration_fee, course_fee, assessment_fee,
    is_eligible_for_assessment, created_at, updated_at
)
SELECT
    CONCAT('IMP-', c.CandidateId, '-', COALESCE(c.tblbatch_id, c.batch)),
    cm.v2_student_id,
    bm.v2_batch_id,
    DATE(c.DateCreated),
    'active',
    COALESCE(p.total_fee, (crs.course_fee + 200)),
    COALESCE(p.paid_amount, 0),
    COALESCE(p.total_fee, (crs.course_fee + 200)) - COALESCE(p.paid_amount, 0),
    100,
    GREATEST(COALESCE(p.total_fee, (crs.course_fee + 200)) - 200, 0),
    100,
    CASE WHEN (COALESCE(p.total_fee, (crs.course_fee + 200)) - COALESCE(p.paid_amount, 0)) <= 0 THEN 1 ELSE 0 END,
    @now,
    @now
FROM v1_smis.tblcandidate c
JOIN import_candidate_map cm ON cm.v1_candidate_id = c.CandidateId
JOIN import_batch_map bm ON bm.v1_batch_id = COALESCE(c.tblbatch_id, c.batch)
JOIN batches vb ON vb.id = bm.v2_batch_id
JOIN courses crs ON crs.id = vb.course_id
LEFT JOIN import_payments p ON p.candidate_id = c.CandidateId
WHERE COALESCE(c.tblbatch_id, c.batch) IS NOT NULL;

-- Create candidate -> enrollment map
CREATE TABLE import_candidate_enrollment AS
SELECT cm.v1_candidate_id, MIN(e.id) AS enrollment_id, MIN(e.student_id) AS student_id
FROM enrollments e
JOIN import_candidate_map cm ON cm.v2_student_id = e.student_id
GROUP BY cm.v1_candidate_id;

-- Payments (one record per candidate with paid_amount > 0)
INSERT INTO payments (
    student_id, enrollment_id, payment_receipt_number, amount, payment_type, status, remarks,
    created_at, updated_at
)
SELECT
    ce.student_id,
    ce.enrollment_id,
    CONCAT('IMP-', p.candidate_id),
    p.paid_amount,
    'partial',
    CASE
        WHEN p.status = 'Paid' THEN 'approved'
        WHEN p.status = 'Pending' THEN 'pending'
        ELSE 'pending'
    END,
    'Imported from v1',
    @now,
    @now
FROM import_payments p
JOIN import_candidate_enrollment ce ON ce.v1_candidate_id = p.candidate_id
WHERE p.paid_amount > 0;

