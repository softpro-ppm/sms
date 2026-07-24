@extends('layouts.admin')

@section('title', 'How the process works')

@section('content')
<div class="p-6 max-w-4xl">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">How the process works</h1>
    <p class="text-gray-600 mb-8">A simple guide for centre staff. Share this with new team members.</p>

    <div class="space-y-8">
        <section class="bg-white rounded-xl shadow border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-800 text-sm font-bold">1</span>
                Student joins
            </h2>
            <ul class="list-disc list-inside text-gray-700 space-y-2 text-sm leading-relaxed">
                <li><strong>Registers online:</strong> Account stays inactive until a staff member <strong>approves</strong> the student and a <strong>photo</strong> is on file. Centres can share a link like <code class="bg-gray-100 px-1 rounded">/register?partner=PARTNERCODE</code> so the student is assigned to that centre.</li>
                <li><strong>First login after approval:</strong> Self-registered students are asked to <strong>set a new password</strong> (they initially use their WhatsApp number as the temporary password).</li>
                <li><strong>Created at the centre:</strong> Student is usually approved immediately; documents are collected during registration.</li>
            </ul>
        </section>

        <section class="bg-white rounded-xl shadow border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-800 text-sm font-bold">2</span>
                Enrollment &amp; fees
            </h2>
            <ul class="list-disc list-inside text-gray-700 space-y-2 text-sm leading-relaxed">
                <li>After approval, the student is <strong>enrolled in a batch</strong>. Fee totals come from the batch/course (or special legacy flow for head office).</li>
                <li>Payments are <strong>recorded</strong> in the system. <strong>Only a centre admin</strong> (not reception) can <strong>approve or reject</strong> a payment.</li>
                <li>When fees are fully paid and rules are met, the student becomes <strong>eligible for the online exam</strong> (except legacy completions, which follow a different path).</li>
            </ul>
        </section>

        <section class="bg-white rounded-xl shadow border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-800 text-sm font-bold">3</span>
                Learning &amp; exam
            </h2>
            <ul class="list-disc list-inside text-gray-700 space-y-2 text-sm leading-relaxed">
                <li>Students complete <strong>online lessons</strong> when the course has them.</li>
                <li>The exam unlocks after <strong>batch end date</strong>, <strong>full payment</strong>, and institute eligibility for normal enrollments.</li>
            </ul>
        </section>

        <section class="bg-white rounded-xl shadow border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-800 text-sm font-bold">4</span>
                Certificate &amp; verification
            </h2>
            <ul class="list-disc list-inside text-gray-700 space-y-2 text-sm leading-relaxed">
                <li>After a <strong>passing</strong> exam result (or the approved legacy path), a <strong>certificate</strong> can be issued.</li>
                <li>Anyone can verify using the <strong>public verify</strong> link or the code on the certificate.</li>
            </ul>
        </section>

        <section class="bg-slate-50 border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-2">Future roadmap (not in the app yet)</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                Larger items such as <strong>percentage commission on student fees</strong>, multi-branch hierarchies, and automated fee-due reminders are planned as a later phase when finance and operations sign off on the rules.
            </p>
        </section>

        <section class="bg-amber-50 border border-amber-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-amber-900 mb-2">Training partner wallet (standard centres)</h2>
            <p class="text-sm text-amber-900 leading-relaxed">
                The platform uses a <strong>prepaid wallet</strong>. A fixed amount can be charged when each student is <strong>approved</strong> (or created, depending on how they joined).
                This is <strong>not</strong> the same as taking a percentage of every fee payment. Head office tops up the wallet when needed.
            </p>
        </section>
    </div>
</div>
@endsection
