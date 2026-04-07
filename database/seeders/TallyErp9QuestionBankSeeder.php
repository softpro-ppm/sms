<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\QuestionBank;
use Illuminate\Database\Seeder;

class TallyErp9QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', ['tally erp 9 advanced'])
            ->orderByDesc('id')
            ->first();

        if (! $course) {
            $this->command?->error('Course "Tally ERP 9 Advanced" not found. Run TallyErp9CourseSeeder first (or create the course).');

            return;
        }

        $questions = [
            [
                'subject' => 'Introduction',
                'question_text' => 'What is the main screen called when you open Tally ERP 9?',
                'option_a' => 'Gateway of Tally',
                'option_b' => 'Control Panel',
                'option_c' => 'Dashboard only',
                'option_d' => 'Start Menu',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Introduction',
                'question_text' => 'Which key is commonly used to go back one level in Tally?',
                'option_a' => 'Esc',
                'option_b' => 'F1',
                'option_c' => 'Tab',
                'option_d' => 'Ctrl+Z',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Company',
                'question_text' => 'Which shortcut opens Create Company from Gateway?',
                'option_a' => 'Alt + F3',
                'option_b' => 'F11',
                'option_c' => 'F12',
                'option_d' => 'Ctrl + N',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Company',
                'question_text' => 'F11 in Tally is mainly used for:',
                'option_a' => 'Enabling features (accounting, inventory, GST, etc.)',
                'option_b' => 'Printing vouchers',
                'option_c' => 'Deleting the company',
                'option_d' => 'Changing screen resolution',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Ledgers',
                'question_text' => 'A customer who owes you money is usually recorded under which group?',
                'option_a' => 'Sundry Debtors',
                'option_b' => 'Sundry Creditors',
                'option_c' => 'Direct Expenses',
                'option_d' => 'Sales Account',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Ledgers',
                'question_text' => 'Bank account ledgers are typically placed under:',
                'option_a' => 'Bank Accounts (often under Current Assets)',
                'option_b' => 'Sales',
                'option_c' => 'Purchase',
                'option_d' => 'Capital Account only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Vouchers',
                'question_text' => 'Which voucher is used for cash deposit into bank or withdrawal?',
                'option_a' => 'Contra',
                'option_b' => 'Journal',
                'option_c' => 'Payment only',
                'option_d' => 'Receipt only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Vouchers',
                'question_text' => 'Payment voucher (F5) records:',
                'option_a' => 'Money going out of the business',
                'option_b' => 'Money coming in only',
                'option_c' => 'Stock transfer between godowns only',
                'option_d' => 'Year-end closing only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Vouchers',
                'question_text' => 'Journal voucher is most appropriate for:',
                'option_a' => 'Adjustments without cash/bank (e.g. depreciation)',
                'option_b' => 'Bank charges paid from bank',
                'option_c' => 'Customer receipt in cash',
                'option_d' => 'Cash sales at counter',
                'correct_answer' => 'A',
                'difficulty_level' => 'medium',
            ],
            [
                'subject' => 'Vouchers',
                'question_text' => 'Receipt voucher (F6) records:',
                'option_a' => 'Money coming into the business',
                'option_b' => 'Money going out',
                'option_c' => 'Only credit purchases',
                'option_d' => 'Only stock adjustments',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'GST',
                'question_text' => 'GST registration details for the company are typically maintained under:',
                'option_a' => 'Statutory / GST setup after enabling GST in features',
                'option_b' => 'Voucher type only',
                'option_c' => 'Windows Control Panel',
                'option_d' => 'Stock group only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'GST',
                'question_text' => 'For a typical intra-state taxable sale in India, which combination is common?',
                'option_a' => 'CGST + SGST',
                'option_b' => 'IGST only',
                'option_c' => 'Custom duty only',
                'option_d' => 'No tax',
                'correct_answer' => 'A',
                'difficulty_level' => 'medium',
            ],
            [
                'subject' => 'Inventory',
                'question_text' => 'Physical locations or warehouses for stock are called:',
                'option_a' => 'Godowns',
                'option_b' => 'Groups only',
                'option_c' => 'Vouchers',
                'option_d' => 'Cost categories only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Inventory',
                'question_text' => 'Stock items in Tally require at minimum:',
                'option_a' => 'Name, unit, and appropriate group (and configuration as per company)',
                'option_b' => 'Only a voucher number',
                'option_c' => 'Only GSTIN',
                'option_d' => 'Only payroll category',
                'correct_answer' => 'A',
                'difficulty_level' => 'medium',
            ],
            [
                'subject' => 'Banking',
                'question_text' => 'Bank reconciliation matches:',
                'option_a' => 'Tally bank ledger with the bank statement',
                'option_b' => 'Only sales invoices',
                'option_c' => 'Only payroll',
                'option_d' => 'Stock valuation only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Reports',
                'question_text' => 'Which report lists all vouchers date-wise for checking entries?',
                'option_a' => 'Day Book',
                'option_b' => 'Payroll attendance',
                'option_c' => 'Godown transfer only',
                'option_d' => 'Stock ageing only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Reports',
                'question_text' => 'In a complete double-entry set, Trial Balance totals should:',
                'option_a' => 'Have equal debit and credit totals',
                'option_b' => 'Always show zero',
                'option_c' => 'Match bank statement only',
                'option_d' => 'Ignore journal vouchers',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
            [
                'subject' => 'Reports',
                'question_text' => 'Profit & Loss Account shows:',
                'option_a' => 'Income and expenses for a period',
                'option_b' => 'Only fixed assets',
                'option_c' => 'Only bank balance',
                'option_d' => 'Only stock quantity',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
            ],
        ];

        $created = 0;
        foreach ($questions as $row) {
            QuestionBank::query()->create(array_merge([
                'course_id' => $course->id,
                'is_active' => true,
            ], $row));
            $created++;
        }

        $this->command?->info('Tally ERP 9 question bank: '.$created.' question(s) added for course id '.$course->id.'.');
    }
}
