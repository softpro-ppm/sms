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
                        'title' => '2.1 Typing, Paragraphs, and Non-Printing Characters',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.1 Typing, Paragraphs, and Non-Printing Characters</h2>

<p class="mb-4">
Microsoft Word is mainly used for typing and formatting documents. To create neat and professional documents,
students must understand how typing works, how paragraphs are formed, and how hidden formatting marks affect the document.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Type text correctly in Microsoft Word</li>
  <li>Understand what a paragraph is</li>
  <li>Use Enter, Space, Backspace, and Delete properly</li>
  <li>Identify non-printing characters in a document</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Typing Basics</h3>
<p class="mb-3">
Typing in Word means entering text into the document area. While typing, it is important to use the keyboard properly
and avoid unnecessary spaces or repeated Enter keys.
</p>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Use <strong>Spacebar</strong> to leave one space between words</li>
  <li>Use <strong>Enter</strong> to start a new paragraph</li>
  <li>Use <strong>Backspace</strong> to remove text on the left side of the cursor</li>
  <li>Use <strong>Delete</strong> to remove text on the right side of the cursor</li>
</ul>

<h3 class="text-lg font-semibold mb-2">What is a Paragraph?</h3>
<p class="mb-4">
A paragraph is a block of text that begins when you press the <strong>Enter</strong> key.
In Word, each paragraph can have its own alignment, spacing, indentation, and formatting.
</p>

<h3 class="text-lg font-semibold mb-2">Non-Printing Characters</h3>
<p class="mb-3">
Non-printing characters are special symbols that help you understand how text is arranged in the document.
These symbols are visible only on screen and do not appear when the document is printed.
</p>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>·</strong> shows a space</li>
  <li><strong>¶</strong> shows the end of a paragraph</li>
  <li><strong>→</strong> shows a tab space</li>
</ul>

<p class="mb-4">
You can show or hide these symbols using the <strong>Show/Hide</strong> button on the Home tab.
This is useful when fixing spacing and formatting problems.
</p>

<h3 class="text-lg font-semibold mb-2">Good Typing Habits</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Do not press the spacebar many times to align text</li>
  <li>Do not press Enter repeatedly to move text down</li>
  <li>Use proper paragraph and formatting tools instead</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Open Microsoft Word and type five lines about yourself.</li>
  <li>Press Enter after each paragraph.</li>
  <li>Turn on the <strong>Show/Hide</strong> option and observe the paragraph marks and spaces.</li>
  <li>Remove extra spaces and unnecessary paragraph breaks.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Correct typing and proper paragraph usage are the foundation of document creation.
Non-printing characters help you identify hidden formatting marks and make the document clean and professional.
</p>
HTML,
                    ],
                    [
                        'title' => '2.2 Fonts, Sizes, and Styles (Heading 1/2)',
                        'minutes' => 50,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.2 Fonts, Sizes, and Styles (Heading 1/2)</h2>

<p class="mb-4">
A professional document must be easy to read and well structured.
Fonts, font sizes, and styles help improve the appearance and readability of a document.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Change font type and font size</li>
  <li>Apply bold, italic, and underline styles</li>
  <li>Use Heading 1 and Heading 2 styles</li>
  <li>Understand the importance of consistent document formatting</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Fonts</h3>
<p class="mb-3">
A font is the design or style of text. Common fonts include Calibri, Arial, and Times New Roman.
Different fonts are used for different purposes, but official documents usually use simple and readable fonts.
</p>

<h3 class="text-lg font-semibold mb-2">Font Size</h3>
<p class="mb-4">
Font size controls how large or small the text appears.
For normal documents, sizes such as <strong>11</strong> or <strong>12</strong> are commonly used.
Headings are usually larger than normal text.
</p>

<h3 class="text-lg font-semibold mb-2">Basic Text Styles</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Bold</strong> makes text darker and stronger</li>
  <li><em>Italic</em> slants the text</li>
  <li><u>Underline</u> adds a line below the text</li>
</ul>

<p class="mb-4">
These styles should be used carefully. Overusing them can make the document look untidy.
</p>

<h3 class="text-lg font-semibold mb-2">Using Styles in Word</h3>
<p class="mb-3">
Microsoft Word provides built-in styles like <strong>Heading 1</strong> and <strong>Heading 2</strong>.
These styles make documents more organized and are useful for long reports and projects.
</p>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Heading 1</strong> is used for main headings</li>
  <li><strong>Heading 2</strong> is used for subheadings</li>
</ul>

<p class="mb-4">
Using styles also helps create an automatic table of contents in large documents.
</p>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Open a blank Word document.</li>
  <li>Type a main heading and apply <strong>Heading 1</strong>.</li>
  <li>Type two subheadings and apply <strong>Heading 2</strong>.</li>
  <li>Type one paragraph below each heading.</li>
  <li>Change the paragraph font size and apply bold or italic where needed.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Fonts, sizes, and styles improve the readability and structure of a document.
Heading styles are especially useful for making documents neat, professional, and easy to navigate.
</p>
HTML,
                    ],
                    [
                        'title' => '2.3 Alignment, Line Spacing, Bullets, Numbering',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.3 Alignment, Line Spacing, Bullets, Numbering</h2>

<p class="mb-4">
Document formatting is not only about typing text. It is also important to arrange the text neatly.
Alignment, line spacing, bullets, and numbering help make content more readable and organized.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Apply text alignment correctly</li>
  <li>Adjust line spacing in paragraphs</li>
  <li>Create bullet lists</li>
  <li>Create numbered lists</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Text Alignment</h3>
<p class="mb-3">
Alignment controls how text is positioned between the left and right margins.
</p>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Left Align</strong> – text starts from the left side</li>
  <li><strong>Center Align</strong> – text appears in the middle</li>
  <li><strong>Right Align</strong> – text starts from the right side</li>
  <li><strong>Justify</strong> – text is aligned evenly on both sides</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Line Spacing</h3>
<p class="mb-4">
Line spacing is the vertical gap between lines of text.
Common line spacing options include <strong>1.0</strong>, <strong>1.15</strong>, <strong>1.5</strong>, and <strong>2.0</strong>.
Proper spacing improves readability.
</p>

<h3 class="text-lg font-semibold mb-2">Bullets</h3>
<p class="mb-4">
Bullets are used to present a list of items where the order is not important.
They are useful for features, points, requirements, and highlights.
</p>

<h3 class="text-lg font-semibold mb-2">Numbering</h3>
<p class="mb-4">
Numbering is used when the order is important.
It is useful for steps, instructions, procedures, and rankings.
</p>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Type one short paragraph and apply <strong>Justify</strong> alignment.</li>
  <li>Change line spacing to <strong>1.5</strong>.</li>
  <li>Create a bullet list of five computer skills.</li>
  <li>Create a numbered list showing steps to open Microsoft Word.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Alignment, spacing, bullets, and numbering help present information clearly.
These tools are essential for creating well-formatted and professional Word documents.
</p>
HTML,
                    ],
                    [
                        'title' => '2.4 Margins, Orientation, Section Breaks',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.4 Margins, Orientation, Section Breaks</h2>

<p class="mb-4">
Page layout settings control how a document appears on paper.
Margins, orientation, and section breaks are important for letters, reports, certificates, and multi-page documents.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Understand page margins</li>
  <li>Change page orientation</li>
  <li>Use section breaks in long documents</li>
  <li>Prepare documents for proper printing</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Margins</h3>
<p class="mb-4">
Margins are the blank spaces around the edges of a page.
They provide space for printing, binding, and neat presentation.
Common settings include Normal, Narrow, and Wide margins.
</p>

<h3 class="text-lg font-semibold mb-2">Orientation</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Portrait</strong> – vertical page layout</li>
  <li><strong>Landscape</strong> – horizontal page layout</li>
</ul>

<p class="mb-4">
Portrait is commonly used for letters and reports.
Landscape is useful for wide tables, charts, and certificates.
</p>

<h3 class="text-lg font-semibold mb-2">Section Breaks</h3>
<p class="mb-3">
A section break divides a document into separate sections so that each section can have different formatting.
For example, one page may be portrait and the next page may be landscape.
</p>

<p class="mb-4">
Section breaks are useful in reports, project files, and mixed-layout documents.
</p>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Open a Word document and type some content on two pages.</li>
  <li>Change the margins using the Layout tab.</li>
  <li>Set page orientation to Portrait.</li>
  <li>Insert a section break and change the second section to Landscape.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Margins, orientation, and section breaks help control the overall layout of a document.
They are important for producing clean, printable, and professional documents.
</p>
HTML,
                    ],
                    [
                        'title' => '2.5 Tables in Word',
                        'minutes' => 50,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.5 Tables in Word</h2>

<p class="mb-4">
Tables are used to arrange information in rows and columns.
They are very useful for marks, fees, schedules, lists, and structured data in Microsoft Word.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Insert a table in Word</li>
  <li>Add and remove rows and columns</li>
  <li>Enter data into table cells</li>
  <li>Apply basic table formatting</li>
</ul>

<h3 class="text-lg font-semibold mb-2">What is a Table?</h3>
<p class="mb-4">
A table is a grid made of rows and columns.
The point where a row and column meet is called a cell.
Each cell can hold text, numbers, or other content.
</p>

<h3 class="text-lg font-semibold mb-2">Using Tables</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Tables are useful for student lists, fee details, timetables, and attendance</li>
  <li>You can insert a table from the Insert tab</li>
  <li>You can adjust borders, shading, and alignment inside the table</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Common Table Actions</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Insert new rows or columns</li>
  <li>Delete unwanted rows or columns</li>
  <li>Merge cells for headings</li>
  <li>Adjust column width and row height</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create a table with 4 columns and 5 rows.</li>
  <li>Enter headings such as Name, Course, Fee, and Phone Number.</li>
  <li>Fill in sample data.</li>
  <li>Apply bold formatting to the heading row.</li>
  <li>Adjust the table so that all data is clearly visible.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Tables help present structured information clearly and neatly.
They are widely used in office documents and are one of the most useful tools in Microsoft Word.
</p>
HTML,
                    ],
                    [
                        'title' => '2.6 Pictures, Shapes, Text Wrapping',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.6 Pictures, Shapes, Text Wrapping</h2>

<p class="mb-4">
Documents often need more than text. Pictures and shapes make documents more attractive and informative.
Text wrapping controls how text flows around inserted objects.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Insert pictures into a Word document</li>
  <li>Insert and format shapes</li>
  <li>Use text wrapping options correctly</li>
  <li>Arrange pictures and text neatly on the page</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Pictures in Word</h3>
<p class="mb-4">
Pictures can be inserted from the computer into a document.
They are useful in brochures, reports, certificates, and advertisements.
</p>

<h3 class="text-lg font-semibold mb-2">Shapes in Word</h3>
<p class="mb-4">
Shapes include lines, arrows, rectangles, circles, and other design elements.
They are useful for highlighting content, making diagrams, and improving page design.
</p>

<h3 class="text-lg font-semibold mb-2">Text Wrapping</h3>
<p class="mb-3">
Text wrapping controls the position of text around a picture or shape.
Common wrapping options include:
</p>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>In Line with Text</strong></li>
  <li><strong>Square</strong></li>
  <li><strong>Tight</strong></li>
  <li><strong>Behind Text</strong></li>
  <li><strong>In Front of Text</strong></li>
</ul>

<p class="mb-4">
Choosing the correct wrapping style helps create neat and professional layouts.
</p>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Open a Word document and type one paragraph.</li>
  <li>Insert a picture into the page.</li>
  <li>Change the text wrapping option to <strong>Square</strong>.</li>
  <li>Insert a shape and type text inside it.</li>
  <li>Move and resize the picture and shape neatly.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Pictures and shapes improve the visual quality of a document.
Text wrapping helps place them properly so that the document looks clean and easy to read.
</p>
HTML,
                    ],
                    [
                        'title' => '2.7 Headers, Footers, Page Numbers',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.7 Headers, Footers, Page Numbers</h2>

<p class="mb-4">
Headers and footers are important parts of formal documents.
They are commonly used in reports, office letters, bills, project files, and printed documents.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Understand the purpose of headers and footers</li>
  <li>Insert content in the header area</li>
  <li>Insert content in the footer area</li>
  <li>Add page numbers to a document</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Header</h3>
<p class="mb-4">
A header is the top section of a page.
It can contain the document title, company name, logo, or other repeated information.
</p>

<h3 class="text-lg font-semibold mb-2">Footer</h3>
<p class="mb-4">
A footer is the bottom section of a page.
It can contain page numbers, contact details, dates, or confidentiality notes.
</p>

<h3 class="text-lg font-semibold mb-2">Page Numbers</h3>
<p class="mb-4">
Page numbers help organize multi-page documents.
They make it easier for readers to follow the content and refer to specific pages.
</p>

<h3 class="text-lg font-semibold mb-2">Common Uses</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Company name in header</li>
  <li>Page numbers in footer</li>
  <li>Date or document title in header or footer</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create a document with at least two pages.</li>
  <li>Insert a header and type your institute or company name.</li>
  <li>Insert a footer and type a short note such as "Prepared in MS Word".</li>
  <li>Add page numbers at the bottom of the page.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Headers, footers, and page numbers improve the appearance and organization of a document.
They are especially useful in formal and multi-page documents.
</p>
HTML,
                    ],
                    [
                        'title' => '2.8 Find, Replace, and Spell Check',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.8 Find, Replace, and Spell Check</h2>

<p class="mb-4">
When working with long documents, it is important to locate words quickly, correct repeated mistakes, and check spelling.
Microsoft Word provides tools to do this efficiently.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Use Find to locate words in a document</li>
  <li>Use Replace to change repeated words or text</li>
  <li>Use Spell Check to correct spelling mistakes</li>
  <li>Improve accuracy in document preparation</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Find</h3>
<p class="mb-4">
The Find tool helps locate a word, phrase, or number in a document.
This is useful when the document contains many pages and searching manually takes time.
</p>

<h3 class="text-lg font-semibold mb-2">Replace</h3>
<p class="mb-4">
The Replace tool helps change one word or phrase into another throughout the document.
For example, if a company name is typed incorrectly many times, Replace can correct all occurrences quickly.
</p>

<h3 class="text-lg font-semibold mb-2">Spell Check</h3>
<p class="mb-4">
Spell Check identifies spelling mistakes and suggests corrections.
Words with spelling issues are often underlined, helping the user correct them easily.
</p>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Type a paragraph with a few repeated words.</li>
  <li>Use <strong>Find</strong> to search for one word.</li>
  <li>Use <strong>Replace</strong> to change it to another word.</li>
  <li>Type a few spelling mistakes and use Spell Check to correct them.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Find, Replace, and Spell Check save time and improve document accuracy.
These tools are very helpful when editing long or important documents.
</p>
HTML,
                    ],
                    [
                        'title' => '2.9 Mail Merge — Letters from an Excel List',
                        'minutes' => 60,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">2.9 Mail Merge — Letters from an Excel List</h2>

<p class="mb-4">
Mail Merge is one of the most powerful features in Microsoft Word.
It helps create many letters, certificates, or notices automatically by using data from an Excel sheet.
</p>

<h3 class="text-lg font-semibold mb-2">Learning Objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Understand the purpose of Mail Merge</li>
  <li>Use an Excel sheet as a data source</li>
  <li>Insert merge fields into a Word document</li>
  <li>Create multiple personalized letters automatically</li>
</ul>

<h3 class="text-lg font-semibold mb-2">What is Mail Merge?</h3>
<p class="mb-4">
Mail Merge combines a main Word document with data from another source, usually an Excel sheet.
This allows one document format to be used for many different people without typing each letter separately.
</p>

<h3 class="text-lg font-semibold mb-2">Example</h3>
<p class="mb-4">
Suppose you want to create admission letters for 100 students.
Instead of typing each student name manually, you can keep the names in Excel and let Word insert them automatically into the letter.
</p>

<h3 class="text-lg font-semibold mb-2">Main Parts of Mail Merge</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Main Document</strong> – the Word letter format</li>
  <li><strong>Data Source</strong> – the Excel list containing names and other details</li>
  <li><strong>Merge Fields</strong> – placeholders such as Name, Course, or Fee</li>
</ul>

<h3 class="text-lg font-semibold mb-2">Basic Steps</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create an Excel file with columns such as Name, Course, and Mobile Number</li>
  <li>Open Microsoft Word and type the main letter</li>
  <li>Go to the <strong>Mailings</strong> tab</li>
  <li>Select <strong>Start Mail Merge</strong></li>
  <li>Choose <strong>Select Recipients</strong> and connect the Excel file</li>
  <li>Insert merge fields where needed</li>
  <li>Preview the results</li>
  <li>Finish and merge the letters</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Practice Activity</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create an Excel sheet with three student names.</li>
  <li>Prepare a simple Word letter for students.</li>
  <li>Insert merge fields such as Name and Course.</li>
  <li>Preview and generate the letters.</li>
</ol>

<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>
Mail Merge saves time and reduces manual work.
It is very useful for admission letters, certificates, notices, fee reminders, and bulk communication documents.
</p>
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
