<?php

namespace Database\Seeders\Data;

/**
 * Full MS Office LMS curriculum: modules and HTML lesson bodies for SoftPro SMS.
 */
final class MSOfficeCurriculum
{
    /**
     * @return list<array{title: string, summary: string, lessons: list<array{title: string, body: string, minutes: int, type?: string, video_url?: string|null}>}>
     */
    public static function modules(): array
    {
        return [
            [
                'title' => 'Module 1 — Introduction to Microsoft Office',
                'summary' => 'Common skills for Word, Excel, and PowerPoint: interface, files, clipboard, and zoom.',
                'lessons' => [
                    [
                        'title' => '1.1 What is Microsoft Office? Word, Excel, PowerPoint',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1"><li>Name the role of Word, Excel, and PowerPoint in real office work</li><li>Open each application and identify the ribbon, document area, and status bar</li></ul>
<h3 class="text-lg font-semibold mb-2">Concepts</h3>
<p class="mb-3"><strong>Word</strong> is for <em>documents</em>: letters, applications, notices, reports. <strong>Excel</strong> is for <em>numbers and lists</em>: fees, mark sheets, stock, schedules. <strong>PowerPoint</strong> is for <em>slides</em>: presentations to teach or pitch.</p>
<h3 class="text-lg font-semibold mb-2">Hands-on (visuals to capture for your LMS)</h3>
<ol class="list-decimal pl-5 space-y-2 mb-3"><li>Press the Windows key and type <strong>Word</strong>. Open it. <strong>Visual:</strong> full window with Home tab ribbon highlighted.</li><li>Point to the <strong>ribbon</strong> (tabs: File, Home, Insert …). The large white area is your <strong>document</strong>.</li><li>Open <strong>Excel</strong>. <strong>Visual:</strong> columns A, B, C and row numbers; cell <strong>A1</strong> selected.</li><li>Open <strong>PowerPoint</strong>. <strong>Visual:</strong> slide thumbnails on the left, big slide on the right.</li></ol>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Open all three apps, type your name in each, then close without saving (discard) once to learn the close dialog.</p>
HTML,
                    ],
                    [
                        'title' => '1.2 New, Open, Save, Save As, and PDF',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Why this matters</h3>
<p class="mb-3">Losing work is painful. Always know <strong>where</strong> the file is saved (folder + filename).</p>
<h3 class="text-lg font-semibold mb-2">Steps in Word (same idea in Excel/PowerPoint)</h3>
<ol class="list-decimal pl-5 space-y-2 mb-3"><li><strong>File → Save As → Browse</strong>. Choose folder <em>MS_Office_Practice</em>. Name: <code>Lesson_1_2</code>. Click Save.</li><li><strong>Visual:</strong> Save As dialog showing path at top.</li><li><strong>File → Save As</strong> again → choose type <strong>PDF (*.pdf)</strong> to give a non-editable copy to someone.</li><li>If you use Microsoft 365 signed in, <strong>AutoSave</strong> may be on — still keep a named copy in your practice folder.</li></ol>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Create three files in one folder: <code>MyLetter.docx</code>, <code>MyFees.xlsx</code>, <code>MySlides.pptx</code>. Export the Word file as PDF.</p>
HTML,
                    ],
                    [
                        'title' => '1.3 Undo, redo, copy, paste, and zoom',
                        'minutes' => 30,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Shortcuts (memorise)</h3>
<ul class="list-disc pl-5 mb-3 space-y-1"><li><strong>Ctrl+Z</strong> Undo &nbsp;·&nbsp; <strong>Ctrl+Y</strong> Redo</li><li><strong>Ctrl+C</strong> Copy &nbsp;·&nbsp; <strong>Ctrl+X</strong> Cut &nbsp;·&nbsp; <strong>Ctrl+V</strong> Paste</li><li><strong>Ctrl+S</strong> Save</li></ul>
<h3 class="text-lg font-semibold mb-2">Zoom</h3>
<p>Use the <strong>slider bottom-right</strong> (Word/Excel/PPT) or <strong>View → Zoom</strong>. <strong>Visual:</strong> before/after 100% vs 120%.</p>
<h3 class="text-lg font-semibold mb-2">Tell Me / search</h3>
<p>Type what you need in the box above the ribbon (e.g. “page number”) to jump to commands.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>In Word, type a paragraph, bold one line (Ctrl+B), undo, redo, copy the paragraph into PowerPoint title placeholder.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Module 2 — Microsoft Word (documents & formatting)',
                'summary' => 'Professional documents: styles, layout, tables, images, headers, mail merge basics.',
                'lessons' => [
                    [
                        'title' => '2.1 Typing, paragraphs, and non-printing characters',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Paragraph vs line break</h3>
<p class="mb-3"><strong>Enter</strong> starts a new <em>paragraph</em>. If you only want a new line inside the same paragraph (rare), use <strong>Shift+Enter</strong>.</p>
<h3 class="text-lg font-semibold mb-2">Show paragraph marks</h3>
<p><strong>Home → ¶</strong> (Show/Hide). You will see ¶ at each paragraph end. <strong>Visual:</strong> same paragraph with marks on/off.</p>
<h3 class="text-lg font-semibold mb-2">Hands-on</h3>
<p>Type a 3-paragraph “Notice” for your institute (title line + two body paragraphs). Turn Show/Hide on and check there is one ¶ between paragraphs.</p>
HTML,
                    ],
                    [
                        'title' => '2.2 Fonts, sizes, and Styles (Heading 1/2)',
                        'minutes' => 50,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Do not only use “big bold” manually</h3>
<p class="mb-3">Use <strong>Styles</strong> on the Home tab: <strong>Heading 1</strong> for main title, <strong>Heading 2</strong> for sections. Later you can change every Heading 1 at once by modifying the style.</p>
<h3 class="text-lg font-semibold mb-2">Steps</h3>
<ol class="list-decimal pl-5 space-y-2 mb-3"><li>Select the title → apply <strong>Heading 1</strong>.</li><li>Select a section line → <strong>Heading 2</strong>.</li><li>Right-click a style → <strong>Modify</strong> → set font (e.g. Arial, colour). OK. <strong>Visual:</strong> Modify Style dialog.</li></ol>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Format a 1-page “Course brochure” for MS Office with H1 title, H2 sections: Overview, Fee, Duration, Contact.</p>
HTML,
                    ],
                    [
                        'title' => '2.3 Alignment, line spacing, bullets, numbering',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Paragraph group</h3>
<p class="mb-3">Home: Align Left/Center/Right/Justify. <strong>Line spacing</strong> (1.15, 1.5, Double). <strong>Space Before/After</strong> opens from Paragraph dialog launcher.</p>
<h3 class="text-lg font-semibold mb-2">Lists</h3>
<p>Use <strong>Bullets</strong> for unordered items, <strong>Numbering</strong> for steps. For multi-level (1.  a)  i.), use <strong>Multilevel list</strong> on Home.</p>
<h3 class="text-lg font-semibold mb-2">Visuals to record</h3>
<ul class="list-disc pl-5 mb-3"><li>Paragraph settings box (Indents and Spacing tab)</li><li>Multilevel list gallery</li></ul>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Write “Institute rules” with 5 numbered rules and sub-bullets under rule 2 and 4.</p>
HTML,
                    ],
                    [
                        'title' => '2.4 Margins, orientation, section breaks',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Layout tab</h3>
<p><strong>Layout → Margins</strong> (Normal, Narrow). <strong>Orientation</strong>: Portrait vs Landscape.</p>
<h3 class="text-lg font-semibold mb-2">Section break for mixed orientation</h3>
<ol class="list-decimal pl-5 space-y-2 mb-3"><li>Place cursor at start of page 2.</li><li><strong>Layout → Breaks → Next Page</strong> (section break).</li><li>Stay on page 2 → set Orientation to <strong>Landscape</strong> — page 1 stays portrait.</li><li><strong>Visual:</strong> Print Preview showing portrait cover + landscape table page.</li></ol>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Page 1 portrait syllabus title; page 2 landscape wide timetable table.</p>
HTML,
                    ],
                    [
                        'title' => '2.5 Tables in Word',
                        'minutes' => 50,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Insert a table</h3>
<p><strong>Insert → Table</strong> → drag or Insert Table (rows/columns). Two new tabs appear when cursor is inside: <strong>Table Design</strong> and <strong>Layout</strong>.</p>
<h3 class="text-lg font-semibold mb-2">Merge & split</h3>
<p>Select cells → <strong>Layout → Merge Cells</strong> for a title row. <strong>Split Cells</strong> to subdivide.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Build a fee table: columns Course, Registration, Tuition, Total; merge top row for “SoftPro — Fee Structure”; add borders from Table Design.</p>
HTML,
                    ],
                    [
                        'title' => '2.6 Pictures, shapes, text wrapping',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Insert picture</h3>
<p><strong>Insert → Pictures</strong> → This Device. Select image → use handles to resize. <strong>Picture Format → Crop</strong> to trim.</p>
<h3 class="text-lg font-semibold mb-2">Wrap text</h3>
<p>Layout Options (beside image) or Picture Format → <strong>Wrap Text</strong>: try <strong>Square</strong> or <strong>Tight</strong> so text flows around. Use <strong>In Front of Text</strong> for posters.</p>
<h3 class="text-lg font-semibold mb-2">Shapes</h3>
<p><strong>Insert → Shapes</strong> → rectangle, arrow, callout. Drag on page; type inside right-click “Add Text”. <strong>Visual:</strong> simple “Computer Lab Rules” poster.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>One-page poster: heading + 4 rules in shapes + small logo image.</p>
HTML,
                    ],
                    [
                        'title' => '2.7 Headers, footers, page numbers',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Header and footer</h3>
<p><strong>Insert → Header</strong> → choose style; type institute name. <strong>Insert → Footer</strong> optional.</p>
<h3 class="text-lg font-semibold mb-2">Page numbers</h3>
<p><strong>Insert → Page Number</strong> → Bottom of Page. <strong>Different First Page</strong> (Header & Footer Tools → check box): cover has no number; numbering starts from page 2.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>3-page document: first page assignment cover (no page no); pages 2–3 with “Page X of Y” optional in footer.</p>
HTML,
                    ],
                    [
                        'title' => '2.8 Find, Replace, and spell check',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Find/Replace</h3>
<p><strong>Ctrl+H</strong>. Find what: old word, Replace with: new → <strong>Replace All</strong> with care (use “Find Next” first on important docs).</p>
<h3 class="text-lg font-semibold mb-2">Editor / spelling</h3>
<p><strong>Review → Editor</strong> or F7. Fix grammar and spelling suggestions.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Replace “ABC Institute” with your name across a 1-page sample; run Editor and fix all issues.</p>
HTML,
                    ],
                    [
                        'title' => '2.9 Mail Merge — letters from an Excel list',
                        'minutes' => 60,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Use case</h3>
<p>Same letter to 200 students; only name/address/course change. Data lives in <strong>Excel</strong>; Word pulls fields.</p>
<h3 class="text-lg font-semibold mb-2">Steps</h3>
<ol class="list-decimal pl-5 space-y-2 mb-3"><li>Excel: Row 1 headers <code>Name</code>, <code>Course</code>, <code>Fee</code>. Rows 2+ data.</li><li>Word: <strong>Mailings → Start Mail Merge → Letters</strong>.</li><li><strong>Select Recipients → Use an Existing List</strong> → pick the Excel file → sheet.</li><li>Click in document, <strong>Insert Merge Field</strong> → Name, Course, Fee.</li><li><strong>Preview Results</strong> to check. <strong>Finish & Merge → Edit Individual Documents</strong> generates one file with all letters.</li></ol>
<h3 class="text-lg font-semibold mb-2">Visuals</h3>
<p>Record Mailings ribbon, merge field codes «Name», and preview pane.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>10 dummy students in Excel → print-ready letters in Word.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Module 3 — Microsoft Excel (spreadsheets)',
                'summary' => 'Formats, formulas, references, lists, charts, printing.',
                'lessons' => [
                    [
                        'title' => '3.1 Sheets, cells, ranges, and Fill handle',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Basics</h3>
<p>A <strong>workbook</strong> has <strong>sheets</strong> (tabs bottom). A cell is named by column+row (<strong>B5</strong>). A <strong>range</strong> is e.g. <strong>A1:A20</strong>.</p>
<h3 class="text-lg font-semibold mb-2">Fill series</h3>
<p>Type <code>1</code> in A1, <code>2</code> in A2 — select both, drag the small square (fill handle) down to auto-fill 1,2,3… <strong>Visual:</strong> cursor on fill handle.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Rename Sheet1 to “Fees”. Create serial 1–100 in column A. Type Mon in B1, drag fill handle to get Tue, Wed…</p>
HTML,
                    ],
                    [
                        'title' => '3.2 Number formats: currency, date, percentage',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Format cells</h3>
<p>Select column → <strong>Home → Number</strong> dropdown: Number, Currency, Accounting, Short Date, Percentage. <strong>Ctrl+1</strong> opens full Format Cells.</p>
<h3 class="text-lg font-semibold mb-2">Indian currency</h3>
<p>Format Cells → Currency → symbol <strong>₹</strong> (or English India). Decimals 2 for fees.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Build a column of fees in ₹, a column of dates as dd-mmm-yyyy, and pass % column 0 decimals.</p>
HTML,
                    ],
                    [
                        'title' => '3.3 Formulas: SUM, AVERAGE, MIN, MAX, COUNT',
                        'minutes' => 50,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Every formula starts with <code>=</code></h3>
<ul class="list-disc pl-5 mb-3 space-y-1"><li><code>=SUM(B2:B10)</code> adds numbers</li><li><code>=AVERAGE(C2:C10)</code></li><li><code>=MIN()</code> / <code>=MAX()</code></li><li><code>=COUNT(B2:B10)</code> counts numbers in range</li></ul>
<h3 class="text-lg font-semibold mb-2">Formula bar</h3>
<p>Click a cell with formula — see expression in the <strong>formula bar</strong> above the sheet. <strong>F2</strong> edits in cell.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Class marks: columns Name, M1,M2,M3; column Total =SUM; Average =AVERAGE for each student.</p>
HTML,
                    ],
                    [
                        'title' => '3.4 Absolute references ($B$1) and copying formulas',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Relative vs absolute</h3>
<p>When you copy a formula sideways, relative references move. Lock a cell with <strong>$</strong>: <code>$B$1</code> never moves. Press <strong>F4</strong> in formula bar on a reference to cycle $A$1, A$1, $A1, A1.</p>
<h3 class="text-lg font-semibold mb-2">Example</h3>
<p>GST rate in <strong>B1</strong> (18%). Amounts in column A starting row 5. In B5: <code>=A5*$B$1</code> — copy down. B1 stays fixed. <strong>Visual:</strong> before/after copy.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Invoice: qty, price, line total; tax from fixed cell; grand total.</p>
HTML,
                    ],
                    [
                        'title' => '3.5 IF and COUNTIF (intro)',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">IF</h3>
<p><code>=IF(C2>=40,"Pass","Fail")</code> — test, value if true, value if false. Nest carefully or readability suffers.</p>
<h3 class="text-lg font-semibold mb-2">COUNTIF</h3>
<p><code>=COUNTIF(Status_range,"Paid")</code> counts cells equal to Paid. Use ranges like <code>E:E</code> only if no headers mixed in.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Fee sheet: Status column Paid/Pending; count how many Paid; flag Pass/Fail on marks.</p>
HTML,
                    ],
                    [
                        'title' => '3.6 Sort, Filter, and Excel Table (Ctrl+T)',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Sort</h3>
<p>Select table (with headers) → <strong>Data → Sort</strong>. Multiple levels: sort by Course, then by Name.</p>
<h3 class="text-lg font-semibold mb-2">Filter</h3>
<p><strong>Data → Filter</strong> — dropdown arrows on headers. Uncheck values to hide rows. Clear filter from same menu.</p>
<h3 class="text-lg font-semibold mb-2">Table</h3>
<p><strong>Ctrl+T</strong> → OK with headers. Enables banded rows, structured references, optional Total row.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>50 rows of students — filter one course only; sort fees descending.</p>
HTML,
                    ],
                    [
                        'title' => '3.7 Charts',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Column chart</h3>
<p>Select category + values → <strong>Insert → Column</strong>. Add <strong>Chart Title</strong>, axis titles from Chart Design.</p>
<h3 class="text-lg font-semibold mb-2">Good habits</h3>
<p>Label axes; avoid 3D pie clutter; prefer bar/column for comparing categories.</p>
<h3 class="text-lg font-semibold mb-2">Visuals</h3>
<p>Before: raw table; after: clean column chart for “Monthly admissions”.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Chart monthly collection from 6 months of data.</p>
HTML,
                    ],
                    [
                        'title' => '3.8 Page setup, print area, repeat header row',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Print preview</h3>
<p><strong>Ctrl+P</strong>. Check scaling: <strong>Fit Sheet on One Page</strong> if wide.</p>
<h3 class="text-lg font-semibold mb-2">Print titles</h3>
<p><strong>Page Layout → Print Titles</strong> → Rows to repeat at top: <code>$1:$1</code> so header prints on every printed page.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Long student list: ensure first row repeats; set landscape if many columns.</p>
HTML,
                    ],
                    [
                        'title' => '3.9 Introduction to PivotTables',
                        'minutes' => 55,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">When to use</h3>
<p>Summarise thousands of rows: totals by course, by month, without writing many SUMIFs.</p>
<h3 class="text-lg font-semibold mb-2">Steps</h3>
<ol class="list-decimal pl-5 space-y-2 mb-3"><li>Select data (headers required). <strong>Insert → PivotTable</strong> → New sheet.</li><li>Field list: drag <strong>Course</strong> to Rows, <strong>Amount</strong> to Values (Sum).</li><li>Format value column as currency.</li></ol>
<h3 class="text-lg font-semibold mb-2">Visual</h3>
<p>PivotTable Fields pane + resulting small summary table.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Sample fee payments: total by course and by payment mode if you have that column.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Module 4 — Microsoft PowerPoint (presentations)',
                'summary' => 'Slides, layouts, Slide Master, visuals, simple animation, presenting.',
                'lessons' => [
                    [
                        'title' => '4.1 Slides, layouts, and outline',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Layouts</h3>
<p><strong>Title Slide</strong> for opening; <strong>Title and Content</strong> for bullets + one media; <strong>Two Content</strong> for side-by-side.</p>
<h3 class="text-lg font-semibold mb-2">Thumbnails</h3>
<p>Left pane: reorder slides by drag. <strong>Ctrl+D</strong> duplicate slide.</p>
<h3 class="text-lg font-semibold mb-2">6×6 rule</h3>
<p>About six bullets × six words per slide for readability — not a law, a guideline.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>5 slides: Title, Agenda, three topic slides, Thank you.</p>
HTML,
                    ],
                    [
                        'title' => '4.2 Themes and Slide Master (logo once)',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Theme</h3>
<p><strong>Design → Themes</strong> — consistent colours/fonts across all slides.</p>
<h3 class="text-lg font-semibold mb-2">Slide Master</h3>
<p><strong>View → Slide Master</strong> — edit the top “parent” layout. Place institute logo top-right; change default fonts. Close Master View — all slides inherit. <strong>Visual:</strong> master with logo.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Apply theme + master logo to a 6-slide deck.</p>
HTML,
                    ],
                    [
                        'title' => '4.3 Images, icons, align tools',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Insert</h3>
<p><strong>Insert → Pictures</strong>; <strong>Insert → Icons</strong> (Microsoft 365) for simple symbols.</p>
<h3 class="text-lg font-semibold mb-2">Align</h3>
<p>Select multiple objects → <strong>Shape Format → Align</strong> (Align Center, Distribute Vertically).</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>One “Lab rules” slide with three icons and aligned captions.</p>
HTML,
                    ],
                    [
                        'title' => '4.4 Transitions and restrained animation',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Transitions</h3>
<p><strong>Transitions</strong> tab — subtle <strong>Fade</strong> or <strong>Push</strong> between slides. Avoid random every slide.</p>
<h3 class="text-lg font-semibold mb-2">Animation</h3>
<p>Select a text box → <strong>Animations → Appear</strong> or <strong>Float In</strong>. <strong>Animation Pane</strong>: reorder, “Start After Previous” for bullet-by-bullet.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Topic slide: bullets appear one by one during demo.</p>
HTML,
                    ],
                    [
                        'title' => '4.5 Presenter View and Slide Show',
                        'minutes' => 30,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Run show</h3>
<p><strong>F5</strong> from start; <strong>Shift+F5</strong> current slide. <strong>Slide Show → Presenter View</strong> if two screens: see notes, next slide, timer.</p>
<h3 class="text-lg font-semibold mb-2">Laser pointer</h3>
<p>Ctrl+L or toolbar during slideshow for temporary highlight.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Present a 5-minute deck to a friend; time yourself.</p>
HTML,
                    ],
                ],
            ],
            [
                'title' => 'Module 5 — Outlook & course project',
                'summary' => 'Email basics and a capstone that uses Word+Excel+PowerPoint together.',
                'lessons' => [
                    [
                        'title' => '5.1 Outlook: inbox, compose, attachments, signature',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Professional email</h3>
<p>Clear <strong>Subject</strong>. Greeting + short body + signature. <strong>Attach</strong> PDF when sending official letters.</p>
<h3 class="text-lg font-semibold mb-2">Folders</h3>
<p>Create <strong>Folders</strong> for Students, Fees, Admin — drag mail to organise.</p>
<h3 class="text-lg font-semibold mb-2">Practice</h3>
<p>Send email to yourself with subject “MS Office practice” + PDF attachment.</p>
HTML,
                    ],
                    [
                        'title' => '5.2 Final portfolio project (assessment brief)',
                        'minutes' => 90,
                        'body' => <<<'HTML'
<h3 class="text-lg font-semibold mb-2">Submit three artefacts</h3>
<ol class="list-decimal pl-5 space-y-2 mb-3"><li><strong>Word:</strong> 1–2 page institute notice with Heading styles, table, header with page numbers (page 2+), one picture.</li><li><strong>Excel:</strong> Fee/admission sheet with SUM, AVERAGE, IF or COUNTIF, number formats, one chart; printing with header row repeat.</li><li><strong>PowerPoint:</strong> minimum 8 slides, Slide Master logo, topic slide with restrained animation, no wall-of-text.</li></ol>
<h3 class="text-lg font-semibold mb-2">Grading rubric (suggested)</h3>
<ul class="list-disc pl-5 mb-3"><li>Formatting consistency — 25%</li><li>Excel formulas correct — 35%</li><li>Slide design &amp; Master — 25%</li><li>Completeness &amp; file naming — 15%</li></ul>
<h3 class="text-lg font-semibold mb-2">Tip</h3>
<p>Name files: <code>YourName_MSOffice_Word.docx</code> etc. Zip if submitting together.</p>
HTML,
                    ],
                ],
            ],
        ];
    }
}
