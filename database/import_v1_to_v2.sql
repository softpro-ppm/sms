-- Import v1 (v1_smis) data into v2student
-- Assumes both databases exist on same MySQL server

USE v2student;

SET @now = NOW();

-- Clean up previous import helpers
DROP TABLE IF EXISTS import_course_map;
DROP TABLE IF EXISTS import_batch_map;
DROP TABLE IF EXISTS import_candidates;
DROP TABLE IF EXISTS import_candidate_map;
DROP TABLE IF EXISTS import_payments;
DROP TABLE IF EXISTS import_candidate_enrollment;

-- -----------------------------
-- Courses (from v1 jobroll)
-- -----------------------------
INSERT INTO courses (name, description, course_fee, registration_fee, assessment_fee, is_active, created_at, updated_at)
SELECT
    j.jobrollname,
    CONCAT('v1_jobroll_id:', j.JobrollId),
    COALESCE(j.payment, 0),
    100,
    100,
    1,
    @now,
    @now
FROM v1_smis.tbljobroll j;

-- Default course for batches without jobroll
INSERT INTO courses (name, description, course_fee, registration_fee, assessment_fee, is_active, created_at, updated_at)
SELECT 'Unknown Course', 'v1_jobroll_id:0', 0, 100, 100, 1, @now, @now
WHERE NOT EXISTS (
    SELECT 1 FROM courses WHERE description = 'v1_jobroll_id:0'
);

CREATE TABLE import_course_map (
    v1_jobroll_id INT PRIMARY KEY,
    v2_course_id BIGINT UNSIGNED NOT NULL
);

INSERT INTO import_course_map (v1_jobroll_id, v2_course_id)
SELECT j.JobrollId, c.id
FROM v1_smis.tbljobroll j
JOIN courses c ON c.description = CONCAT('v1_jobroll_id:', j.JobrollId);

-- -----------------------------
-- Batches (from v1 tblbatch)
-- -----------------------------
INSERT INTO batches (course_id, batch_name, start_date, end_date, max_students, is_active, created_at, updated_at)
SELECT
    COALESCE(cm.v2_course_id, (SELECT id FROM courses WHERE description = 'v1_jobroll_id:0' LIMIT 1)),
    b.batch_name,
    b.start_date,
    b.end_date,
    NULL,
    1,
    @now,
    @now
FROM v1_smis.tblbatch b
LEFT JOIN import_course_map cm ON cm.v1_jobroll_id = b.job_roll_id;

CREATE TABLE import_batch_map (
    v1_batch_id INT PRIMARY KEY,
    v2_batch_id BIGINT UNSIGNED NOT NULL
);

INSERT INTO import_batch_map (v1_batch_id, v2_batch_id)
SELECT b.id, vb.id
FROM v1_smis.tblbatch b
JOIN batches vb
  ON vb.batch_name = b.batch_name
 AND vb.start_date <=> b.start_date
 AND vb.end_date <=> b.end_date;

-- -----------------------------
-- Students (from v1 tblcandidate)
-- -----------------------------
CREATE TABLE import_candidates AS
SELECT
    c.CandidateId AS v1_candidate_id,
    -- De-dup Aadhar
    CASE
        WHEN c.aadharnumber IS NULL OR c.aadharnumber = 0 THEN CONCAT('9', LPAD(c.CandidateId, 11, '0'))
        WHEN ROW_NUMBER() OVER (PARTITION BY c.aadharnumber ORDER BY c.CandidateId) = 1 THEN LPAD(c.aadharnumber, 12, '0')
        ELSE CONCAT(
            LEFT(LPAD(c.aadharnumber, 12, '0'), 10),
            LPAD(ROW_NUMBER() OVER (PARTITION BY c.aadharnumber ORDER BY c.CandidateId), 2, '0')
        )
    END AS aadhar_number,
    -- De-dup email
    CASE
        WHEN c.email IS NULL OR TRIM(c.email) = '' THEN CONCAT('cand', c.CandidateId, '@import.local')
        WHEN ROW_NUMBER() OVER (PARTITION BY LOWER(TRIM(c.email)) ORDER BY c.CandidateId) = 1 THEN LOWER(TRIM(c.email))
        ELSE CONCAT('cand', c.CandidateId, '@import.local')
    END AS email,
    c.candidatename AS full_name,
    c.qualification AS qualification,
    -- Phone / WhatsApp
    CASE
        WHEN c.phonenumber IS NULL OR c.phonenumber = 0 THEN CONCAT('9', LPAD(c.CandidateId, 9, '0'))
        ELSE LPAD(c.phonenumber, 10, '0')
    END AS phone,
    CASE
        WHEN c.phonenumber IS NULL OR c.phonenumber = 0 THEN CONCAT('9', LPAD(c.CandidateId, 9, '0'))
        ELSE LPAD(c.phonenumber, 10, '0')
    END AS whatsapp_number,
    c.dateofbirth AS date_of_birth,
    CASE
        WHEN LOWER(c.gender) IN ('male','m') THEN 'male'
        WHEN LOWER(c.gender) IN ('female','f') THEN 'female'
        ELSE 'other'
    END AS gender,
    NULLIF(CONCAT_WS(', ', NULLIF(c.village,''), NULLIF(c.mandal,''), NULLIF(c.district,'')), '') AS address,
    c.district AS city,
    c.state AS state,
    c.pincode AS pincode,
    'approved' AS status,
    1 AS is_active,
    c.DateCreated AS created_at,
    c.DateModified AS updated_at
FROM v1_smis.tblcandidate c;

INSERT INTO students (
    aadhar_number, full_name, qualification, email, phone, whatsapp_number,
    date_of_birth, gender, address, city, state, pincode,
    status, is_active, approved_at, created_at, updated_at
)
SELECT
    ic.aadhar_number, ic.full_name, ic.qualification, ic.email, ic.phone, ic.whatsapp_number,
    ic.date_of_birth, ic.gender, ic.address, ic.city, ic.state, ic.pincode,
    ic.status, ic.is_active, @now, ic.created_at, ic.updated_at
FROM import_candidates ic;

CREATE TABLE import_candidate_map (
    v1_candidate_id INT PRIMARY KEY,
    v2_student_id BIGINT UNSIGNED NOT NULL
);

INSERT INTO import_candidate_map (v1_candidate_id, v2_student_id)
SELECT ic.v1_candidate_id, s.id
FROM import_candidates ic
JOIN students s ON s.aadhar_number = ic.aadhar_number;

-- -----------------------------
-- Payments summary (from v1 payment)
-- -----------------------------
CREATE TABLE import_payments AS
SELECT
    p.candidate_id,
    MAX(p.total_fee) AS total_fee,
    MAX(p.paid) AS paid_amount,
    MAX(p.balance) AS balance,
    MAX(p.status) AS status
FROM v1_smis.payment p
GROUP BY p.candidate_id;

-- -----------------------------
-- Enrollments (from v1 tblbatch_candidate)
-- -----------------------------
INSERT INTO enrollments (
    enrollment_number, student_id, batch_id, enrollment_date, status,
    total_fee, paid_amount, outstanding_amount, registration_fee, course_fee, assessment_fee,
    is_eligible_for_assessment, created_at, updated_at
)
SELECT
    CONCAT('IMP-', bc.candidate_id, '-', bc.batch_id),
    cm.v2_student_id,
    bm.v2_batch_id,
    DATE(bc.date),
    'active',
    COALESCE(p.total_fee, (c.course_fee + 200)),
    COALESCE(p.paid_amount, 0),
    COALESCE(p.total_fee, (c.course_fee + 200)) - COALESCE(p.paid_amount, 0),
    100,
    GREATEST(COALESCE(p.total_fee, (c.course_fee + 200)) - 200, 0),
    100,
    CASE WHEN (COALESCE(p.total_fee, (c.course_fee + 200)) - COALESCE(p.paid_amount, 0)) <= 0 THEN 1 ELSE 0 END,
    @now,
    @now
FROM v1_smis.tblbatch_candidate bc
JOIN import_candidate_map cm ON cm.v1_candidate_id = bc.candidate_id
JOIN import_batch_map bm ON bm.v1_batch_id = bc.batch_id
JOIN batches vb ON vb.id = bm.v2_batch_id
JOIN courses c ON c.id = vb.course_id
LEFT JOIN import_payments p ON p.candidate_id = bc.candidate_id;

-- -----------------------------
-- Payments (one record per candidate with paid_amount > 0)
-- -----------------------------
CREATE TABLE import_candidate_enrollment AS
SELECT cm.v1_candidate_id, MIN(e.id) AS enrollment_id, MIN(e.student_id) AS student_id
FROM enrollments e
JOIN import_candidate_map cm ON cm.v2_student_id = e.student_id
GROUP BY cm.v1_candidate_id;

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

