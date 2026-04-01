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
<h2 class="text-xl font-bold mb-4">3.1 Sheets, cells, ranges, and Fill handle</h2>
<p class="mb-4">Excel is the tool training institutes use for <strong>fee registers</strong>, <strong>marks sheets</strong>, <strong>attendance logs</strong>, and <strong>office expense</strong> tracking. This lesson builds the foundation: sheets, cells, ranges, and the Fill handle so you can enter data quickly and accurately.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Name workbook, worksheet, cell, and range</li>
  <li>Select cells and ranges with the mouse and keyboard</li>
  <li>Use the Fill handle for serial numbers and weekdays</li>
  <li>Rename sheets for real projects (e.g. Fees, Marks, Attendance)</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>A <strong>workbook</strong> is the entire <code>.xlsx</code> file; each tab at the bottom is a <strong>worksheet</strong> (sheet).</li>
  <li>A <strong>cell</strong> is one box, named by <strong>column letter + row number</strong> (e.g. <code>D12</code>).</li>
  <li>A <strong>range</strong> is a rectangle of cells (e.g. <code>A1:A100</code> or <code>B2:E20</code>).</li>
  <li>The <strong>Name Box</strong> (left of the formula bar) shows the active cell address.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Fee register:</strong> Sheet “Fees”, columns Serial, Student, Course, Amount, Date — one row per payment.</p>
<p class="mb-4"><strong>Marks:</strong> Sheet “M1_Test”, row 1 headers (Name, M1, M2, Total), data from row 2 down.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step: Fill handle</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>In <code>A1</code> type <code>1</code>, in <code>A2</code> type <code>2</code>.</li>
  <li>Select <code>A1:A2</code>. Point at the small square at the bottom-right of the selection (the <strong>fill handle</strong>).</li>
  <li>Drag down — Excel continues the pattern (3, 4, 5…).</li>
  <li>For weekdays: type <code>Mon</code> in <code>B1</code>, drag the fill handle — you get Tue, Wed, Thu…</li>
  <li>To fill months or dates, type the first two cells with a clear pattern, then drag.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Dragging the fill handle without selecting two cells first for a numeric pattern — Excel may copy the same number instead of a series.</li>
  <li>Mixing labels and numbers in one column (harder to SUM later) — keep “Paid”, amounts, and dates in separate columns.</li>
  <li>Leaving Sheet1 unnamed — rename to match what the sheet holds.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Rename the first sheet to <strong>Fees</strong>.</li>
  <li>In column A, use the fill handle to create serial numbers <strong>1 to 100</strong>.</li>
  <li>In column B, type <code>Mon</code> and drag to fill one week of days.</li>
  <li>Add a second sheet named <strong>Practice</strong> and type your name in <code>A1</code>.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Sheets organise topics; cells hold one value each; ranges let you work on blocks of data. Mastering the fill handle saves hours when building registers and lists.</p>
HTML,
                    ],
                    [
                        'title' => '3.2 Number formats: currency, date, percentage',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">3.2 Number formats: currency, date, percentage</h2>
<p class="mb-4">Numbers on screen are not “just text” in Excel — <strong>formatting</strong> controls how values <em>display</em> (₹, dates, %) while the cell can still calculate correctly. For a training centre, that means fees look like money, dates look readable, and pass percentages stay clear.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Apply Currency / Accounting for fees and income</li>
  <li>Format dates consistently (e.g. for admission or payment dates)</li>
  <li>Use Percentage for marks and pass rates</li>
  <li>Open Format Cells (<strong>Ctrl+1</strong>) for full control</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Main explanation</h3>
<p class="mb-4">Select a cell or column → <strong>Home</strong> tab → <strong>Number</strong> group. Pick <strong>Currency</strong>, <strong>Accounting</strong>, <strong>Short Date</strong>, <strong>Percentage</strong>, or <strong>More Number Formats</strong> at the bottom for the full dialog.</p>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Currency</strong> — shows ₹ and a fixed number of decimals; good for fee columns.</li>
  <li><strong>Accounting</strong> — aligns currency symbols neatly in columns (professional reports).</li>
  <li><strong>Date</strong> — store real dates (good for sorting/filtering), not typed text like “10-3-2026” mixed styles.</li>
  <li><strong>Percentage</strong> — type <code>0.85</code> and format as % to show <strong>85%</strong>; or type <code>85</code> and choose % depending on your sheet design.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Fee column:</strong> All amounts in ₹ with 2 decimals — matches receipts and bank records.</p>
<p class="mb-3"><strong>GST or tax display:</strong> Use % format for rate cells (e.g. 18% displayed, calculation uses proper decimals).</p>
<p class="mb-4"><strong>Monthly income/expense:</strong> Date column formatted as <code>dd-mmm-yyyy</code> so March admissions don’t confuse day/month.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Select the fee column → Home → Number → <strong>Currency</strong> → choose <strong>₹</strong> (English India if available).</li>
  <li>Select the date column → <strong>Short Date</strong> or <strong>Ctrl+1</strong> → Date → pick a format you like.</li>
  <li>For pass % column → <strong>Percentage</strong>, set decimal places to 0 or 1 as needed.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Typing amounts with the rupee symbol inside the cell everywhere — better to store plain numbers and format as currency.</li>
  <li>Dates stored as text — sorting “Jan, Feb, Mar” fails; use real date formats.</li>
  <li>Applying % to whole marks (e.g. 72 as 7200%) — either divide by 100 in the cell or format correctly.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create a small table: <strong>Student</strong>, <strong>Fee Paid (₹)</strong>, <strong>Payment Date</strong>, <strong>Attendance %</strong> (sample 92, 88, 95).</li>
  <li>Format the fee column as <strong>₹</strong> with 2 decimals.</li>
  <li>Format dates as <strong>dd-mmm-yyyy</strong>.</li>
  <li>Format attendance as <strong>percentage</strong> with 0 decimals.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Correct number formats make your centre’s registers print-ready and professional. Spend time on currency, date, and % once — every future formula and chart will look trustworthy.</p>
HTML,
                    ],
                    [
                        'title' => '3.3 Formulas: SUM, AVERAGE, MIN, MAX, COUNT',
                        'minutes' => 50,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">3.3 Formulas: SUM, AVERAGE, MIN, MAX, COUNT</h2>
<p class="mb-4">Formulas turn a static list into a <strong>living register</strong>: totals of fees collected, average marks, highest/lowest scores, and counts of students. Every office Excel job expects you to use these five functions confidently.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Write formulas starting with <code>=</code></li>
  <li>Use SUM and AVERAGE for marks and fee totals</li>
  <li>Use MIN and MAX for range checks (best score, lowest fee bracket)</li>
  <li>Use COUNT to count numeric entries in a range</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><code>=SUM(B2:B10)</code> — adds all numbers in the range.</li>
  <li><code>=AVERAGE(C2:C10)</code> — arithmetic mean; empty text cells are ignored if truly blank.</li>
  <li><code>=MIN(D2:D20)</code> / <code>=MAX(D2:D20)</code> — smallest / largest value in a range.</li>
  <li><code>=COUNT(E2:E100)</code> — counts cells that contain <strong>numbers</strong> (not text names).</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Student marks:</strong> Columns <strong>M1, M2, M3</strong>; <code>=SUM(C2:E2)</code> in <strong>Total</strong>; <code>=AVERAGE(C2:E2)</code> in <strong>Avg</strong>.</p>
<p class="mb-3"><strong>Fee collection:</strong> Column of amounts paid — <code>=SUM(F2:F200)</code> for day-end total.</p>
<p class="mb-4"><strong>Attendance register:</strong> Numeric “days present” column — <code>=MAX()</code> to sanity-check against working days in month.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Click the cell where the answer should appear.</li>
  <li>Type <code>=SUM(</code>, select the range with the mouse, type <code>)</code>, press <strong>Enter</strong>.</li>
  <li>Click a cell with a formula → read it in the <strong>formula bar</strong>. Press <strong>F2</strong> to edit inside the cell.</li>
  <li>Copy the formula down: select the cell, drag the fill handle through student rows (references shift relatively).</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Forgetting <code>=</code> — Excel treats the cell as text.</li>
  <li>Including the Total row inside the SUM range (double-counting).</li>
  <li>Using COUNT on names — use <code>COUNTA</code> later for non-empty cells; this lesson focuses on <code>COUNT</code> for numbers.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Build a sheet with 5 students: <strong>Name, M1, M2, M3</strong>.</li>
  <li>Add <strong>Total</strong> = SUM of three marks; <strong>Average</strong> = AVERAGE of three marks.</li>
  <li>Below the table, show <strong>Class Highest</strong> (MAX of all marks in one column) and <strong>Class Lowest</strong> (MIN).</li>
  <li>Use COUNT on one marks column to confirm 5 numeric entries.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>SUM and AVERAGE power marks sheets and fee reports; MIN/MAX help quality checks; COUNT verifies how many numeric records you have. These are the backbone of every institute register.</p>
HTML,
                    ],
                    [
                        'title' => '3.4 Absolute references ($B$1) and copying formulas',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">3.4 Absolute references ($B$1) and copying formulas</h2>
<p class="mb-4">When you copy a formula down a fee list or across an invoice, <strong>relative</strong> references move — that is usually what you want for each row’s amount. But <strong>GST rate</strong>, <strong>discount %</strong>, or a <strong>fixed registration fee</strong> must stay on one cell. That is when you <strong>lock</strong> references with <code>$</code>.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Tell relative vs absolute cell references apart</li>
  <li>Lock row, column, or both using <code>$</code></li>
  <li>Use F4 to cycle reference styles quickly</li>
  <li>Build GST/tax and fee worksheets that copy safely</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><code>A1</code> — relative; moves when you copy the formula.</li>
  <li><code>$A$1</code> — fully absolute; always points to A1.</li>
  <li><code>$A1</code> — column locked; <code>A$1</code> — row locked.</li>
  <li><strong>F4</strong> with cursor inside a reference in the formula bar cycles through these.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>GST calculation:</strong> Taxable amounts in <code>A5:A100</code>, rate <strong>18%</strong> in <code>$B$1</code>. In <code>B5</code>: <code>=A5*$B$1</code> — fill down; every row uses the same rate.</p>
<p class="mb-3"><strong>Invoice:</strong> Quantity × Unit price = line amount; each line references a locked <strong>shipping flat fee</strong> in one cell for “delivery”.</p>
<p class="mb-4"><strong>Monthly expense:</strong> Fixed “rent” cell — all category totals can reference <code>$E$2</code>.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Put <strong>GST %</strong> (e.g. <code>0.18</code> or <code>18%</code> formatted) in <strong>B1</strong>.</li>
  <li>In <strong>C5</strong> type <code>=A5*B1</code>. Before copying, make B1 absolute: <code>=A5*$B$1</code>.</li>
  <li>Copy from C5 down to C20 — check a few rows: all still use B1.</li>
  <li>Change B1 to 5% for a demo — entire column recalculates.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Hard-coding the tax rate inside every formula (e.g. <code>*0.18</code>) — one policy change means editing hundreds of cells.</li>
  <li>Using <code>$B$1</code> when you meant only to lock the row — understand mixed references when copying across columns.</li>
  <li>Copying a formula and not noticing the reference “walked” to an empty cell.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create columns: <strong>Item, Qty, Price, Line total</strong> with Line = Qty × Price (relative copies).</li>
  <li>Put <strong>GST %</strong> in <code>F1</code> (one cell). Add <strong>Tax</strong> column = Line × <code>$F$1</code>, fill down.</li>
  <li>Add <strong>Grand total</strong> = SUM of line totals + SUM of tax column (or one formula — your choice).</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Relative references automate row-by-row math; absolute references anchor rates, fees, and constants. Mastering <code>$</code> and F4 is what separates amateur sheets from professional institute billing workbooks.</p>
HTML,
                    ],
                    [
                        'title' => '3.5 IF and COUNTIF (intro)',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">3.5 IF and COUNTIF (intro)</h2>
<p class="mb-4"><strong>IF</strong> lets Excel make simple decisions: pass/fail from marks, “Paid” reminders from balance, eligibility flags. <strong>COUNTIF</strong> answers “how many?” — how many fees are Paid, how many students chose MS Office, etc. Together they power everyday institute reports.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Write IF with a logical test and two outcomes</li>
  <li>Use COUNTIF to count cells that match a criterion</li>
  <li>Apply both to fee status and marks sheets</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<p class="mb-3"><strong>IF syntax:</strong> <code>=IF(logical_test, value_if_true, value_if_false)</code></p>
<p class="mb-3">Example: <code>=IF(C2>=40,"Pass","Fail")</code> if C2 holds total marks out of 50 (adjust threshold to your rules).</p>
<p class="mb-4"><strong>COUNTIF syntax:</strong> <code>=COUNTIF(range, criteria)</code> — e.g. <code>=COUNTIF(E2:E500,"Paid")</code> counts “Paid” in the payment status column.</p>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Fee register:</strong> Column <strong>Status</strong> shows Paid/Pending — COUNTIF tells daily collection count.</li>
  <li><strong>Attendance:</strong> If “Present days” &lt; minimum, IF returns “Condoned” vs “Deficient” for review.</li>
  <li><strong>Exam:</strong> IF combined with percentage column to print “Eligible for certificate” vs “Repeat”.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>In the marks sheet, add column <strong>Result</strong>. Formula: <code>=IF(D2>=175,"Pass","Fail")</code> (change 175 to your pass mark).</li>
  <li>In an empty cell: <code>=COUNTIF(F2:F200,"Paid")</code> for number of paid students.</li>
  <li>Use quotes around text criteria; numbers can be unquoted.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Heavy nesting (IF inside IF inside IF) — hard to audit; split into extra columns or use IFS in newer Excel when you learn it.</li>
  <li>COUNTIF on a column that includes the header row — count becomes wrong; use <code>F2:F500</code> not <code>F:F</code> if row 1 is text headers.</li>
  <li>Spelling mismatch: “paid” vs “Paid” — COUNTIF is case-insensitive usually, but extra spaces break matches.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Build 8 rows: <strong>Name, Total marks, Status (Paid/Pending)</strong>.</li>
  <li>Add <strong>Pass?</strong> column using IF from total marks.</li>
  <li>Below the table, show <strong>How many Paid</strong> using COUNTIF.</li>
  <li>Show <strong>How many Pass</strong> using COUNTIF on the Pass? column if you standardise text as Pass/Fail.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>IF automates decisions from data; COUNTIF quantifies categories. Both are essential for fee tracking and academic outcomes without manual recounting.</p>
HTML,
                    ],
                    [
                        'title' => '3.6 Sort, Filter, and Excel Table (Ctrl+T)',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">3.6 Sort, Filter, and Excel Table (Ctrl+T)</h2>
<p class="mb-4">Large student lists, fee ledgers, and attendance dumps are useless if you cannot <strong>find</strong> and <strong>order</strong> data. Sort and Filter turn a wall of rows into answers: “show only MS Office students”, “highest fee first”, “pending payments this week”. <strong>Format as Table (Ctrl+T)</strong> adds professional formatting and easier features.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Sort by one or multiple columns (e.g. Course, then Name)</li>
  <li>Turn on AutoFilter and filter by course or payment status</li>
  <li>Convert a range to an Excel Table with Ctrl+T</li>
  <li>Clear filters when done to see the full register again</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Sort</strong> reorders rows — always confirm whether your sheet has a <strong>header row</strong> selected in the Sort dialog.</li>
  <li><strong>Filter</strong> hides rows temporarily; it does not delete data.</li>
  <li><strong>Table</strong> — structured range with filters on by default, optional total row, consistent column formatting.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Admission register:</strong> Filter <strong>Course = Tally</strong> to print only that batch list.</p>
<p class="mb-3"><strong>Fee collection:</strong> Sort <strong>Amount</strong> descending to review largest payments first.</p>
<p class="mb-4"><strong>Attendance export:</strong> Multi-level sort: <strong>Month</strong>, then <strong>Student</strong> for tidy reporting.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Select any cell inside your data block (with row 1 as headers).</li>
  <li><strong>Data → Sort</strong> → Add level: Sort by <strong>Course</strong>, then Add level: Sort by <strong>Name</strong>.</li>
  <li><strong>Data → Filter</strong> — use dropdown on <strong>Status</strong> to show only <strong>Pending</strong> fees.</li>
  <li>Press <strong>Ctrl+T</strong>, tick “My table has headers”, OK. Try Table Design styles.</li>
  <li>Clear filter: Data → Clear (or per-column “Select All”).</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Sorting one column without expanding the selection — breaks rows (names no longer match marks). Prefer “Expand the selection” when prompted.</li>
  <li>Leaving filters on when printing — printout looks partial; clear filters before export.</li>
  <li>Merging cells in header rows — breaks tables and filters; avoid merged cells in data lists.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create at least <strong>15 fake students</strong>: Name, Course, Fee Paid, Status.</li>
  <li>Sort by Course A→Z, then by Fee Paid high→low.</li>
  <li>Filter to one course and note the visible row count.</li>
  <li>Convert to <strong>Table</strong> and apply a table style.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Sort orders your world; Filter narrows it; Tables package lists professionally. These three skills are non-negotiable for centre admins working in Excel daily.</p>
HTML,
                    ],
                    [
                        'title' => '3.7 Charts',
                        'minutes' => 45,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">3.7 Charts</h2>
<p class="mb-4">Charts communicate patterns at a glance: which course drew the most admissions, how fee collection grew month by month, or how attendance compares across batches. For an institute, a clean chart in a report slide or notice board beats raw tables for decision-makers.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Insert a column or bar chart from a tidy data table</li>
  <li>Edit chart title and axis titles for clarity</li>
  <li>Choose chart types suited to institute data</li>
  <li>Avoid misleading or cluttered visuals</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Category axis</strong> — usually months, course names, or batches.</li>
  <li><strong>Value axis</strong> — amounts, counts, or percentages.</li>
  <li><strong>Column/bar</strong> — compare categories; <strong>line</strong> — trends over time; <strong>pie</strong> — use sparingly for simple part-of-whole stories.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Monthly fee collection:</strong> Six rows — Month, Collection ₹ — column chart titled “Fee collection by month”.</p>
<p class="mb-3"><strong>Course popularity:</strong> Course name vs enrolled count — bar chart for director’s review.</p>
<p class="mb-4"><strong>Expense categories:</strong> Rent, salary, marketing — vertical column chart for internal finance meeting.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Select headers + data (e.g. <code>A1:B7</code> for Month and Amount).</li>
  <li><strong>Insert → Charts → Column</strong> (2-D clustered).</li>
  <li>Click chart → <strong>Chart Design</strong> → <strong>Add Chart Element</strong> → Axis Titles, Chart Title.</li>
  <li>Type meaningful titles: “Month”, “Collection (₹)”, chart title “FY Admissions Income”.</li>
  <li>Resize chart inside the sheet or copy to PowerPoint later.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>3D pie charts with many slices — unreadable in meetings.</li>
  <li>No axis labels — audience cannot tell units or time span.</li>
  <li>Selecting blank rows or totals row as part of series — skews the chart.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Enter 6 months and plausible collection figures for a training centre.</li>
  <li>Create a <strong>column chart</strong> with title and axis labels.</li>
  <li>Duplicate the data block and make a second chart — <strong>bar chart</strong> — compare which is easier to read on-screen.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Well-labelled column and bar charts turn registers into stories. Use them in internal reviews, annual reports, and student-facing achievement displays.</p>
HTML,
                    ],
                    [
                        'title' => '3.8 Page setup, print area, repeat header row',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">3.8 Page setup, print area, repeat header row</h2>
<p class="mb-4">Printed marks registers, fee summaries, and attendance sheets must be <strong>legible on paper</strong>: correct orientation, scaling, and repeated column headers on every page. Directors and auditors read printouts — Excel’s page setup tools make your institute output look credible.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Use Print Preview before sending a sheet to the printer</li>
  <li>Set scaling (fit wide sheets, avoid cut-off columns)</li>
  <li>Repeat top header rows on every printed page</li>
  <li>Choose portrait vs landscape for wide registers</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Ctrl+P</strong> opens Print — preview shows page breaks.</li>
  <li><strong>Print titles</strong> (Page Layout tab) — rows/columns to repeat on each page.</li>
  <li><strong>Print area</strong> — optional: define only the block you want printed.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>200-row student list:</strong> Row 1 has Name, Course, Fee, Phone — set “Rows to repeat at top” to <code>$1:$1</code>.</p>
<p class="mb-3"><strong>Wide fee ledger:</strong> Many columns — try <strong>Landscape</strong> orientation and “Fit All Columns on One Page” scaling.</p>
<p class="mb-4"><strong>Monthly income/expense summary:</strong> Print area = summary block only, not raw data dump.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Press <strong>Ctrl+P</strong>. Page through previews — note blank cut-off columns.</li>
  <li><strong>Page Layout → Orientation → Landscape</strong> if needed.</li>
  <li><strong>Page Layout → Print Titles</strong> → Sheet tab → Rows to repeat at top: click row 1 picker → select header row → OK.</li>
  <li>In Print settings, try <strong>Scaling</strong> — “Fit Sheet on One Page” for quick handouts (watch font size).</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Printing without preview — second page shows data with no headers.</li>
  <li>Forgetting to reset print area after earlier work — wrong range prints.</li>
  <li>Squeezing everything on one page until text is unreadable — prefer two pages with legible font.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create a list of 30+ students with headers in row 1.</li>
  <li>Set print titles so row 1 repeats.</li>
  <li>Add enough columns that preview cuts off — switch to <strong>landscape</strong> and adjust scaling.</li>
  <li>Save the file — note the print settings travel with the workbook.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Professional centres print registers often. Master Print Preview, repeat headers, and scaling so every hard copy is as clear as your on-screen data.</p>
HTML,
                    ],
                    [
                        'title' => '3.9 Introduction to PivotTables',
                        'minutes' => 55,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">3.9 Introduction to PivotTables</h2>
<p class="mb-4">When your fee log or admission export has <strong>hundreds of rows</strong>, managers ask questions like: “Total collection per course?” or “How much came through UPI vs cash this quarter?” PivotTables answer in seconds — without writing long SUMIF chains. This is senior-level skill for institute admins.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Know when a PivotTable beats manual summaries</li>
  <li>Create a PivotTable from a clean table with headers</li>
  <li>Drag fields to Rows, Columns, Values, Filters</li>
  <li>Format value fields as currency and refresh when source data changes</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Source data must have <strong>one header row</strong> and no blank header cells.</li>
  <li><strong>Rows</strong> — categories (Course, Month, Staff name).</li>
  <li><strong>Values</strong> — usually <strong>Sum</strong> of Amount or <strong>Count</strong> of students.</li>
  <li>Changing source data → <strong>Right-click Pivot → Refresh</strong>.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Fee by course:</strong> Rows = Course, Values = Sum of Amount Paid.</p>
<p class="mb-3"><strong>Payment mode split:</strong> Rows = Mode (UPI/Cash/Bank), Values = Sum of Amount.</p>
<p class="mb-4"><strong>Monthly trend:</strong> Rows = Month, Values = Sum — quick line chart from Pivot output optional.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Select any cell inside your data range.</li>
  <li><strong>Insert → PivotTable</strong> → choose <strong>New Worksheet</strong> → OK.</li>
  <li>In the Fields pane, drag <strong>Course</strong> to <strong>Rows</strong>.</li>
  <li>Drag <strong>Amount</strong> (or Fee) to <strong>Values</strong> — ensure it says Sum, not Count.</li>
  <li>Right-click a value cell → <strong>Number Format</strong> → Currency ₹.</li>
  <li>Add a filter: drag <strong>Payment Mode</strong> to <strong>Filters</strong> if that column exists.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Blank rows inside data — breaks the range; clean data first.</li>
  <li>Summarising on text columns as Sum — switch to Count or fix data types.</li>
  <li>Expecting Pivot to update live without Refresh after editing source.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Build a sheet with 40+ payment rows: Date, Student, Course, Amount, Mode.</li>
  <li>Create a PivotTable: total <strong>Amount</strong> by <strong>Course</strong>.</li>
  <li>Create a second Pivot (or change layout): total by <strong>Mode</strong>.</li>
  <li>Change one row’s amount in source — <strong>Refresh</strong> and confirm totals update.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>PivotTables are the fastest way to summarise institute-scale spreadsheets. Learn them once — you will use them in every finance and admissions review.</p>
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
<h2 class="text-xl font-bold mb-4">4.1 Slides, layouts, and outline</h2>
<p class="mb-4">PowerPoint is how your institute presents <strong>training seminars</strong>, <strong>course orientation</strong>, <strong>placement briefings</strong>, and <strong>business proposals</strong>. The first skill is structure: choosing the right <strong>slide layout</strong> for each message so audiences stay focused.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Choose layouts: Title, Title and Content, Two Content</li>
  <li>Reorder slides in the thumbnail pane</li>
  <li>Plan a simple story: opening, agenda, topics, close</li>
  <li>Apply readable text density per slide</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Title Slide</strong> — programme name, date, presenter (institute branding).</li>
  <li><strong>Title and Content</strong> — one topic, bullets, or a single chart/image.</li>
  <li><strong>Two Content</strong> — compare “before/after”, “problem/solution”, or text + diagram.</li>
  <li><strong>Outline view</strong> — sanity-check flow before polishing design.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Institute introduction deck:</strong> Title → About us → Courses → Placements → Contact.</p>
<p class="mb-3"><strong>MS Office orientation:</strong> Agenda slide listing modules and duration.</p>
<p class="mb-4"><strong>Business presentation:</strong> One idea per slide; avoid paragraphs copied from Word.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li><strong>Home → New Slide</strong> dropdown — pick <strong>Title Slide</strong> for slide 1.</li>
  <li>Add slides with <strong>Title and Content</strong> for each main point.</li>
  <li>Drag thumbnails to reorder; use <strong>Ctrl+D</strong> to duplicate a slide template.</li>
  <li>Resize thumbnail pane if you need more room; zoom slide canvas for detail work.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Wall-of-text slides — audience reads instead of listening.</li>
  <li>Using Title-only layout but cramming bullets into the subtitle box awkwardly.</li>
  <li>No “closing” slide — always end with thanks + contact or Q&amp;A.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create <strong>5 slides</strong>: Title (“SoftPro — MS Office Orientation”), Agenda, three topic slides (Word / Excel / Outlook one line each), Thank you + contact.</li>
  <li>Reorder two topic slides and observe outline flow.</li>
  <li>Keep bullets to <strong>≤6 per slide</strong>, short phrases.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Layouts are the skeleton of professional decks. Master Title + Content first — most institute and corporate presentations live there.</p>
HTML,
                    ],
                    [
                        'title' => '4.2 Themes and Slide Master (logo once)',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">4.2 Themes and Slide Master (logo once)</h2>
<p class="mb-4">A <strong>theme</strong> gives every slide matching colours and fonts — essential for brand trust. <strong>Slide Master</strong> lets you place your <strong>institute logo</strong>, footer tagline, or default heading style <strong>once</strong>, instead of pasting on 40 slides. This is how paid training providers look polished.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Apply and switch Design themes</li>
  <li>Open Slide Master and edit parent layouts</li>
  <li>Place a logo in a consistent corner across layouts</li>
  <li>Return to Normal view and verify inheritance</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Design → Themes</strong> — instant colour/font harmony.</li>
  <li><strong>Slide Master</strong> — template layer; students should not edit Normal slides to “fix” branding — fix Master.</li>
  <li>Child layouts (Title, Content…) inherit from the top master slide.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Training seminar:</strong> Master slide shows logo + “SoftPro — Quality ICT Training”.</p>
<p class="mb-3"><strong>Report presentation:</strong> Footer on master: “Confidential — Internal Review”.</p>
<p class="mb-4"><strong>Institute intro:</strong> Theme colours match website for instant recognition.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li><strong>Design → Themes</strong> — pick a professional variant (avoid neon defaults for corporate talks).</li>
  <li><strong>View → Slide Master</strong> — scroll to the largest parent layout at top.</li>
  <li><strong>Insert → Pictures</strong> — place logo top-right; resize modestly.</li>
  <li>Adjust title font in master if needed (readable from back of classroom).</li>
  <li><strong>Slide Master → Close Master View</strong> — flip through slides: logo appears everywhere appropriate.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Logo on each slide manually — when slides are duplicated, some miss the logo.</li>
  <li>Oversized logo — steals content space.</li>
  <li>Editing only one layout in Master — another layout still shows old fonts.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Build a <strong>6-slide</strong> deck on any topic.</li>
  <li>Apply a theme; enter Slide Master and add a placeholder logo (any image).</li>
  <li>Change master title font size — confirm all title slides update.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Themes unify colour; Slide Master unifies structure and branding. Together they elevate student projects to institute-ready standard.</p>
HTML,
                    ],
                    [
                        'title' => '4.3 Images, icons, align tools',
                        'minutes' => 35,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">4.3 Images, icons, align tools</h2>
<p class="mb-4">Strong presentations mix <strong>text + visuals</strong>. Icons and photos support memory; alignment tools make messy slides look <strong>designed</strong>, not pasted. Use this lesson for institute promo decks, lab rules slides, and business pitch visuals.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Insert pictures from device or stock where available</li>
  <li>Insert icons (Microsoft 365) for simple metaphors</li>
  <li>Select multiple objects and align / distribute evenly</li>
  <li>Keep file size reasonable with sensible image dimensions</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Insert → Pictures</strong> — campus photo, product shot, diagram.</li>
  <li><strong>Insert → Icons</strong> — clock, book, certificate symbols for bullet substitutes.</li>
  <li><strong>Align / Distribute</strong> — line up icons in a row with equal spacing.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Course benefits slide:</strong> Three icons — Employability, Certificates, Support — with one line each.</p>
<p class="mb-3"><strong>Training seminar:</strong> Photo of classroom + short caption, aligned to grid.</p>
<p class="mb-4"><strong>Report presentation:</strong> Screenshot of Excel chart pasted as image for static snapshot.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li><strong>Insert → Icons</strong> — search “computer”, insert three icons.</li>
  <li>Hold <strong>Shift</strong>, click each icon to multi-select.</li>
  <li><strong>Shape Format → Align → Align Middle</strong>; then <strong>Distribute → Horizontally</strong>.</li>
  <li>Add short text boxes under each icon; align text boxes to the same grid.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Stretching low-resolution images full-screen — blurry projection.</li>
  <li>Random icon styles mixed (outline + flat + 3D) — pick one family per deck.</li>
  <li>Overlapping icons without Align — looks accidental.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Create one slide “<strong>Lab rules</strong>” with <strong>3 icons</strong> and captions.</li>
  <li>Align icons horizontally; captions directly below, centred.</li>
  <li>Optional: add one <strong>related photo</strong> cropped to a clean rectangle.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Icons and images shorten reading time; alignment communicates care. Both signal professional training quality to students and visitors.</p>
HTML,
                    ],
                    [
                        'title' => '4.4 Transitions and restrained animation',
                        'minutes' => 40,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">4.4 Transitions and restrained animation</h2>
<p class="mb-4">Motion should <strong>guide attention</strong>, not distract. Corporate trainers and institute faculty use <strong>subtle transitions</strong> between slides and <strong>restrained animation</strong> on bullet lists so students follow the speaker — not watch special effects. Flashy transitions signal amateur decks.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Apply one consistent slide transition (e.g. Fade)</li>
  <li>Animate bullet paragraphs to appear sequentially</li>
  <li>Use the Animation Pane to reorder and time effects</li>
  <li>Know when <strong>not</strong> to animate (data-heavy slides)</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Transition</strong> — between slides (whole slide change).</li>
  <li><strong>Animation</strong> — on shapes/text inside a slide.</li>
  <li><strong>Start On Click vs After Previous</strong> — controls pacing during live teaching.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Training seminar:</strong> Fade transition; topic bullets appear one-by-one as you explain.</p>
<p class="mb-3"><strong>Business presentation:</strong> Single subtle Push transition for entire deck — not 20 different effects.</p>
<p class="mb-4"><strong>Institute demo day:</strong> Animate only keywords on “Why choose us?” slide.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Select a slide thumbnail → <strong>Transitions</strong> → choose <strong>Fade</strong> → <strong>Apply to All</strong> for consistency.</li>
  <li>On a content slide, click the text box → <strong>Animations → Appear</strong> (or Wipe).</li>
  <li><strong>Animation Pane</strong> — expand entry, set bullet list to animate <strong>By Paragraph</strong> (wording varies by version).</li>
  <li>Set each paragraph to <strong>Start After Previous</strong> for auto flow, or <strong>On Click</strong> for classroom control.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Different loud transition every slide — tiring for audience.</li>
  <li>Long animation delays on data slides — slows serious meetings.</li>
  <li>Spinning logos or bouncing text in formal institute contexts.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Pick a 4-slide deck; apply <strong>one</strong> transition type to all.</li>
  <li>On slide 2, animate a bullet list <strong>paragraph by paragraph</strong>.</li>
  <li>Practice presenting aloud — each click reveals the next idea.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Restraint is professionalism: Fade + simple Appear animations carry institute and business presentations further than gimmicks.</p>
HTML,
                    ],
                    [
                        'title' => '4.5 Presenter View and Slide Show',
                        'minutes' => 30,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">4.5 Presenter View and Slide Show</h2>
<p class="mb-4">Delivering a deck is a performance skill. <strong>Presenter View</strong> (with two screens or one screen + practice) shows <strong>your notes</strong>, <strong>next slide</strong>, and a <strong>timer</strong> — while students see only the full-slide view. Trainers use this for <strong>seminars</strong>, <strong>faculty meetings</strong>, and <strong>placement briefings</strong>.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Start slideshow from beginning vs current slide</li>
  <li>Navigate slides with keyboard shortcuts safely</li>
  <li>Use Presenter View when available</li>
  <li>Use laser / pen tools without leaving slideshow</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>F5</strong> — from first slide.</li>
  <li><strong>Shift+F5</strong> — from current slide (great for rehearsal).</li>
  <li><strong>Presenter View</strong> — second monitor or laptop screen split; audience projector shows slideshow.</li>
  <li><strong>Speaker notes</strong> — typed in Notes pane below slide in Normal view.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Institute open house:</strong> Presenter sees timing cues; parents see clean slides only.</p>
<p class="mb-3"><strong>Report presentation:</strong> Notes hold data citations you do not put on-slide.</p>
<p class="mb-4"><strong>Student project defence:</strong> Laser pointer highlights chart areas during Q&amp;A.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Add short <strong>speaker notes</strong> on 2–3 slides (View → Normal → drag Notes pane up).</li>
  <li>Connect second screen or use Win+P “Extend” before class.</li>
  <li><strong>Slide Show → Presenter View</strong> (or check “Use Presenter View” in Setup Slideshow).</li>
  <li>Press <strong>F5</strong>; practise moving forward with <strong>Space / Click</strong>, backward with <strong>P</strong> or arrow keys (learn your setup).</li>
  <li>During show, try <strong>Ctrl+L</strong> for temporary laser pointer (version dependent).</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Forgetting to test projector before audience arrives.</li>
  <li>Reading slides verbatim — notes should prompt, not replace teaching.</li>
  <li>Escaping slideshow accidentally — practise pause/resume flow.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Prepare a <strong>5-minute</strong> mini-deck on “Why learn MS Office?”.</li>
  <li>Add notes on at least <strong>2 slides</strong>.</li>
  <li>Rehearse aloud once with a timer; trim one overloaded slide.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Great slides plus confident delivery equals professional training. Presenter View and rehearsed timing separate institute-grade presenters from beginners.</p>
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
<h2 class="text-xl font-bold mb-4">5.1 Outlook: inbox, compose, attachments, signature</h2>
<p class="mb-4">Email is still the backbone of <strong>official communication</strong>: fee receipts, admission letters, employer references, and vendor quotes. Outlook (or similar work email) expects <strong>clear subjects</strong>, <strong>proper attachments</strong>, and <strong>professional signatures</strong>. This lesson matches real office expectations.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Write subject lines that help recipients search and prioritise</li>
  <li>Compose a polite, concise business email body</li>
  <li>Attach PDFs safely and mention them in the message</li>
  <li>Create or update an email signature with name, role, phone</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Important concepts</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Subject</strong> — summarise in ~6–10 words: “Fee receipt — March 2026 — Your Name”.</li>
  <li><strong>To / Cc</strong> — To = must act; Cc = informed; avoid reply-all noise.</li>
  <li><strong>Attachments</strong> — PDF for read-only official docs; check size before sending.</li>
  <li><strong>Signature</strong> — closes every mail consistently with contact details.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practical examples</h3>
<p class="mb-3"><strong>Admission confirmation:</strong> Subject includes course + batch; body references attachment “Admission_Letter.pdf”.</p>
<p class="mb-3"><strong>Fee reminder:</strong> Short bullet list of amount due, due date, payment link or bank details.</p>
<p class="mb-4"><strong>Employer outreach:</strong> Formal greeting, one paragraph purpose, signature with institute letterhead block.</p>
<h3 class="text-lg font-semibold mb-2">Step-by-step</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li><strong>New Email</strong> — fill <strong>To</strong> with your own address for practice.</li>
  <li>Type subject: <code>MS Office course — practice mail</code>.</li>
  <li>Body: greeting (e.g. “Dear Sir/Madam,”), 2–3 sentences, call-to-action, “Regards,” + name.</li>
  <li><strong>Attach</strong> a small PDF (any practice file).</li>
  <li><strong>File → Options → Mail → Signatures</strong> (path varies) — create signature with phone and role; assign to New messages.</li>
  <li>Create folders: <strong>Students</strong>, <strong>Fees</strong>, <strong>Admin</strong> — drag one practice mail into a folder.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Vague subjects (“Hello”, “Update”) — hard to find later.</li>
  <li>Attaching without mentioning it — recipients miss the file.</li>
  <li>Emotional tone or ALL CAPS — unprofessional in workplace mail.</li>
  <li>Sending large uncompressed images instead of PDF — blocked or spam-flagged.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Email yourself: subject includes course name + your roll/enrolment id.</li>
  <li>Attach one PDF; first line of body says “Please find attached…”.</li>
  <li>Enable a signature with <strong>name, course, mobile</strong>.</li>
  <li>File the sent item into a folder you created.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>Professional email is searchable, concise, and attachment-aware. Master subject lines and signatures before you represent any institute externally.</p>
HTML,
                    ],
                    [
                        'title' => '5.2 Final portfolio project (assessment brief)',
                        'minutes' => 90,
                        'body' => <<<'HTML'
<h2 class="text-xl font-bold mb-4">5.2 Final portfolio project (assessment brief)</h2>
<p class="mb-4">This <strong>capstone</strong> proves you can deliver a realistic office package: a formal <strong>Word</strong> document, a data-driven <strong>Excel</strong> workbook, and a presentation-ready <strong>PowerPoint</strong> deck — the same trio used in training institutes and SMEs. Treat this like a <strong>paid client submission</strong>: correct naming, clean formatting, and zero broken references.</p>
<h3 class="text-lg font-semibold mb-2">Learning objectives</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Integrate Word + Excel + PowerPoint skills from Modules 1–5</li>
  <li>Produce print-ready and presenter-ready artefacts</li>
  <li>Follow a professional file naming and packaging convention</li>
  <li>Self-check work against a clear rubric before submission</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Scenario (use this storyline)</h3>
<p class="mb-4">You work for <strong>SoftPro Training Institute</strong>. Prepare materials for an <strong>MS Office evening batch</strong>: a public notice about the batch, a fee/marks tracker for internal use, and a slide deck to pitch the programme to visiting students.</p>
<h3 class="text-lg font-semibold mb-2">Deliverable A — Microsoft Word</h3>
<p class="mb-3"><strong>Goal:</strong> 1–2 page <strong>official notice</strong> (A4).</p>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Use <strong>Heading 1</strong> for the notice title and <strong>Heading 2</strong> for sections (e.g. Course outline, Fee, Schedule, Contact).</li>
  <li>Include one <strong>table</strong> (fee breakdown OR batch timetable).</li>
  <li>Add a <strong>header</strong> with institute name; <strong>page numbers</strong> from page 2 onward (cover-style page 1 optional).</li>
  <li>Insert at least one <strong>picture</strong> (logo or stock training image) with sensible text wrap.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Deliverable B — Microsoft Excel</h3>
<p class="mb-3"><strong>Goal:</strong> Mini <strong>admission &amp; fee register</strong> (minimum 15 sample students).</p>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Columns should include: Student name, Course, Fee due, Fee paid, Balance, Payment status (Paid/Pending), and at least two numeric columns suitable for marks or attendance days.</li>
  <li>Use <code>SUM</code> / <code>AVERAGE</code> for totals; use <code>IF</code> or <code>COUNTIF</code> for pass/fee summaries.</li>
  <li>Apply <strong>₹ currency</strong>, sensible <strong>dates</strong>, and <strong>percent</strong> where appropriate.</li>
  <li>Insert one <strong>chart</strong> (e.g. fee collected by course OR monthly trend — label axes).</li>
  <li>Set <strong>print titles</strong> so row 1 repeats; verify in Print Preview.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Deliverable C — Microsoft PowerPoint</h3>
<p class="mb-3"><strong>Goal:</strong> Institutional <strong>marketing + orientation</strong> deck.</p>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Minimum <strong>8 slides</strong> (Title, Agenda, programme benefits, module overview, fee/table snapshot, career relevance, student support, Contact/Q&amp;A).</li>
  <li>Apply a <strong>theme</strong>; place institute <strong>logo via Slide Master</strong>.</li>
  <li>Include at least one slide with <strong>icons or images</strong> aligned cleanly.</li>
  <li>Use <strong>consistent transition</strong> + <strong>restrained animation</strong> on one content slide (bullets by paragraph).</li>
  <li>No wall-of-text: largest body slide ≤ 6 short bullets.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Submission checklist</h3>
<ol class="list-decimal pl-5 mb-4 space-y-2">
  <li>Name files: <code>YourName_MSOffice_Word.docx</code>, <code>YourName_MSOffice_Excel.xlsx</code>, <code>YourName_MSOffice_PowerPoint.pptx</code>.</li>
  <li>Spell-check all three; broken formulas = automatic deduction.</li>
  <li>Zip together if your LMS accepts one upload: <code>YourName_MSOffice_Portfolio.zip</code>.</li>
</ol>
<h3 class="text-lg font-semibold mb-2">Grading rubric (suggested)</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li><strong>Word structure &amp; polish</strong> — 25% (styles, table, header/page numbers, image)</li>
  <li><strong>Excel accuracy &amp; formats</strong> — 35% (functions, chart, print setup)</li>
  <li><strong>PowerPoint design &amp; Master</strong> — 25% (theme, logo, animation discipline)</li>
  <li><strong>Professional finish</strong> — 15% (naming, completeness, no placeholder “Lorem ipsum”)</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Common mistakes</h3>
<ul class="list-disc pl-5 mb-4 space-y-1">
  <li>Pasting Excel tables as screenshots only — we need a live workbook.</li>
  <li>Charts without titles or currency units.</li>
  <li>PowerPoint logos pasted per slide instead of Slide Master.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Practice exercise</h3>
<p class="mb-4">Draft a <strong>timeline</strong> (on paper first): Day 1 Word, Day 2 Excel, Day 3 PowerPoint, Day 4 rehearse presentation + final export. Submit only when all checklist boxes are ticked.</p>
<h3 class="text-lg font-semibold mb-2">Summary</h3>
<p>This portfolio demonstrates job-ready fluency across the Office suite. Finished work should look like it came from a <strong>professional training institute</strong>, not a quick homework draft.</p>
HTML,
                    ],
                ],
            ],
        ];
    }
}
