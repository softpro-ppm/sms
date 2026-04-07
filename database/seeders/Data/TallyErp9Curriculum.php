<?php

namespace Database\Seeders\Data;

/**
 * Tally ERP 9 LMS outline: modules and lesson bodies for institute self-paced learning.
 * Pair with TallyErp9CourseSeeder (course name "Tally ERP 9 Advanced").
 */
class TallyErp9Curriculum
{
    /**
     * @return array<int, array{title: string, summary: string, lessons: array<int, array<string, mixed>>}>
     */
    public static function modules(): array
    {
        return [
            [
                'title' => 'Introduction to Tally ERP 9',
                'summary' => 'What Tally is, the Gateway screen, navigation, and safe practice habits.',
                'lessons' => [
                    [
                        'title' => 'What is Tally ERP 9 and who uses it?',
                        'minutes' => 12,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Tally ERP 9</strong> is accounting and inventory software widely used by small and medium businesses in India. It helps you record vouchers, manage GST, stock, payroll, and produce statutory reports.</p>
<ul>
<li><strong>Users:</strong> Accountants, bookkeepers, business owners, and students preparing for commerce and office jobs.</li>
<li><strong>Modes:</strong> You typically work in a <em>company</em> data file; each company has its own books.</li>
<li><strong>Practice:</strong> Always use a <strong>practice company</strong> or backup before experimenting on live data.</li>
</ul>
<p>Learning goal: move confidently from the Gateway to masters, vouchers, and reports.</p>
HTML,
                    ],
                    [
                        'title' => 'Gateway of Tally — menus and shortcuts',
                        'minutes' => 15,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>When you open Tally, you see the <strong>Gateway of Tally</strong> — the main menu.</p>
<ul>
<li><strong>Masters:</strong> Create and alter groups, ledgers, stock items, and other masters.</li>
<li><strong>Transactions:</strong> Accounting vouchers, inventory vouchers, and order processing.</li>
<li><strong>Utilities / Company:</strong> Create company, change period, split data, backup, restore.</li>
<li><strong>Display / Statements:</strong> Trial balance, P&amp;L, balance sheet, GST reports, and more.</li>
</ul>
<p><strong>Tips:</strong> Use <kbd>Esc</kbd> to go back one level. <kbd>Alt</kbd> + underlined letter often opens a menu. Read the bottom bar for context keys.</p>
HTML,
                    ],
                    [
                        'title' => 'Versions, licensing, and data safety',
                        'minutes' => 10,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>Institutes may use educational or licensed copies. Regardless of edition, <strong>your skills</strong> transfer: vouchers, GST setup, and reports follow the same logic.</p>
<ul>
<li><strong>Backup:</strong> Regular backups (company folder or built-in backup) prevent data loss.</li>
<li><strong>Financial year:</strong> Books are kept per financial year; know your company’s year start.</li>
<li><strong>Security:</strong> Use passwords and controlled access where your organisation requires it.</li>
</ul>
<p>Before class exercises: confirm you can open your practice company and return to the Gateway.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Company creation and configuration',
                'summary' => 'Create a company, set financial year, enable features (F11), and base currency.',
                'lessons' => [
                    [
                        'title' => 'Creating a new company (Alt + F3)',
                        'minutes' => 18,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>Use <strong>Company Info → Create Company</strong> (or <kbd>Alt</kbd> + <kbd>F3</kbd> from Gateway).</p>
<ol>
<li>Enter <strong>name</strong>, mailing name, and address.</li>
<li>Set <strong>financial year beginning</strong> and books beginning date.</li>
<li>Choose <strong>base currency</strong> (e.g. INR).</li>
<li>Enable statutory features as needed (maintain payroll, GST, etc.) — you can refine later in F11.</li>
</ol>
<p>After creation, <strong>select</strong> the company from the list to work inside it.</p>
HTML,
                    ],
                    [
                        'title' => 'F12 configuration and F11 features',
                        'minutes' => 15,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>F11: Features</strong> turns modules on or off: accounting, inventory, invoicing, GST, payroll, and more. Enable only what you need to keep menus simple.</p>
<p><strong>F12: Configure</strong> controls behaviour such as date blocks, voucher numbering, and display options. Your centre may use a standard configuration sheet — follow it for exercises.</p>
<ul>
<li>Inventory + accounting together is common for trading businesses.</li>
<li>GST must be configured with registration details and tax masters for compliant returns.</li>
</ul>
HTML,
                    ],
                    [
                        'title' => 'Alter company and security basics',
                        'minutes' => 12,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Alter</strong> company from Gateway → Company Info to change address, financial year impact (careful!), or contact details.</p>
<ul>
<li><strong>Split financial year</strong> and advanced tools are for experienced users — avoid on live data without supervision.</li>
<li><strong>Password / control:</strong> If enabled, only authorised users alter masters or sensitive reports.</li>
</ul>
<p>Checkpoint: you can create, select, and alter a practice company.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Chart of accounts — groups and ledgers',
                'summary' => 'Primary groups, sub-groups, and ledger creation under correct heads.',
                'lessons' => [
                    [
                        'title' => 'Groups in Tally (Balance Sheet vs Profit & Loss)',
                        'minutes' => 16,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>Tally ships with a <strong>predefined chart</strong>: Capital, Current Assets, Current Liabilities, Direct Expenses, Indirect Expenses, Sales, Purchase, and more.</p>
<ul>
<li><strong>Assets & liabilities</strong> appear on the Balance Sheet.</li>
<li><strong>Income & expenses</strong> flow to Profit &amp; Loss.</li>
<li>Create <strong>sub-groups</strong> under primary groups for clarity (e.g. Bank Accounts under Current Assets).</li>
</ul>
<p>Wrong group = wrong financial statement classification. When in doubt, match similar ledgers your trainer shows.</p>
HTML,
                    ],
                    [
                        'title' => 'Creating and altering ledgers',
                        'minutes' => 20,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Ledgers</strong> are individual accounts: Cash, HDFC Bank, Rent, Sales Local, etc.</p>
<ol>
<li>Gateway → <strong>Masters → Ledgers → Create</strong>.</li>
<li>Enter name and select the <strong>group</strong>.</li>
<li>For bank ledgers, you can fill IFSC, account number (optional fields depend on version/config).</li>
<li>For parties (debtors/creditors), use <strong>Sundry Debtors</strong> or <strong>Sundry Creditors</strong> unless your chart uses sub-groups.</li>
</ol>
<p><strong>Alter</strong> to rename or move groups carefully — historical vouchers stay linked to the same ledger.</p>
HTML,
                    ],
                    [
                        'title' => 'Bill-wise details and cost centres (overview)',
                        'minutes' => 14,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Bill-wise details</strong> track invoices and references for receivables/payables — essential for matching payments to bills.</p>
<p><strong>Cost centres / categories</strong> allocate expenses or revenue to departments or projects when enabled in F11.</p>
<p>For beginners: master simple ledgers first; then enable bill-wise on party ledgers when doing credit sales or purchases.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Accounting vouchers',
                'summary' => 'Contra, Payment, Receipt, Journal, Purchase, Sales — when to use each.',
                'lessons' => [
                    [
                        'title' => 'Contra, Payment, and Receipt vouchers',
                        'minutes' => 22,
                        'type' => 'article',
                        'body' => <<<'HTML'
<ul>
<li><strong>Contra (F4):</strong> Cash ↔ Bank movements only (withdrawal, deposit, transfer between own bank accounts).</li>
<li><strong>Payment (F5):</strong> Money going out — expenses, supplier payments, drawings (as per your chart).</li>
<li><strong>Receipt (F6):</strong> Money coming in — customer receipts, income, capital introduced.</li>
</ul>
<p>Always check <strong>date</strong>, <strong>debit/credit</strong> ledgers, and narration. Use <kbd>Enter</kbd> to move between fields; <kbd>Ctrl</kbd> + <kbd>A</kbd> often accepts the voucher.</p>
HTML,
                    ],
                    [
                        'title' => 'Journal voucher (F7) for adjustments',
                        'minutes' => 18,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Journal</strong> records non-cash adjustments: depreciation, provisions, reclassifications, and transfers between ledgers without bank/cash.</p>
<ul>
<li>Total debits must equal total credits.</li>
<li>Use clear <strong>narration</strong> so auditors and future you understand the entry.</li>
</ul>
<p>Common student mistake: using Journal for bank payments — use <strong>Payment</strong> instead.</p>
HTML,
                    ],
                    [
                        'title' => 'Purchase (F9) and Sales (F8) vouchers',
                        'minutes' => 24,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Sales</strong> and <strong>Purchase</strong> vouchers record trading transactions and, when inventory is on, can update stock with proper item and godown details.</p>
<ul>
<li>Link party ledger (debtor/creditor) and stock items or ledger amounts as per your exercise.</li>
<li>With <strong>GST</strong>, tax ledgers and rates appear based on item/ledger configuration.</li>
</ul>
<p>Practice: one credit sale with GST and one credit purchase matching your institute’s sample scenario.</p>
HTML,
                    ],
                    [
                        'title' => 'Optional: Debit / Credit notes',
                        'minutes' => 12,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Debit Note</strong> and <strong>Credit Note</strong> handle returns, rate differences, and post-sale adjustments. Availability depends on features enabled.</p>
<p>They must follow your company’s GST and accounting policy — always mirror the underlying invoice and tax treatment your trainer specifies.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'GST in Tally ERP 9 (essentials)',
                'summary' => 'Registration, tax ledgers, HSN/SAC, and common return concepts.',
                'lessons' => [
                    [
                        'title' => 'Enabling GST and company registration details',
                        'minutes' => 16,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>Enable GST in <strong>F11: Statutory features</strong> and fill <strong>GST registration</strong> details for the company (GSTIN, state, registration type).</p>
<ul>
<li>Stock items and ledgers can carry <strong>HSN/SAC</strong> and tax rates.</li>
<li>Party masters should have correct GSTIN when dealing with registered vendors/customers.</li>
</ul>
<p>Rules change over time — use your trainer’s current checklist for rates and return forms.</p>
HTML,
                    ],
                    [
                        'title' => 'CGST, SGST, IGST — how vouchers pick tax',
                        'minutes' => 18,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>For domestic supplies, intra-state sales typically use <strong>CGST + SGST</strong>; inter-state often uses <strong>IGST</strong>. Tally calculates based on party state, company state, and item configuration.</p>
<ul>
<li>Ensure <strong>tax ledgers</strong> exist and are classified correctly.</li>
<li>Verify voucher-wise tax before filing — use GST reports in <strong>Display → Statutory Reports → GST</strong>.</li>
</ul>
HTML,
                    ],
                    [
                        'title' => 'GSTR overview (conceptual)',
                        'minutes' => 12,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>Reports such as <strong>GSTR-1</strong> (outward supplies) and <strong>GSTR-2</strong> / purchase summaries help reconcile with the portal. Exact menu names may vary slightly by release and statutory pack.</p>
<p>Learning focus: <strong>clean voucher entry</strong> and correct tax flags — reports are only as good as the data entered.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Inventory and godowns',
                'summary' => 'Stock groups, items, units, godowns, and stock vouchers.',
                'lessons' => [
                    [
                        'title' => 'Stock groups, categories, and units of measure',
                        'minutes' => 16,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>Enable <strong>Maintain inventory</strong> in F11. Then create <strong>stock groups</strong> (e.g. Electronics, Grocery), <strong>units</strong> (Nos, Kg, Ltr), and <strong>stock items</strong> with rates and GST details as needed.</p>
<ul>
<li><strong>Categories</strong> add another dimension for classification if used.</li>
<li>Opening balances can be entered when creating items or via stock journals as per process.</li>
</ul>
HTML,
                    ],
                    [
                        'title' => 'Godowns and stock movement',
                        'minutes' => 14,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Godowns</strong> represent warehouses or branches. Each receipt or issue can specify godown so <strong>stock balances</strong> are location-wise.</p>
<p>Use <strong>Delivery Note</strong>, <strong>Receipt Note</strong>, or integrated sales/purchase vouchers depending on your workflow. Your institute’s case study will define which voucher type to practise.</p>
HTML,
                    ],
                    [
                        'title' => 'Stock summary and valuation',
                        'minutes' => 12,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Display → Inventory Books → Stock Summary</strong> shows quantities and values. Valuation method (FIFO, average, etc.) depends on company settings.</p>
<p>Reconcile physical stock periodically; post <strong>stock journals</strong> or adjustments only with proper approval in real businesses.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Banking, reconciliation, and utilities',
                'summary' => 'Bank ledgers, reconciliation, backup, and split year (awareness).',
                'lessons' => [
                    [
                        'title' => 'Bank reconciliation (BRS) basics',
                        'minutes' => 18,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Bank reconciliation</strong> matches your bank ledger in Tally with the bank statement: uncleared cheques, bank charges, direct credits.</p>
<ul>
<li>Enter <strong>instrument dates</strong> and references in payment/receipt vouchers where practised.</li>
<li>Use the reconciliation screen to mark transactions as cleared.</li>
</ul>
<p>Goal: the adjusted book balance agrees with the statement for the same date.</p>
HTML,
                    ],
                    [
                        'title' => 'Backup, restore, and repair (essentials)',
                        'minutes' => 12,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Backup</strong> from Gateway → company utilities before major changes or year-end. Store copies off the same PC when possible.</p>
<p><strong>Restore</strong> replaces data — use only on practice files or with explicit recovery procedures.</p>
<p>If Tally suggests <strong>rewrite</strong> or data errors appear, stop and involve an expert on live data.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Reports, MIS, and period-end',
                'summary' => 'Day book, trial balance, P&L, balance sheet, and year-end awareness.',
                'lessons' => [
                    [
                        'title' => 'Day Book and Ledger scrutiny',
                        'minutes' => 14,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p><strong>Day Book</strong> lists vouchers by date — quick way to verify entries and find mistakes.</p>
<p>Drill down from any report into <strong>voucher alter</strong> (with permissions) to correct errors; some changes may affect GST already filed — policy matters in real life.</p>
HTML,
                    ],
                    [
                        'title' => 'Trial Balance, Profit & Loss, Balance Sheet',
                        'minutes' => 20,
                        'type' => 'article',
                        'body' => <<<'HTML'
<ul>
<li><strong>Trial Balance:</strong> All ledger balances — debits must equal credits if books are complete.</li>
<li><strong>Profit &amp; Loss:</strong> Income minus expenses for the period.</li>
<li><strong>Balance Sheet:</strong> Assets, liabilities, and capital snapshot.</li>
</ul>
<p>Change <strong>period</strong> (F2: Period) to view monthly or yearly figures. Export options help share with Excel/PDF where configured.</p>
HTML,
                    ],
                    [
                        'title' => 'Year-end closing — conceptual',
                        'minutes' => 10,
                        'type' => 'article',
                        'body' => <<<'HTML'
<p>Many businesses carry balances forward into a new year in the same company or through a controlled year-end process. Exact steps depend on Tally setup and auditor requirements.</p>
<p>Students: focus on <strong>accurate vouchers through year-end date</strong> and reading final reports; advanced closing is often a separate module.</p>
HTML,
                    ],
                ],
            ],
        ];
    }
}
