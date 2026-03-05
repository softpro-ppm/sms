<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionBank;
use App\Models\Course;

class MSOfficeQuestionBankSeeder extends Seeder
{
    public function run()
    {
        // Find MS Office course (assuming it exists)
        $course = Course::where('name', 'like', '%MS Office%')->first();
        
        if (!$course) {
            $this->command->error('MS Office course not found. Please create the course first.');
            return;
        }

        $questions = [
            // MS Office Fundamentals
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'What is Microsoft Office?',
                'option_a' => 'A software suite',
                'option_b' => 'A single program',
                'option_c' => 'A hardware device',
                'option_d' => 'An operating system',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'Which applications are included in MS Office?',
                'option_a' => 'Word, Excel, PowerPoint, Paint',
                'option_b' => 'Only Word',
                'option_c' => 'Only Excel',
                'option_d' => 'Word and Excel only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'What is the purpose of MS Office?',
                'option_a' => 'Productivity and office tasks',
                'option_b' => 'Gaming',
                'option_c' => 'Web browsing',
                'option_d' => 'Email only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'MS Office is developed by which company?',
                'option_a' => 'Microsoft',
                'option_b' => 'Google',
                'option_c' => 'Apple',
                'option_d' => 'IBM',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'What file format is commonly used in MS Office?',
                'option_a' => 'DOCX, XLSX, PPTX',
                'option_b' => 'PDF only',
                'option_c' => 'TXT only',
                'option_d' => 'JPG only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'What is the main advantage of MS Office?',
                'option_a' => 'Integrated productivity tools',
                'option_b' => 'Free software',
                'option_c' => 'Gaming features',
                'option_d' => 'Internet browsing',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'Which version of MS Office is commonly used?',
                'option_a' => 'Office 365 and Office 2021',
                'option_b' => 'Office 95 only',
                'option_c' => 'Office 2000 only',
                'option_d' => 'Office 2003 only',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'What is cloud integration in MS Office?',
                'option_a' => 'Access files from anywhere',
                'option_b' => 'Local storage only',
                'option_c' => 'Offline mode only',
                'option_d' => 'No internet required',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'What is collaboration in MS Office?',
                'option_a' => 'Multiple users working together',
                'option_b' => 'Single user only',
                'option_c' => 'Offline work only',
                'option_d' => 'No sharing allowed',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Office Fundamentals',
                'question_text' => 'What is the Ribbon in MS Office?',
                'option_a' => 'Toolbar with commands',
                'option_b' => 'File format',
                'option_c' => 'Hardware device',
                'option_d' => 'Operating system',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],

            // MS Word
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'What is the default file extension for Word documents?',
                'option_a' => '.docx',
                'option_b' => '.txt',
                'option_c' => '.pdf',
                'option_d' => '.xlsx',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'Which shortcut is used to save a document?',
                'option_a' => 'Ctrl+S',
                'option_b' => 'Ctrl+A',
                'option_c' => 'Ctrl+C',
                'option_d' => 'Ctrl+V',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'What is the purpose of MS Word?',
                'option_a' => 'Word processing',
                'option_b' => 'Spreadsheet creation',
                'option_c' => 'Image editing',
                'option_d' => 'Email management',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'Which shortcut is used to copy text?',
                'option_a' => 'Ctrl+C',
                'option_b' => 'Ctrl+V',
                'option_c' => 'Ctrl+X',
                'option_d' => 'Ctrl+Z',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'Which shortcut is used to paste text?',
                'option_a' => 'Ctrl+V',
                'option_b' => 'Ctrl+C',
                'option_c' => 'Ctrl+X',
                'option_d' => 'Ctrl+Z',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'What is the purpose of the Home tab?',
                'option_a' => 'Basic formatting tools',
                'option_b' => 'File operations',
                'option_c' => 'Page layout',
                'option_d' => 'Review features',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'Which shortcut is used to undo an action?',
                'option_a' => 'Ctrl+Z',
                'option_b' => 'Ctrl+Y',
                'option_c' => 'Ctrl+X',
                'option_d' => 'Ctrl+C',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'What is the purpose of the Insert tab?',
                'option_a' => 'Insert objects and elements',
                'option_b' => 'Basic formatting',
                'option_c' => 'Page layout',
                'option_d' => 'Review features',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'Which shortcut is used to select all text?',
                'option_a' => 'Ctrl+A',
                'option_b' => 'Ctrl+S',
                'option_c' => 'Ctrl+C',
                'option_d' => 'Ctrl+V',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Word',
                'question_text' => 'What is the purpose of the Page Layout tab?',
                'option_a' => 'Page setup and formatting',
                'option_b' => 'Basic formatting',
                'option_c' => 'Insert objects',
                'option_d' => 'Review features',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],

            // MS Excel
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'What is a cell in Excel?',
                'option_a' => 'Intersection of row and column',
                'option_b' => 'A single row',
                'option_c' => 'A single column',
                'option_d' => 'A worksheet',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'Which function is used to add numbers?',
                'option_a' => 'SUM',
                'option_b' => 'ADD',
                'option_c' => 'PLUS',
                'option_d' => 'TOTAL',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'What is the default file extension for Excel files?',
                'option_a' => '.xlsx',
                'option_b' => '.docx',
                'option_c' => '.txt',
                'option_d' => '.pdf',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'What is the purpose of MS Excel?',
                'option_a' => 'Spreadsheet and data analysis',
                'option_b' => 'Word processing',
                'option_c' => 'Image editing',
                'option_d' => 'Email management',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'Which function is used to find the average?',
                'option_a' => 'AVERAGE',
                'option_b' => 'MEAN',
                'option_c' => 'AVG',
                'option_d' => 'TOTAL',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'What is a formula in Excel?',
                'option_a' => 'Mathematical expression',
                'option_b' => 'Text only',
                'option_c' => 'Image file',
                'option_d' => 'Video file',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'Which function is used to count cells?',
                'option_a' => 'COUNT',
                'option_b' => 'TOTAL',
                'option_c' => 'SUM',
                'option_d' => 'ADD',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'What is a worksheet in Excel?',
                'option_a' => 'A single sheet in a workbook',
                'option_b' => 'A single cell',
                'option_c' => 'A single row',
                'option_d' => 'A single column',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'Which function is used to find the maximum value?',
                'option_a' => 'MAX',
                'option_b' => 'HIGHEST',
                'option_c' => 'TOP',
                'option_d' => 'BIGGEST',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Excel',
                'question_text' => 'What is a workbook in Excel?',
                'option_a' => 'Collection of worksheets',
                'option_b' => 'Single worksheet',
                'option_c' => 'Single cell',
                'option_d' => 'Single row',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],

            // MS PowerPoint
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'How do you add a new slide?',
                'option_a' => 'Ctrl+M',
                'option_b' => 'Ctrl+N',
                'option_c' => 'Ctrl+O',
                'option_d' => 'Ctrl+P',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'What is a slide in PowerPoint?',
                'option_a' => 'A single page',
                'option_b' => 'A document',
                'option_c' => 'A spreadsheet',
                'option_d' => 'A database',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'What is the purpose of MS PowerPoint?',
                'option_a' => 'Presentation creation',
                'option_b' => 'Word processing',
                'option_c' => 'Spreadsheet creation',
                'option_d' => 'Image editing',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'What is the default file extension for PowerPoint files?',
                'option_a' => '.pptx',
                'option_b' => '.docx',
                'option_c' => '.xlsx',
                'option_d' => '.txt',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'Which shortcut is used to start a slideshow?',
                'option_a' => 'F5',
                'option_b' => 'F1',
                'option_c' => 'F2',
                'option_d' => 'F3',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'What is the purpose of the Design tab?',
                'option_a' => 'Slide design and themes',
                'option_b' => 'Basic formatting',
                'option_c' => 'Insert objects',
                'option_d' => 'Review features',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'Which shortcut is used to duplicate a slide?',
                'option_a' => 'Ctrl+D',
                'option_b' => 'Ctrl+C',
                'option_c' => 'Ctrl+V',
                'option_d' => 'Ctrl+X',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'What is the purpose of the Animations tab?',
                'option_a' => 'Add slide transitions and animations',
                'option_b' => 'Basic formatting',
                'option_c' => 'Insert objects',
                'option_d' => 'Review features',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'Which shortcut is used to save a presentation?',
                'option_a' => 'Ctrl+S',
                'option_b' => 'Ctrl+A',
                'option_c' => 'Ctrl+C',
                'option_d' => 'Ctrl+V',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS PowerPoint',
                'question_text' => 'What is the purpose of the Slide Show tab?',
                'option_a' => 'Control presentation playback',
                'option_b' => 'Basic formatting',
                'option_c' => 'Insert objects',
                'option_d' => 'Review features',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],

            // MS Paint
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'What is MS Paint used for?',
                'option_a' => 'Image editing',
                'option_b' => 'Word processing',
                'option_c' => 'Spreadsheet creation',
                'option_d' => 'Email management',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'Which tool is used to fill areas with color?',
                'option_a' => 'Paint Bucket',
                'option_b' => 'Pencil',
                'option_c' => 'Brush',
                'option_d' => 'Eraser',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'What is the default file extension for Paint files?',
                'option_a' => '.png',
                'option_b' => '.docx',
                'option_c' => '.xlsx',
                'option_d' => '.txt',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'Which tool is used to draw straight lines?',
                'option_a' => 'Line tool',
                'option_b' => 'Pencil',
                'option_c' => 'Brush',
                'option_d' => 'Eraser',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'What is the purpose of the Eraser tool?',
                'option_a' => 'Remove parts of the image',
                'option_b' => 'Add color',
                'option_c' => 'Draw lines',
                'option_d' => 'Fill areas',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'Which tool is used to select rectangular areas?',
                'option_a' => 'Rectangle Select',
                'option_b' => 'Free-form Select',
                'option_c' => 'Pencil',
                'option_d' => 'Brush',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'What is the purpose of the Color Picker tool?',
                'option_a' => 'Select colors from the image',
                'option_b' => 'Fill areas with color',
                'option_c' => 'Draw lines',
                'option_d' => 'Erase parts',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'Which tool is used to draw freehand lines?',
                'option_a' => 'Pencil',
                'option_b' => 'Line tool',
                'option_c' => 'Rectangle',
                'option_d' => 'Eraser',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'What is the purpose of the Brush tool?',
                'option_a' => 'Paint with various brush sizes',
                'option_b' => 'Draw straight lines',
                'option_c' => 'Fill areas with color',
                'option_d' => 'Erase parts',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ],
            [
                'course_id' => $course->id,
                'subject' => 'MS Paint',
                'question_text' => 'Which shortcut is used to save an image?',
                'option_a' => 'Ctrl+S',
                'option_b' => 'Ctrl+A',
                'option_c' => 'Ctrl+C',
                'option_d' => 'Ctrl+V',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy'
            ]
        ];

        foreach ($questions as $question) {
            QuestionBank::create($question);
        }

        $this->command->info('MS Office question bank seeded successfully!');
        $this->command->info('Total questions created: ' . count($questions));
        $this->command->info('Subjects: MS Office Fundamentals, MS Word, MS Excel, MS PowerPoint, MS Paint');
    }
}
