<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    private $faker;
    
    // Pakistani student names - Male
    private $maleFirstNames = [
        'Ahmed', 'Bilal', 'Daniyal', 'Ehsan', 'Fahad', 'Ghazi', 'Hamza', 'Ibrahim', 
        'Junaid', 'Kamran', 'Moiz', 'Nabeel', 'Omar', 'Qasim', 'Rayan', 'Saad', 
        'Taha', 'Usman', 'Waleed', 'Yahya', 'Zaid', 'Ali', 'Hassan', 'Hussain', 
        'Abdullah', 'Rehan', 'Shayan', 'Farhan', 'Arham', 'Ayaan', 'Mustafa', 'Zain',
        'Arslan', 'Baber', 'Dawood', 'Faizan', 'Haroon', 'Imran', 'Kashif', 'Luqman',
        'Mikaeel', 'Noman', 'Owais', 'Rizwan', 'Salman', 'Tahir', 'Umair', 'Wasim',
        'Adeel', 'Basit', 'Danish', 'Faisal', 'Haider', 'Irfan', 'Jawad', 'Khalid'
    ];
    
    private $maleLastNames = [
        'Khan', 'Ahmed', 'Butt', 'Chaudhry', 'Dar', 'Ejaz', 'Farooq', 'Ghafoor',
        'Hashmi', 'Iqbal', 'Javed', 'Kamal', 'Lodhi', 'Malik', 'Naeem', 'Qureshi',
        'Rana', 'Sheikh', 'Tariq', 'Umar', 'Waseem', 'Yousaf', 'Zafar', 'Siddiqui',
        'Rashid', 'Nasir', 'Mahmood', 'Shah', 'Aziz', 'Latif', 'Anwar', 'Hameed',
        'Abbasi', 'Bhatti', 'Cheema', 'Dogar', 'Gill', 'Jutt', 'Khokhar', 'Mughal'
    ];
    
    private $femaleFirstNames = [
        'Ayesha', 'Bisma', 'Dua', 'Eman', 'Fatima', 'Ghazala', 'Hira', 'Iqra',
        'Javeria', 'Kainat', 'Laiba', 'Mahnoor', 'Nimra', 'Omama', 'Qurat', 'Rida',
        'Sana', 'Tehreem', 'Uzma', 'Warda', 'Yusra', 'Zainab', 'Amina', 'Bushra',
        'Khadija', 'Maryam', 'Noor', 'Sadia', 'Tahira', 'Zara', 'Anum', 'Sara',
        'Areeba', 'Dania', 'Fiza', 'Hania', 'Jannat', 'Kinza', 'Lubna', 'Mehak',
        'Nashra', 'Palwasha', 'Ramsha', 'Sidra', 'Tayyaba', 'Unzila', 'Wajiha', 'Zoha',
        'Aleena', 'Bareera', 'Durre', 'Eshal', 'Hafsa', 'Inaya', 'Jiya', 'Minahil'
    ];
    
    private $femaleLastNames = [
        'Khan', 'Ahmed', 'Butt', 'Chaudhry', 'Dar', 'Fatima', 'Ghafoor', 'Hashmi',
        'Iqbal', 'Javed', 'Khalid', 'Lodhi', 'Malik', 'Naeem', 'Qureshi', 'Rana',
        'Sheikh', 'Tariq', 'Umar', 'Waseem', 'Yousaf', 'Zafar', 'Siddiqui', 'Rashid',
        'Nasir', 'Mahmood', 'Shah', 'Aziz', 'Batool', 'Hassan', 'Hussain', 'Abbasi',
        'Bhatti', 'Cheema', 'Gill', 'Jutt', 'Mughal', 'Syed', 'Waraich', 'Zaidi'
    ];
    
    private $fatherNames = [
        'Muhammad Aslam', 'Muhammad Akram', 'Abdul Rehman', 'Ghulam Mustafa', 
        'Muhammad Iqbal', 'Abdul Hameed', 'Muhammad Yousaf', 'Noor Muhammad',
        'Muhammad Ashraf', 'Ahmed Khan', 'Rana Muhammad', 'Sheikh Rashid',
        'Muhammad Arshad', 'Abdul Ghafoor', 'Muhammad Anwar', 'Ghulam Rasool',
        'Muhammad Saeed', 'Muhammad Asif', 'Abdul Rasheed', 'Muhammad Nawaz',
        'Mehmood Ahmed', 'Muhammad Irfan', 'Muhammad Waseem', 'Abdul Sattar',
        'Muhammad Khalid', 'Muhammad Ramzan', 'Muhammad Saleem', 'Syed Imran',
        'Muhammad Ayub', 'Abdul Ghani', 'Muhammad Shafiq', 'Abdul Majeed',
        'Muhammad Rafiq', 'Muhammad Arif', 'Abdul Qadir', 'Muhammad Latif',
        'Muhammad Bashir', 'Muhammad Sharif', 'Ghulam Nabi', 'Muhammad Amin'
    ];
    
    private $cities = [
        'Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Faisalabad', 
        'Multan', 'Gujranwala', 'Peshawar', 'Quetta', 'Sialkot',
        'Sargodha', 'Bahawalpur', 'Gujrat', 'Jhelum', 'Sheikhupura',
        'Rahim Yar Khan', 'Sahiwal', 'Wah Cantt', 'Mardan', 'Abbottabad'
    ];

    // Pakistani Textbook Names
    private $textbooks = [
        'KG' => [
            'English' => 'New Oxford Modern English Primer B',
            'Urdu' => 'Urdu Ki Pehli Kitab',
            'Maths' => 'New Countdown Primer B',
            'General Knowledge' => 'Exploring the World KG',
            'Drawing' => 'Creative Arts for KG',
            'Islamiyat' => 'Islamiyat for Beginners'
        ],
        '1' => [
            'English' => 'New Oxford Modern English Book 1',
            'Urdu' => 'Urdu Ki Darsi Kitab 1',
            'Maths' => 'New Countdown Book 1',
            'General Knowledge' => 'Know Your World Book 1',
            'Islamiyat' => 'Islamiyat Book 1',
            'Computer' => 'Computer Whiz Book 1'
        ],
        '2' => [
            'English' => 'New Oxford Modern English Book 2',
            'Urdu' => 'Urdu Ki Darsi Kitab 2',
            'Maths' => 'New Countdown Book 2',
            'Science' => 'New Amazing Science Book 2',
            'Islamiyat' => 'Islamiyat Book 2',
            'Computer' => 'Computer Whiz Book 2',
            'Social Studies' => 'World Watch Social Studies Book 2'
        ],
        '3' => [
            'English' => 'New Oxford Modern English Book 3',
            'Urdu' => 'Urdu Ki Darsi Kitab 3',
            'Maths' => 'New Countdown Book 3',
            'Science' => 'New Amazing Science Book 3',
            'Islamiyat' => 'Islamiyat Book 3',
            'Computer' => 'Computer Whiz Book 3',
            'Social Studies' => 'World Watch Social Studies Book 3'
        ],
        '4' => [
            'English' => 'New Oxford Modern English Book 4',
            'Urdu' => 'Urdu Ki Darsi Kitab 4',
            'Maths' => 'New Countdown Book 4',
            'Science' => 'New Amazing Science Book 4',
            'Islamiyat' => 'Islamiyat Book 4',
            'Computer' => 'Computer Whiz Book 4',
            'Social Studies' => 'World Watch Social Studies Book 4'
        ],
        '5' => [
            'English' => 'New Oxford Modern English Book 5',
            'Urdu' => 'Urdu Ki Darsi Kitab 5',
            'Maths' => 'New Countdown Book 5',
            'Science' => 'New Amazing Science Book 5',
            'Islamiyat' => 'Islamiyat Book 5',
            'Computer' => 'Computer Whiz Book 5',
            'Social Studies' => 'World Watch Social Studies Book 5'
        ],
        '6' => [
            'English' => 'Oxford Progressive English Book 6',
            'Urdu' => 'Urdu Ki Darsi Kitab 6',
            'Maths' => 'New Syllabus Mathematics Book 1',
            'Science' => 'Science Fact File Book 1',
            'Islamiyat' => 'Islamiyat Book 6',
            'Computer' => 'Computer Whiz Book 6',
            'Social Studies' => 'World Watch Social Studies Book 6',
            'History' => 'History of Pakistan Book 6',
            'Geography' => 'Geography Today Book 1'
        ],
        '7' => [
            'English' => 'Oxford Progressive English Book 7',
            'Urdu' => 'Urdu Ki Darsi Kitab 7',
            'Maths' => 'New Syllabus Mathematics Book 2',
            'Science' => 'Science Fact File Book 2',
            'Islamiyat' => 'Islamiyat Book 7',
            'Computer' => 'Computer Whiz Book 7',
            'Social Studies' => 'World Watch Social Studies Book 7',
            'History' => 'History of Pakistan Book 7',
            'Geography' => 'Geography Today Book 2'
        ],
        '8' => [
            'English' => 'Oxford Progressive English Book 8',
            'Urdu' => 'Urdu Ki Darsi Kitab 8',
            'Maths' => 'New Syllabus Mathematics Book 3',
            'Science' => 'Science Fact File Book 3',
            'Islamiyat' => 'Islamiyat Book 8',
            'Computer' => 'Computer Whiz Book 8',
            'Social Studies' => 'World Watch Social Studies Book 8',
            'History' => 'History of Pakistan Book 8',
            'Geography' => 'Geography Today Book 3'
        ],
        '9' => [
            'English' => 'Oxford Progressive English Book 9',
            'Urdu' => 'Urdu Lazmi Book 9',
            'Maths' => 'New Syllabus Mathematics D Book 4',
            'Islamiyat' => 'Islamiyat Lazmi Book 9',
            'Pakistan Studies' => 'Pakistan Studies Book 9',
            'Physics' => 'Physics for Class 9 - PTB',
            'Chemistry' => 'Chemistry for Class 9 - PTB',
            'Biology' => 'Biology for Class 9 - PTB',
            'Computer' => 'Computer Science Book 9',
            'General Science' => 'General Science Book 9',
            'Economics' => 'Principles of Economics Book 9',
            'Civics' => 'Civics and Community Engagement Book 9',
            'Home Economics' => 'Home Economics Book 9',
            'Arabic' => 'Arabic for Secondary Classes Book 9',
        ],
        '10' => [
            'English' => 'Oxford Progressive English Book 10',
            'Urdu' => 'Urdu Lazmi Book 10',
            'Maths' => 'New Syllabus Mathematics D Book 5',
            'Islamiyat' => 'Islamiyat Lazmi Book 10',
            'Pakistan Studies' => 'Pakistan Studies Book 10',
            'Physics' => 'Physics for Class 10 - PTB',
            'Chemistry' => 'Chemistry for Class 10 - PTB',
            'Biology' => 'Biology for Class 10 - PTB',
            'Computer' => 'Computer Science Book 10',
            'General Science' => 'General Science Book 10',
            'Economics' => 'Principles of Economics Book 10',
            'Civics' => 'Civics and Community Engagement Book 10',
            'Home Economics' => 'Home Economics Book 10',
            'Arabic' => 'Arabic for Secondary Classes Book 10',
        ],
    ];

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('🏫 COMPLETE PAKISTANI SCHOOL DATABASE SEEDER');
        $this->command->info('   KG to 10th Grade (Science & Arts)');
        $this->command->info('========================================');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate all tables
        $tables = [
            'student_discounts', 'invoices', 'student_fee_submissions',
            'student_attendance_summaries', 'student_attendance',
            'attendance_summaries', 'staff_attendances', 'leaves', 'salaries',
            'payroll_items', 'payroll_batches', 'salary_templates',
            'teacher_class_attendance', 'attendance_teacher_section', 'common_subject_classes',
            'timetable_entries', 'teacher_availabilities', 'teacher_subjects',
            'subject_assignments', 'time_slots', 'school_timings',
            'exam_results', 'exam_marks', 'exams', 'exam_groups', 'exam_types',
            'grade_scales',
            'students', 'class_sections', 'classes', 'elective_tracks', 'streams', 'grades',
            'academic_sessions', 'subjects',
            'payment_methods', 'banks', 'discounts', 'fee_submission_types', 'fee_types',
            'staff', 'employee_categories',
            'rooms', 'floors', 'blocks',
            'certificate_distributions', 'certificate_types', 'transfer_certificates', 'student_transfers',
            'attendance_reasons',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("Truncated: {$table}");
            }
        }
        $this->command->info("✅ All tables truncated");

        // ==============================================
        // 1. ATTENDANCE REASONS
        // ==============================================
        $this->command->info("\n📋 Creating attendance reasons...");
        
        $attendanceReasons = [
            // Half Day Reasons
            ['category' => 'half_day', 'reason' => 'Medical Appointment', 'description' => 'Doctor ya medical appointment', 'is_active' => true, 'sort_order' => 1],
            ['category' => 'half_day', 'reason' => 'Family Emergency', 'description' => 'Ghar par emergency', 'is_active' => true, 'sort_order' => 2],
            ['category' => 'half_day', 'reason' => 'Personal Work', 'description' => 'Personal kaam', 'is_active' => true, 'sort_order' => 3],
            ['category' => 'half_day', 'reason' => 'Sick', 'description' => 'Tabiyat kharab', 'is_active' => true, 'sort_order' => 4],
            ['category' => 'half_day', 'reason' => 'Exam/Test', 'description' => 'Exam ya test dena', 'is_active' => true, 'sort_order' => 5],
            ['category' => 'half_day', 'reason' => 'Religious Event', 'description' => 'Mazhabi taqreeb', 'is_active' => true, 'sort_order' => 6],
            ['category' => 'half_day', 'reason' => 'Sports/Activity', 'description' => 'Sports tournament', 'is_active' => true, 'sort_order' => 7],
            ['category' => 'half_day', 'reason' => 'School Event', 'description' => 'School event mein participation', 'is_active' => true, 'sort_order' => 8],
            ['category' => 'half_day', 'reason' => 'Travel', 'description' => 'Safar', 'is_active' => true, 'sort_order' => 9],
            ['category' => 'half_day', 'reason' => 'Other', 'description' => 'Digar', 'is_active' => true, 'sort_order' => 10],
            
            // Late Reasons
            ['category' => 'late', 'reason' => 'Traffic', 'description' => 'Traffic jam', 'is_active' => true, 'sort_order' => 1],
            ['category' => 'late', 'reason' => 'Transportation Delay', 'description' => 'Bus/van late ya available nahi', 'is_active' => true, 'sort_order' => 2],
            ['category' => 'late', 'reason' => 'Slept Late', 'description' => 'Der se uthna', 'is_active' => true, 'sort_order' => 3],
            ['category' => 'late', 'reason' => 'Medical Emergency', 'description' => 'Health issue', 'is_active' => true, 'sort_order' => 4],
            ['category' => 'late', 'reason' => 'Family Emergency', 'description' => 'Ghar par emergency', 'is_active' => true, 'sort_order' => 5],
            ['category' => 'late', 'reason' => 'Weather', 'description' => 'Kharab mausam', 'is_active' => true, 'sort_order' => 6],
            ['category' => 'late', 'reason' => 'Vehicle Issue', 'description' => 'Car/bike kharab', 'is_active' => true, 'sort_order' => 7],
            ['category' => 'late', 'reason' => 'Heavy Rain', 'description' => 'Barish ki waja se', 'is_active' => true, 'sort_order' => 8],
            ['category' => 'late', 'reason' => 'Public Transport Strike', 'description' => 'Transport strike', 'is_active' => true, 'sort_order' => 9],
            ['category' => 'late', 'reason' => 'Other', 'description' => 'Digar', 'is_active' => true, 'sort_order' => 10],
            
            // Absent Reasons
            ['category' => 'absent', 'reason' => 'Sick', 'description' => 'Bemar', 'is_active' => true, 'sort_order' => 1],
            ['category' => 'absent', 'reason' => 'Family Emergency', 'description' => 'Ghar par emergency', 'is_active' => true, 'sort_order' => 2],
            ['category' => 'absent', 'reason' => 'Travel', 'description' => 'Shehar se bahar', 'is_active' => true, 'sort_order' => 3],
            ['category' => 'absent', 'reason' => 'Unwell', 'description' => 'Tabiyat theek nahi', 'is_active' => true, 'sort_order' => 4],
            ['category' => 'absent', 'reason' => 'Wedding', 'description' => 'Shaadi ki taqreeb', 'is_active' => true, 'sort_order' => 5],
            ['category' => 'absent', 'reason' => 'Death in Family', 'description' => 'Ghar mein intiqal', 'is_active' => true, 'sort_order' => 6],
        ];
        
        foreach ($attendanceReasons as $data) {
            DB::table('attendance_reasons')->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        }
        $this->command->info("✅ " . count($attendanceReasons) . " attendance reasons created");

        // ==============================================
        // 2. BLOCKS AND FLOORS
        // ==============================================
        $this->command->info("\n🏗️ Creating blocks and floors...");
        
        $blocks = [
            ['name' => 'Main Building', 'code' => 'MAIN'],
            ['name' => 'Primary Block', 'code' => 'PRI'],
            ['name' => 'Secondary Block', 'code' => 'SEC'],
            ['name' => 'Science Wing', 'code' => 'SCI'],
            ['name' => 'Admin Block', 'code' => 'ADM'],
        ];
        
        foreach ($blocks as $data) {
            DB::table('blocks')->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        }
        $blockMap = DB::table('blocks')->pluck('id', 'code')->toArray();
        
        $floors = [
            ['name' => 'Ground Floor', 'code' => 'G', 'level' => 0, 'block_id' => $blockMap['MAIN']],
            ['name' => 'First Floor', 'code' => '1', 'level' => 1, 'block_id' => $blockMap['MAIN']],
            ['name' => 'Second Floor', 'code' => '2', 'level' => 2, 'block_id' => $blockMap['MAIN']],
            ['name' => 'Primary Ground', 'code' => 'PG', 'level' => 0, 'block_id' => $blockMap['PRI']],
            ['name' => 'Primary First', 'code' => 'PF', 'level' => 1, 'block_id' => $blockMap['PRI']],
            ['name' => 'Secondary Ground', 'code' => 'SG', 'level' => 0, 'block_id' => $blockMap['SEC']],
            ['name' => 'Secondary First', 'code' => 'SF', 'level' => 1, 'block_id' => $blockMap['SEC']],
            ['name' => 'Science Ground', 'code' => 'SCG', 'level' => 0, 'block_id' => $blockMap['SCI']],
            ['name' => 'Science First', 'code' => 'SCF', 'level' => 1, 'block_id' => $blockMap['SCI']],
            ['name' => 'Admin Ground', 'code' => 'AG', 'level' => 0, 'block_id' => $blockMap['ADM']],
        ];
        
        foreach ($floors as $data) {
            DB::table('floors')->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        }
        $floorMap = DB::table('floors')->pluck('id', 'code')->toArray();
        $this->command->info("✅ " . count($blocks) . " blocks, " . count($floors) . " floors");

        // ==============================================
        // 3. ROOMS (31 rooms for the school)
        // ==============================================
        $this->command->info("\n🏠 Creating rooms...");
        
        $roomsData = [
            // Main Building - Ground Floor
            ['name' => 'Room 101', 'room_number' => '101', 'type' => 'classroom', 'capacity' => 40, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['G']],
            ['name' => 'Room 102', 'room_number' => '102', 'type' => 'classroom', 'capacity' => 40, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['G']],
            ['name' => 'Room 103', 'room_number' => '103', 'type' => 'classroom', 'capacity' => 35, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['G']],
            ['name' => 'Room 104', 'room_number' => '104', 'type' => 'classroom', 'capacity' => 45, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['G']],
            
            // Main Building - First Floor
            ['name' => 'Room 201', 'room_number' => '201', 'type' => 'classroom', 'capacity' => 40, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['1']],
            ['name' => 'Room 202', 'room_number' => '202', 'type' => 'classroom', 'capacity' => 40, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['1']],
            ['name' => 'Room 203', 'room_number' => '203', 'type' => 'classroom', 'capacity' => 35, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['1']],
            ['name' => 'Room 204', 'room_number' => '204', 'type' => 'classroom', 'capacity' => 45, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['1']],
            
            // Primary Block
            ['name' => 'KG Room A', 'room_number' => 'KG-A', 'type' => 'classroom', 'capacity' => 30, 'block_id' => $blockMap['PRI'], 'floor_id' => $floorMap['PG']],
            ['name' => 'KG Room B', 'room_number' => 'KG-B', 'type' => 'classroom', 'capacity' => 30, 'block_id' => $blockMap['PRI'], 'floor_id' => $floorMap['PG']],
            ['name' => 'Class 1 Room', 'room_number' => 'C1', 'type' => 'classroom', 'capacity' => 30, 'block_id' => $blockMap['PRI'], 'floor_id' => $floorMap['PG']],
            ['name' => 'Class 2 Room', 'room_number' => 'C2', 'type' => 'classroom', 'capacity' => 30, 'block_id' => $blockMap['PRI'], 'floor_id' => $floorMap['PF']],
            ['name' => 'Class 3 Room', 'room_number' => 'C3', 'type' => 'classroom', 'capacity' => 30, 'block_id' => $blockMap['PRI'], 'floor_id' => $floorMap['PF']],
            ['name' => 'Class 4 Room', 'room_number' => 'C4', 'type' => 'classroom', 'capacity' => 30, 'block_id' => $blockMap['PRI'], 'floor_id' => $floorMap['PF']],
            ['name' => 'Class 5 Room', 'room_number' => 'C5', 'type' => 'classroom', 'capacity' => 35, 'block_id' => $blockMap['PRI'], 'floor_id' => $floorMap['PF']],
            
            // Secondary Block
            ['name' => 'Class 6 Room', 'room_number' => 'C6', 'type' => 'classroom', 'capacity' => 40, 'block_id' => $blockMap['SEC'], 'floor_id' => $floorMap['SG']],
            ['name' => 'Class 7 Room', 'room_number' => 'C7', 'type' => 'classroom', 'capacity' => 40, 'block_id' => $blockMap['SEC'], 'floor_id' => $floorMap['SG']],
            ['name' => 'Class 8 Room', 'room_number' => 'C8', 'type' => 'classroom', 'capacity' => 40, 'block_id' => $blockMap['SEC'], 'floor_id' => $floorMap['SF']],
            ['name' => 'Class 9 Room', 'room_number' => 'C9', 'type' => 'classroom', 'capacity' => 45, 'block_id' => $blockMap['SEC'], 'floor_id' => $floorMap['SF']],
            ['name' => 'Class 10 Room', 'room_number' => 'C10', 'type' => 'classroom', 'capacity' => 45, 'block_id' => $blockMap['SEC'], 'floor_id' => $floorMap['SF']],
            
            // Science Wing - Labs
            ['name' => 'Physics Lab', 'room_number' => 'PHY-LAB', 'type' => 'lab', 'capacity' => 30, 'block_id' => $blockMap['SCI'], 'floor_id' => $floorMap['SCG']],
            ['name' => 'Chemistry Lab', 'room_number' => 'CHEM-LAB', 'type' => 'lab', 'capacity' => 30, 'block_id' => $blockMap['SCI'], 'floor_id' => $floorMap['SCG']],
            ['name' => 'Biology Lab', 'room_number' => 'BIO-LAB', 'type' => 'lab', 'capacity' => 30, 'block_id' => $blockMap['SCI'], 'floor_id' => $floorMap['SCF']],
            ['name' => 'Computer Lab 1', 'room_number' => 'COMP-LAB1', 'type' => 'lab', 'capacity' => 30, 'block_id' => $blockMap['SCI'], 'floor_id' => $floorMap['SCF']],
            ['name' => 'Computer Lab 2', 'room_number' => 'COMP-LAB2', 'type' => 'lab', 'capacity' => 25, 'block_id' => $blockMap['SCI'], 'floor_id' => $floorMap['SCF']],
            
            // Special Rooms
            ['name' => 'Library', 'room_number' => 'LIB', 'type' => 'library', 'capacity' => 100, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['2']],
            ['name' => 'Auditorium', 'room_number' => 'AUD', 'type' => 'auditorium', 'capacity' => 300, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['2']],
            ['name' => 'Art Room', 'room_number' => 'ART', 'type' => 'activity', 'capacity' => 35, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['2']],
            
            // Admin
            ['name' => 'Staff Room', 'room_number' => 'STAFF', 'type' => 'other', 'capacity' => 50, 'block_id' => $blockMap['ADM'], 'floor_id' => $floorMap['AG']],
            ['name' => 'Principal Office', 'room_number' => 'POFF', 'type' => 'other', 'capacity' => 15, 'block_id' => $blockMap['ADM'], 'floor_id' => $floorMap['AG']],
            ['name' => 'Admin Office', 'room_number' => 'ADMIN', 'type' => 'other', 'capacity' => 25, 'block_id' => $blockMap['ADM'], 'floor_id' => $floorMap['AG']],
        ];
        
        foreach ($roomsData as $data) {
            DB::table('rooms')->insert(array_merge($data, [
                'is_available' => true, 
                'has_ac' => true, 
                'has_projector' => in_array($data['type'], ['lab', 'classroom']),
                'created_at' => now(), 
                'updated_at' => now()
            ]));
        }
        $this->command->info("✅ " . count($roomsData) . " rooms created");

        // ==============================================
        // 4. ACADEMIC SESSION
        // ==============================================
        $this->command->info("\n📅 Creating academic session...");
        
        DB::table('academic_sessions')->insert([
            'name' => '2024-2025',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
            'is_active' => true,
            'description' => 'Current Academic Session 2024-2025',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $sessionId = DB::getPdo()->lastInsertId();
        $this->command->info("✅ Academic Session 2024-2025 created");

        // ==============================================
        // 5. GRADES (KG to 10th)
        // ==============================================
        $this->command->info("\n📚 Creating grades (KG to 10th)...");
        
        $grades = [
            ['name' => 'KG', 'numeric_value' => 0],
            ['name' => '1', 'numeric_value' => 1],
            ['name' => '2', 'numeric_value' => 2],
            ['name' => '3', 'numeric_value' => 3],
            ['name' => '4', 'numeric_value' => 4],
            ['name' => '5', 'numeric_value' => 5],
            ['name' => '6', 'numeric_value' => 6],
            ['name' => '7', 'numeric_value' => 7],
            ['name' => '8', 'numeric_value' => 8],
            ['name' => '9', 'numeric_value' => 9],
            ['name' => '10', 'numeric_value' => 10],
        ];
        DB::table('grades')->insert($grades);
        $gradesDb = DB::table('grades')->get();
        $gradeMap = $gradesDb->keyBy('name');
        $this->command->info("✅ " . count($grades) . " grades created");

        // ==============================================
        // 6. STREAMS
        // ==============================================
        $this->command->info("\n📊 Creating streams...");
        
        $streams = [
            ['name' => 'General', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Arts', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('streams')->insert($streams);
        $streamMap = DB::table('streams')->pluck('id', 'name')->toArray();
        $this->command->info("✅ Streams created");

        // ==============================================
        // 7. CLASSES (KG-8 General, 9-10 Science & Arts)
        // ==============================================
        $this->command->info("\n🏫 Creating classes...");
        
        $classesData = [];
        // KG to 8 - General stream
        foreach (['KG', '1', '2', '3', '4', '5', '6', '7', '8'] as $name) {
            $classesData[] = [
                'academic_session_id' => $sessionId,
                'grade_id' => $gradeMap[$name]->id,
                'stream_id' => $streamMap['General'],
                'elective_track_id' => null,
                'capacity' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        // 9th Grade - Science & Arts
        $classesData[] = [
            'academic_session_id' => $sessionId,
            'grade_id' => $gradeMap['9']->id,
            'stream_id' => $streamMap['Science'],
            'elective_track_id' => null,
            'capacity' => 40,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $classesData[] = [
            'academic_session_id' => $sessionId,
            'grade_id' => $gradeMap['9']->id,
            'stream_id' => $streamMap['Arts'],
            'elective_track_id' => null,
            'capacity' => 35,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        // 10th Grade - Science & Arts
        $classesData[] = [
            'academic_session_id' => $sessionId,
            'grade_id' => $gradeMap['10']->id,
            'stream_id' => $streamMap['Science'],
            'elective_track_id' => null,
            'capacity' => 45,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $classesData[] = [
            'academic_session_id' => $sessionId,
            'grade_id' => $gradeMap['10']->id,
            'stream_id' => $streamMap['Arts'],
            'elective_track_id' => null,
            'capacity' => 40,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('classes')->insert($classesData);
        $classesDb = DB::table('classes')->where('academic_session_id', $sessionId)->get();
        $this->command->info("✅ " . count($classesDb) . " classes created");

        // ==============================================
        // 8. CLASS SECTIONS (2 per class = 26 sections)
        // ==============================================
        $this->command->info("\n📋 Creating class sections (2 per class)...");
        
        $classSectionsData = [];
        foreach ($classesDb as $class) {
            $gradeName = $gradesDb->where('id', $class->grade_id)->first()->name;
            $streamName = DB::table('streams')->where('id', $class->stream_id)->value('name');
            
            foreach (['A', 'B'] as $section) {
                $sectionName = $gradeName . '-' . $section;
                if ($gradeName == '9' || $gradeName == '10') {
                    $streamShort = $streamName == 'Science' ? 'Sc' : 'Ar';
                    $sectionName = $gradeName . $streamShort . '-' . $section;
                }
                $classSectionsData[] = [
                    'class_id' => $class->id,
                    'section_name' => $sectionName,
                    'capacity' => 30,
                    'student_count' => 12,
                    'student_strength' => 12,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('class_sections')->insert($classSectionsData);
        $classSectionsDb = DB::table('class_sections')->get();
        $this->command->info("✅ " . count($classSectionsDb) . " class sections (2 each)");

        // ==============================================
        // 9. SUBJECTS (Pakistani Curriculum)
        // ==============================================
        $this->command->info("\n📖 Creating subjects...");
        
        $subjects = [
            // Core Subjects (All Grades)
            ['name' => 'English', 'code' => 'ENG', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Urdu', 'code' => 'URD', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Mathematics', 'code' => 'MATH', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Islamiyat', 'code' => 'ISL', 'type' => 'theory', 'is_active' => true],
            
            // Primary Subjects
            ['name' => 'Science', 'code' => 'SCI', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Social Studies', 'code' => 'SST', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Computer', 'code' => 'COMP', 'type' => 'practical', 'is_active' => true],
            ['name' => 'General Knowledge', 'code' => 'GK', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Drawing/Art', 'code' => 'ART', 'type' => 'practical', 'is_active' => true],
            ['name' => 'Nazra Quran', 'code' => 'NAZRA', 'type' => 'theory', 'is_active' => true],
            
            // Middle School Subjects
            ['name' => 'History', 'code' => 'HIST', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Geography', 'code' => 'GEO', 'type' => 'theory', 'is_active' => true],
            
            // 9th & 10th Common
            ['name' => 'Pakistan Studies', 'code' => 'PST', 'type' => 'theory', 'is_active' => true],
            
            // Science Stream Subjects
            ['name' => 'Physics', 'code' => 'PHY', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Biology', 'code' => 'BIO', 'type' => 'theory', 'is_active' => true],
            
            // Arts Stream Subjects
            ['name' => 'General Science', 'code' => 'GSC', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Economics', 'code' => 'ECO', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Civics', 'code' => 'CIV', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Home Economics', 'code' => 'HECO', 'type' => 'theory', 'is_active' => true],
            ['name' => 'Arabic', 'code' => 'ARB', 'type' => 'theory', 'is_active' => true],
        ];
        
        DB::table('subjects')->insert($subjects);
        $subjectsDb = DB::table('subjects')->get();
        $subjectsByName = $subjectsDb->keyBy('name');
        $this->command->info("✅ " . count($subjectsDb) . " subjects created");

        // ==============================================
        // 10. SUBJECT ASSIGNMENTS (Per class section)
        // ==============================================
        $this->command->info("\n📋 Assigning subjects to class sections...");
        
        // Grade level subject mapping
        $gradeSubjects = [
            'KG' => ['English', 'Urdu', 'Mathematics', 'General Knowledge', 'Drawing/Art', 'Islamiyat'],
            '1' => ['English', 'Urdu', 'Mathematics', 'General Knowledge', 'Islamiyat', 'Computer'],
            '2' => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies'],
            '3' => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies'],
            '4' => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies'],
            '5' => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies', 'Nazra Quran'],
            '6' => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies', 'History', 'Geography', 'Nazra Quran'],
            '7' => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies', 'History', 'Geography', 'Nazra Quran'],
            '8' => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies', 'History', 'Geography', 'Nazra Quran'],
        ];
        
        // 9th & 10th subjects by stream
        $scienceSubjects9th10th = ['English', 'Urdu', 'Mathematics', 'Islamiyat', 'Pakistan Studies', 'Physics', 'Chemistry', 'Biology', 'Computer'];
        $artsSubjects9th10th = ['English', 'Urdu', 'Mathematics', 'Islamiyat', 'Pakistan Studies', 'General Science', 'Economics', 'Civics', 'Home Economics', 'Arabic'];
        
        $subjectAssignmentData = [];
        foreach ($classSectionsDb as $section) {
            $classInfo = $classesDb->where('id', $section->class_id)->first();
            $gradeInfo = $gradesDb->where('id', $classInfo->grade_id)->first();
            $streamInfo = DB::table('streams')->where('id', $classInfo->stream_id)->value('name');
            $gradeName = $gradeInfo->name;
            
            $subjectsForSection = [];
            if (isset($gradeSubjects[$gradeName])) {
                $subjectsForSection = $gradeSubjects[$gradeName];
            } elseif ($gradeName == '9' || $gradeName == '10') {
                $subjectsForSection = $streamInfo == 'Science' ? $scienceSubjects9th10th : $artsSubjects9th10th;
            }
            
            foreach ($subjectsForSection as $subjectName) {
                if (isset($subjectsByName[$subjectName])) {
                    // Higher weekly frequency for science subjects
                    $weeklyFreq = 5;
                    if (in_array($subjectName, ['Computer', 'Drawing/Art', 'Nazra Quran', 'Arabic', 'Home Economics'])) {
                        $weeklyFreq = 3;
                    }
                    if (in_array($subjectName, ['Physics', 'Chemistry', 'Biology'])) {
                        $weeklyFreq = 6;
                    }
                    
                    $subjectAssignmentData[] = [
                        'class_section_id' => $section->id,
                        'subject_id' => $subjectsByName[$subjectName]->id,
                        'weekly_frequency' => $weeklyFreq,
                        'is_elective' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        DB::table('subject_assignments')->insert($subjectAssignmentData);
        $this->command->info("✅ " . count($subjectAssignmentData) . " subject assignments created");

        // ==============================================
        // 11. EMPLOYEE CATEGORIES & STAFF (21 Teachers + 2 Admin)
        // ==============================================
        $this->command->info("\n👥 Creating employee categories and staff...");
        
        DB::table('employee_categories')->insert([
            ['name' => 'Teaching Staff', 'code' => 'TCH', 'description' => 'Teachers', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin Staff', 'code' => 'ADM', 'description' => 'Administration', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Teacher specializations matching subjects
        $teacherSpecializations = [
            'English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat',
            'Social Studies', 'Computer', 'General Knowledge', 'Drawing/Art', 'Nazra Quran',
            'History', 'Geography', 'Pakistan Studies', 'Physics', 'Chemistry',
            'Biology', 'General Science', 'Economics', 'Civics', 'Home Economics',
            'Arabic'
        ];
        
        $teacherNames = [
            ['first' => 'Muhammad', 'last' => 'Asif', 'gender' => 'Male'],
            ['first' => 'Saima', 'last' => 'Akram', 'gender' => 'Female'],
            ['first' => 'Imran', 'last' => 'Khan', 'gender' => 'Male'],
            ['first' => 'Farah', 'last' => 'Naz', 'gender' => 'Female'],
            ['first' => 'Ahmed', 'last' => 'Raza', 'gender' => 'Male'],
            ['first' => 'Nadia', 'last' => 'Hassan', 'gender' => 'Female'],
            ['first' => 'Khalid', 'last' => 'Mehmood', 'gender' => 'Male'],
            ['first' => 'Rabia', 'last' => 'Basri', 'gender' => 'Female'],
            ['first' => 'Usman', 'last' => 'Ghani', 'gender' => 'Male'],
            ['first' => 'Sadia', 'last' => 'Javed', 'gender' => 'Female'],
            ['first' => 'Shahid', 'last' => 'Afridi', 'gender' => 'Male'],
            ['first' => 'Ayesha', 'last' => 'Siddiqui', 'gender' => 'Female'],
            ['first' => 'Zahid', 'last' => 'Hameed', 'gender' => 'Male'],
            ['first' => 'Tahira', 'last' => 'Yasmin', 'gender' => 'Female'],
            ['first' => 'Rashid', 'last' => 'Minhas', 'gender' => 'Male'],
            ['first' => 'Samina', 'last' => 'Rafiq', 'gender' => 'Female'],
            ['first' => 'Tariq', 'last' => 'Aziz', 'gender' => 'Male'],
            ['first' => 'Noreen', 'last' => 'Bano', 'gender' => 'Female'],
            ['first' => 'Faisal', 'last' => 'Qureshi', 'gender' => 'Male'],
            ['first' => 'Humaira', 'last' => 'Khalid', 'gender' => 'Female'],
            ['first' => 'Javed', 'last' => 'Iqbal', 'gender' => 'Male'],
        ];
        
        $qualificationsList = ['B.Ed', 'M.Ed', 'M.A. English', 'M.Sc. Mathematics', 'M.Sc. Physics', 'M.Sc. Chemistry', 'B.Sc.', 'M.A. Urdu', 'M.A. Islamiyat'];
        
        $staffData = [];
        for ($i = 0; $i < 21; $i++) {
            $basicSalary = $this->faker->numberBetween(35000, 65000);
            
            $staffData[] = [
                'first_name' => $teacherNames[$i]['first'],
                'last_name' => $teacherNames[$i]['last'],
                'father_name' => $this->fatherNames[array_rand($this->fatherNames)],
                'gender' => $teacherNames[$i]['gender'],
                'date_of_birth' => $this->faker->dateTimeBetween('-45 years', '-25 years')->format('Y-m-d'),
                'cnic' => $this->faker->numerify('35202-#######-#'),
                'nationality' => 'Pakistani',
                'religion' => 'Islam',
                'email' => strtolower($teacherNames[$i]['first'] . '.' . $teacherNames[$i]['last'] . '@school.edu.pk'),
                'phone' => '03' . $this->faker->numerify('##-#######'),
                'mobile' => '03' . $this->faker->numerify('##-#######'),
                'emergency_contact_name' => $this->faker->name,
                'emergency_contact_relation' => 'Spouse',
                'emergency_contact_phone' => '03' . $this->faker->numerify('##-#######'),
                'present_address' => $this->faker->streetAddress . ', ' . $this->cities[array_rand($this->cities)],
                'permanent_address' => $this->faker->streetAddress . ', ' . $this->cities[array_rand($this->cities)],
                'city' => $this->cities[array_rand($this->cities)],
                'state' => 'Punjab',
                'postal_code' => $this->faker->numerify('#####'),
                'country' => 'Pakistan',
                'employee_id' => 'EMP-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'designation' => in_array($teacherSpecializations[$i], ['Physics', 'Chemistry', 'Biology', 'Mathematics']) ? 'Senior Teacher' : 'Subject Teacher',
                'department' => 'Academics',
                'employment_type' => 'full_time',
                'joining_date' => $this->faker->dateTimeBetween('-8 years', '-1 year')->format('Y-m-d'),
                'qualification' => $qualificationsList[array_rand($qualificationsList)],
                'experience' => $this->faker->numberBetween(2, 20) . ' years teaching experience',
                'specialization' => $teacherSpecializations[$i],
                'basic_salary' => $basicSalary,
                'salary_type' => 'monthly',
                'daily_rate' => round($basicSalary / 22, 2),
                'allowances' => $this->faker->numberBetween(3000, 10000),
                'medical_allowance' => 5000,
                'transport_allowance' => 3000,
                'pf_percentage' => 5,
                'attendance_required' => true,
                'bank_name' => 'Habib Bank Ltd',
                'bank_account_number' => $this->faker->numerify('##########'),
                'bank_iban' => 'PK36HABB' . $this->faker->numerify('############'),
                'category_id' => 1,
                'is_active' => true,
                'max_periods_per_day' => 7,
                'max_periods_per_week' => 35,
                'is_teacher' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
               // Principal
        $staffData[] = [
            'first_name' => 'Abdul', 
            'last_name' => 'Rehman', 
            'father_name' => 'Muhammad Latif', 
            'gender' => 'Male',
            'date_of_birth' => '1970-03-15', 
            'cnic' => '35202-1234567-1', 
            'nationality' => 'Pakistani', 
            'religion' => 'Islam',
            'email' => 'principal@school.edu.pk', 
            'phone' => '0300-1234567', 
            'mobile' => '0300-1234567',
            'emergency_contact_name' => 'Mrs. Rehman',
            'emergency_contact_relation' => 'Wife',
            'emergency_contact_phone' => '0300-1111111',
            'present_address' => '123 Main Road, Lahore', 
            'permanent_address' => '123 Main Road, Lahore',
            'city' => 'Lahore', 
            'state' => 'Punjab', 
            'postal_code' => '54000', 
            'country' => 'Pakistan',
            'employee_id' => 'EMP-022', 
            'designation' => 'Principal', 
            'department' => 'Administration',
            'employment_type' => 'full_time', 
            'joining_date' => '2010-04-01',
            'qualification' => 'M.Phil Education', 
            'experience' => '25 years in education',
            'specialization' => null, 
            'basic_salary' => 150000, 
            'salary_type' => 'monthly', 
            'daily_rate' => 0,
            'allowances' => 25000, 
            'medical_allowance' => 10000, 
            'transport_allowance' => 8000, 
            'pf_percentage' => 5,
            'attendance_required' => true, 
            'bank_name' => 'Habib Bank Ltd', 
            'bank_account_number' => '1234567890',
            'bank_iban' => 'PK36HABB1234567890123456',
            'category_id' => 2, 
            'is_active' => true, 
            'max_periods_per_day' => 0, 
            'max_periods_per_week' => 0, 
            'is_teacher' => false,
            'created_at' => now(), 
            'updated_at' => now(),
        ];
        
        // Admin Officer
        $staffData[] = [
            'first_name' => 'Nasreen', 
            'last_name' => 'Akhtar', 
            'father_name' => 'Ghulam Mustafa', 
            'gender' => 'Female',
            'date_of_birth' => '1975-08-20', 
            'cnic' => '35202-7654321-2', 
            'nationality' => 'Pakistani', 
            'religion' => 'Islam',
            'email' => 'admin@school.edu.pk', 
            'phone' => '0300-7654321', 
            'mobile' => '0300-7654321',
            'emergency_contact_name' => 'Mr. Akhtar',
            'emergency_contact_relation' => 'Husband',
            'emergency_contact_phone' => '0300-2222222',
            'present_address' => '456 Model Town, Lahore', 
            'permanent_address' => '456 Model Town, Lahore',
            'city' => 'Lahore', 
            'state' => 'Punjab', 
            'postal_code' => '54000', 
            'country' => 'Pakistan',
            'employee_id' => 'EMP-023', 
            'designation' => 'Admin Officer', 
            'department' => 'Administration',
            'employment_type' => 'full_time', 
            'joining_date' => '2015-04-01',
            'qualification' => 'MBA', 
            'experience' => '15 years in administration',
            'specialization' => null, 
            'basic_salary' => 65000, 
            'salary_type' => 'monthly', 
            'daily_rate' => 0,
            'allowances' => 10000, 
            'medical_allowance' => 5000, 
            'transport_allowance' => 4000, 
            'pf_percentage' => 5,
            'attendance_required' => true, 
            'bank_name' => 'Meezan Bank', 
            'bank_account_number' => '0987654321',
            'bank_iban' => 'PK36MEZN0987654321098765',
            'category_id' => 2, 
            'is_active' => true, 
            'max_periods_per_day' => 0, 
            'max_periods_per_week' => 0, 
            'is_teacher' => false,
            'created_at' => now(), 
            'updated_at' => now(),
        ];
       
        
        DB::table('staff')->insert($staffData);
        $staffDb = DB::table('staff')->get();
        $teacherIds = DB::table('staff')->where('is_teacher', true)->pluck('id')->toArray();
        $staffIds = DB::table('staff')->pluck('id')->toArray();
        $this->command->info("✅ " . count($staffData) . " staff created (" . count($teacherIds) . " teachers + 2 admin)");

        // ==============================================
        // 12. TEACHER-SUBJECT ASSIGNMENTS
        // ==============================================
        $this->command->info("\n📚 Assigning subjects to teachers...");
        
        $teacherSubjectData = [];
        foreach ($teacherIds as $index => $teacherId) {
            $specialization = $teacherSpecializations[$index];
            
            // Primary subject based on specialization
            if (isset($subjectsByName[$specialization])) {
                $teacherSubjectData[] = [
                    'teacher_id' => $teacherId,
                    'subject_id' => $subjectsByName[$specialization]->id,
                    'preference_level' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Add 1-2 additional subjects
            $otherSubjects = $subjectsDb->where('name', '!=', $specialization)->random(min(2, $subjectsDb->count() - 1));
            // Handle if random() returns single item instead of collection
            if ($otherSubjects instanceof \stdClass) {
                $otherSubjects = [$otherSubjects];
            }
            if (!is_array($otherSubjects) && !$otherSubjects instanceof \Illuminate\Support\Collection) {
                $otherSubjects = [$otherSubjects];
            }
            foreach ($otherSubjects as $subj) {
                $teacherSubjectData[] = [
                    'teacher_id' => $teacherId,
                    'subject_id' => $subj->id,
                    'preference_level' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        DB::table('teacher_subjects')->insert($teacherSubjectData);
        $this->command->info("✅ " . count($teacherSubjectData) . " teacher-subject assignments");

        // ==============================================
        // 13. STUDENTS (12 per section)
        // ==============================================
        $this->command->info("\n👨‍🎓 Creating students (12 per section)...");
        
        $studentData = [];
        $admissionNum = 1;
        
        // Age map for grade-based DOB calculation
        $ageMap = [
            'KG' => 5, '1' => 6, '2' => 7, '3' => 8, '4' => 9, '5' => 10,
            '6' => 11, '7' => 12, '8' => 13, '9' => 14, '10' => 15
        ];
        
        foreach ($classSectionsDb as $section) {
            for ($i = 1; $i <= 12; $i++) {
                $gender = $i <= 6 ? 'Male' : 'Female';
                
                if ($gender == 'Male') {
                    $firstName = $this->maleFirstNames[array_rand($this->maleFirstNames)];
                    $lastName = $this->maleLastNames[array_rand($this->maleLastNames)];
                } else {
                    $firstName = $this->femaleFirstNames[array_rand($this->femaleFirstNames)];
                    $lastName = $this->femaleLastNames[array_rand($this->femaleLastNames)];
                }
                
                $classInfo = $classesDb->where('id', $section->class_id)->first();
                $gradeInfo = $gradesDb->where('id', $classInfo->grade_id)->first();
                $gradeName = $gradeInfo->name;
                
                $age = $ageMap[$gradeName] ?? 8;
                $dob = date('Y-m-d', strtotime("-{$age} years " . rand(-6, 6) . " months"));
                
                $fatherName = $this->fatherNames[array_rand($this->fatherNames)];
                $city = $this->cities[array_rand($this->cities)];
                $fatherOccupation = $this->faker->randomElement([
                    'Businessman', 'Government Servant', 'Doctor', 'Engineer', 
                    'Teacher', 'Army Officer', 'Lawyer', 'Banker', 'Shopkeeper', 'Private Job'
                ]);
                
                $studentData[] = [
                    'first_name' => $firstName,
                    'middle_name' => null,
                    'last_name' => $lastName,
                    'gender' => $gender,
                    'date_of_birth' => $dob,
                    'dob_in_words' => null,
                    'religion' => 'Islam',
                    'caste_subcaste' => null,
                    'blood_group' => $this->faker->randomElement(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-']),
                    'address' => $this->faker->streetAddress . ', ' . $city,
                    'phone' => '03' . $this->faker->numerify('##-#######'),
                    'email' => strtolower($firstName . '.' . $lastName . $admissionNum . '@gmail.com'),
                    'city' => $city,
                    'state' => 'Punjab',
                    'country' => 'Pakistan',
                    'extra_note' => null,
                    'mother_tongue' => 'Urdu',
                    'birth_place' => $city,
                    'previous_school_name' => $gradeName == 'KG' ? null : $this->faker->randomElement([
                        'The Educators', 'Beaconhouse', 'Allied School', 'Dar-e-Arqam', 'LACAS', null
                    ]),
                    'previous_school_address' => null,
                    'previous_class' => null,
                    'passout_year' => null,
                    'previous_category' => null,
                    'admission_date' => '2024-04-01',
                    'student_type' => $gradeName == 'KG' ? 'New' : 'Old',
                    'admission_number' => 'ADM-' . str_pad($admissionNum, 4, '0', STR_PAD_LEFT),
                    'roll_number' => str_pad($admissionNum, 3, '0', STR_PAD_LEFT),
                    'profile_photo' => null,
                    'father_name' => $fatherName,
                    'father_phone' => '03' . $this->faker->numerify('##-#######'),
                    'father_occupation' => $fatherOccupation,
                    'mother_name' => $this->faker->firstName('female') . ' ' . $this->faker->lastName,
                    'mother_phone' => '03' . $this->faker->numerify('##-#######'),
                    'mother_occupation' => $this->faker->randomElement(['Housewife', 'Teacher', 'Doctor', 'Housewife', 'Housewife', 'Lecturer']),
                    'parent_id_proof' => null,
                    'parent_signature' => null,
                    'assigned_concession' => $this->faker->randomElement([null, null, null, null, 'Sibling Discount', 'Merit Scholarship']),
                    'status' => 'Active',
                    'suspension_start_date' => null,
                    'suspension_end_date' => null,
                    'suspension_message' => null,
                    'class_section_id' => $section->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $admissionNum++;
            }
        }
        
        DB::table('students')->insert($studentData);
        $studentsDb = DB::table('students')->get();
        $this->command->info("✅ " . count($studentData) . " students created (12 per section × 26 sections = 312)");

        // Update student counts in class_sections
        $counts = DB::table('students')
            ->select('class_section_id', DB::raw('count(*) as total'))
            ->groupBy('class_section_id')
            ->get();
        foreach ($counts as $count) {
            DB::table('class_sections')
                ->where('id', $count->class_section_id)
                ->update(['student_count' => $count->total, 'student_strength' => $count->total]);
        }

        // ==============================================
        // 14. BANKS & FINANCIAL SETUP
        // ==============================================
        $this->command->info("\n🏦 Creating financial setup...");
        
        $banks = [
            ['name' => 'Habib Bank Ltd', 'branch_name' => 'Main Branch', 'account_title' => 'School Fee Account', 'account_number' => '1234-567890-01', 'iban' => 'PK36HABB0012345678901234', 'routing_number' => '123456', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meezan Bank', 'branch_name' => 'Islamic Banking', 'account_title' => 'School Fee', 'account_number' => '5566-778899-03', 'iban' => 'PK36MEZN0012345678909012', 'routing_number' => '654321', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('banks')->insert($banks);
        
        $discounts = [
            ['name' => 'Sibling Discount 10%', 'type' => 'percentage', 'value' => 10, 'description' => 'For siblings studying in same school', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Merit Scholarship 25%', 'type' => 'percentage', 'value' => 25, 'description' => 'For top performing students', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Need Based 20%', 'type' => 'percentage', 'value' => 20, 'description' => 'Financial assistance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Orphan Support 50%', 'type' => 'percentage', 'value' => 50, 'description' => 'For orphan students', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('discounts')->insert($discounts);
        
        $feeTypes = [
            ['name' => 'Admission Fee', 'description' => 'One-time admission fee', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Tuition Fee', 'description' => 'Monthly tuition charges', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Exam Fee', 'description' => 'Per term exam fee', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lab Fee', 'description' => 'Science/Computer lab charges', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('fee_types')->insert($feeTypes);
        
        $feeSubmissionTypes = [
            ['name' => 'Admission Fee', 'period' => 'one_time', 'amount' => 8000, 'description' => 'One time at admission', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Fee (KG)', 'period' => 'monthly', 'amount' => 2500, 'description' => 'Monthly fee for KG', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Fee (Class 1-5)', 'period' => 'monthly', 'amount' => 3000, 'description' => 'Monthly fee for Class 1-5', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Fee (Class 6-8)', 'period' => 'monthly', 'amount' => 3500, 'description' => 'Monthly fee for Class 6-8', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Fee (Class 9-10)', 'period' => 'monthly', 'amount' => 4000, 'description' => 'Monthly fee for Class 9-10', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Exam Fee', 'period' => 'quarterly', 'amount' => 1500, 'description' => 'Per term examination fee', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science Lab Fee', 'period' => 'annually', 'amount' => 3000, 'description' => 'Annual lab fee for 9-10 Science', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('fee_submission_types')->insert($feeSubmissionTypes);
        
        $this->command->info("✅ Financial setup complete");

        // ==============================================
               // ==============================================
        // 15. STUDENT FEE INSTALLMENTS & INVOICES (FIXED)
        // ==============================================
        $this->command->info("\n💰 Creating fee installments and invoices...");
        
        $gradeFeeMap = [
            'KG' => 2, '1' => 3, '2' => 3, '3' => 3, '4' => 3, '5' => 3,
            '6' => 4, '7' => 4, '8' => 4, '9' => 5, '10' => 5
        ];
        
        $installmentData = [];
        $invoiceData = [];
        $installmentId = 1;
        
        foreach ($studentsDb as $student) {
            $classInfo = $classesDb->where('id', $student->class_section_id)->first();
            if (!$classInfo) continue;
            
            $gradeInfo = $gradesDb->where('id', $classInfo->grade_id)->first();
            if (!$gradeInfo) continue;
            
            $streamInfo = DB::table('streams')->where('id', $classInfo->stream_id)->value('name');
            $feeSubmissionTypeId = $gradeFeeMap[$gradeInfo->name] ?? 2;
            $feeAmount = DB::table('fee_submission_types')->where('id', $feeSubmissionTypeId)->value('amount');
            
            // Create 4 months of installments (Apr-Jul)
            for ($month = 1; $month <= 4; $month++) {
                $dueDate = date('Y-m-d', strtotime("2024-" . (3 + $month) . "-10"));
                $status = $month <= 3 ? 'paid' : 'pending';
                $paidAmount = $status == 'paid' ? $feeAmount : 0;
                $paymentDate = $status == 'paid' ? date('Y-m-d', strtotime($dueDate . ' -2 days')) : null;
                $paidAt = $status == 'paid' ? date('Y-m-d H:i:s', strtotime($dueDate . ' -2 days 10:30:00')) : null;
                
                $installmentData[] = [
                    'student_id' => $student->id,
                    'fee_submission_type_id' => $feeSubmissionTypeId,
                    'installment_number' => $month,
                    'amount' => $feeAmount,
                    'due_date' => $dueDate,
                    'status' => $status,
                    'paid_amount' => $paidAmount,
                    'payment_date' => $paymentDate,
                    'receipt_number' => $status == 'paid' ? 'RCP-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT) : null,
                    'remarks' => 'Monthly Fee - ' . date('F Y', strtotime($dueDate)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // ALL invoices must have exactly the same structure
                $invoiceData[] = [
                    'invoice_number' => 'INV-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT),
                    'student_id' => $student->id,
                    'bank_id' => rand(1, 2),
                    'student_fee_submission_id' => $installmentId,
                    'amount' => $feeAmount,
                    'discount_amount' => 0,
                    'due_date' => $dueDate,
                    'status' => $status == 'paid' ? 'paid' : 'pending',
                    'challan_file' => null,
                    'payment_proof_file' => null,
                    'payment_remarks' => null,
                    'paid_at' => $paidAt,
                    'approved_by' => null,
                    'discount_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $installmentId++;
            }
            
            // Add science lab fee for 9th-10th science students
            if (($gradeInfo->name == '9' || $gradeInfo->name == '10') && $streamInfo == 'Science') {
                $installmentData[] = [
                    'student_id' => $student->id,
                    'fee_submission_type_id' => 7, // Science Lab Fee
                    'installment_number' => 1,
                    'amount' => 3000,
                    'due_date' => '2024-04-15',
                    'status' => 'paid',
                    'paid_amount' => 3000,
                    'payment_date' => '2024-04-13',
                    'receipt_number' => 'RCP-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT),
                    'remarks' => 'Science Lab Fee - Annual',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // SAME structure as other invoices - all fields must be present
                $invoiceData[] = [
                    'invoice_number' => 'INV-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT),
                    'student_id' => $student->id,
                    'bank_id' => 1,
                   'student_fee_submission_id' => $installmentId,
                    'amount' => 3000,
                    'discount_amount' => 0,
                    'due_date' => '2024-04-15',
                    'status' => 'paid',
                    'challan_file' => null,
                    'payment_proof_file' => null,
                    'payment_remarks' => null,
                    'paid_at' => '2024-04-13 10:30:00',
                    'approved_by' => null,
                    'discount_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $installmentId++;
            }
        }
        
        foreach (array_chunk($installmentData, 500) as $chunk) {
            DB::table('student_fee_submissions')->insert($chunk);
        }
        foreach (array_chunk($invoiceData, 500) as $chunk) {
            DB::table('invoices')->insert($chunk);
        }
        $this->command->info("✅ " . count($installmentData) . " fee installments and " . count($invoiceData) . " invoices created");
        // ==============================================
        // 16. SCHOOL TIMING & TIME SLOTS
        // ==============================================
        $this->command->info("\n⏰ Creating school timings and time slots...");
        
        DB::table('school_timings')->insert([
            'session_name' => 'Regular 2024-2025',
            'session_start' => '2024-04-01',
            'session_end' => '2025-03-31',
            'school_start' => '08:00:00',
            'school_end' => '14:30:00',
            'period_duration' => 45,
            'breaks' => json_encode([
                ['label' => 'Short Break', 'start' => '09:40', 'end' => '09:55'],
                ['label' => 'Lunch Break', 'start' => '11:25', 'end' => '11:55'],
            ]),
            'has_activity' => true,
            'activity_label' => 'Assembly/Dua',
            'activity_start' => '08:00:00',
            'activity_end' => '08:10:00',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $schoolTimingId = DB::getPdo()->lastInsertId();
        
        $timeSlotsList = [
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 1, 'label' => 'Assembly', 'start_time' => '08:00', 'end_time' => '08:10', 'type' => 'activity', 'period_number' => null, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 2, 'label' => 'Period 1', 'start_time' => '08:10', 'end_time' => '08:55', 'type' => 'period', 'period_number' => 1, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 3, 'label' => 'Period 2', 'start_time' => '08:55', 'end_time' => '09:40', 'type' => 'period', 'period_number' => 2, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 4, 'label' => 'Short Break', 'start_time' => '09:40', 'end_time' => '09:55', 'type' => 'break', 'period_number' => null, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 5, 'label' => 'Period 3', 'start_time' => '09:55', 'end_time' => '10:40', 'type' => 'period', 'period_number' => 3, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 6, 'label' => 'Period 4', 'start_time' => '10:40', 'end_time' => '11:25', 'type' => 'period', 'period_number' => 4, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 7, 'label' => 'Lunch Break', 'start_time' => '11:25', 'end_time' => '11:55', 'type' => 'break', 'period_number' => null, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 8, 'label' => 'Period 5', 'start_time' => '11:55', 'end_time' => '12:40', 'type' => 'period', 'period_number' => 5, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 9, 'label' => 'Period 6', 'start_time' => '12:40', 'end_time' => '13:25', 'type' => 'period', 'period_number' => 6, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 10, 'label' => 'Period 7', 'start_time' => '13:25', 'end_time' => '14:10', 'type' => 'period', 'period_number' => 7, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 11, 'label' => 'Period 8', 'start_time' => '14:10', 'end_time' => '14:30', 'type' => 'period', 'period_number' => 8, 'is_active' => true],
        ];
        
        foreach ($timeSlotsList as $data) {
            DB::table('time_slots')->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        }
        $timeSlotsDb = DB::table('time_slots')->where('type', 'period')->get();
        $this->command->info("✅ School timing and " . count($timeSlotsList) . " time slots created");

        // ==============================================
        // 17. TEACHER AVAILABILITIES
        // ==============================================
        $this->command->info("\n📅 Creating teacher availabilities...");
        
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $availabilityData = [];
        
        foreach ($teacherIds as $teacherId) {
            foreach ($days as $day) {
                foreach ($timeSlotsDb as $slot) {
                    $availabilityData[] = [
                        'teacher_id' => $teacherId,
                        'day_of_week' => $day,
                        'time_slot_id' => $slot->id,
                        'is_available' => rand(1, 100) <= 90,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        
        foreach (array_chunk($availabilityData, 500) as $chunk) {
            DB::table('teacher_availabilities')->insert($chunk);
        }
        $this->command->info("✅ " . count($availabilityData) . " teacher availability records");

        // ==============================================
        // 18. TIMETABLE ENTRIES (FIXED - No duplicates)
        // ==============================================
        $this->command->info("\n📅 Creating timetable entries...");
        
        $rooms = DB::table('rooms')->where('type', 'classroom')->get();
        $timetableData = [];
        $usedTeacherSlots = []; // Track teacher+day+slot to avoid duplicates
        
        foreach ($classSectionsDb as $section) {
            $sectionSubjects = DB::table('subject_assignments')
                ->where('class_section_id', $section->id)
                ->get();
            
            $room = $rooms->random();
            
            foreach ($days as $day) {
                $periodSlots = $timeSlotsDb->sortBy('sort_order');
                $subjectIndex = 0;
                
                foreach ($periodSlots as $slot) {
                    if ($subjectIndex >= $sectionSubjects->count()) break;
                    
                    $assignment = $sectionSubjects[$subjectIndex];
                    
                    // Find a teacher for this subject who isn't already booked
                    $teacher = DB::table('teacher_subjects')
                        ->where('subject_id', $assignment->subject_id)
                        ->inRandomOrder()
                        ->first();
                    
                    if ($teacher) {
                        $key = $teacher->teacher_id . '-' . $day . '-' . $slot->id;
                        
                        // Only add if this teacher isn't already booked for this day+slot
                        if (!in_array($key, $usedTeacherSlots)) {
                            $usedTeacherSlots[] = $key;
                            
                            $timetableData[] = [
                                'class_section_id' => $section->id,
                                'subject_id' => $assignment->subject_id,
                                'teacher_id' => $teacher->teacher_id,
                                'room_id' => $room->id,
                                'time_slot_id' => $slot->id,
                                'day_of_week' => $day,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                    
                    $subjectIndex++;
                }
            }
        }
        
        // Use insertOrIgnore to safely skip any remaining duplicates
        foreach (array_chunk($timetableData, 100) as $chunk) {
            DB::table('timetable_entries')->insertOrIgnore($chunk);
        }
        
        $this->command->info("✅ " . count($timetableData) . " timetable entries created");

        // ==============================================
        // 19. SALARY TEMPLATES
        // ==============================================
        $this->command->info("\n💰 Creating salary templates...");
        
        $salaryTemplates = [
            ['name' => 'Teacher Basic', 'description' => 'Basic teacher salary', 'basic_salary' => 45000, 'allowances' => 8000, 'medical_allowance' => 5000, 'transport_allowance' => 3000, 'pf_percentage' => 5, 'tax_percentage' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senior Teacher', 'description' => 'Senior teacher salary', 'basic_salary' => 65000, 'allowances' => 12000, 'medical_allowance' => 7000, 'transport_allowance' => 5000, 'pf_percentage' => 5, 'tax_percentage' => 2.5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin Staff', 'description' => 'Admin staff salary', 'basic_salary' => 55000, 'allowances' => 10000, 'medical_allowance' => 6000, 'transport_allowance' => 4000, 'pf_percentage' => 5, 'tax_percentage' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('salary_templates')->insert($salaryTemplates);
        $this->command->info("✅ Salary templates created");

        // ==============================================
        // 20. SALARIES
        // ==============================================
        $this->command->info("\n💰 Creating salary records...");
        
        $months = ['2024-04', '2024-05', '2024-06', '2024-07'];
        $salaryData = [];
        
        foreach ($staffIds as $staffId) {
            $staff = DB::table('staff')->where('id', $staffId)->first();
            
            foreach ($months as $month) {
                $salaryData[] = [
                    'staff_id' => $staffId,
                    'month' => $month,
                    'total_working_days' => 22,
                    'days_present' => 21,
                    'days_absent' => 1,
                    'days_late' => 0,
                    'basic_salary' => $staff->basic_salary,
                    'daily_rate' => round($staff->basic_salary / 22, 2),
                    'attendance_bonus' => 500,
                    'overtime_pay' => 0,
                    'allowances' => $staff->allowances ?? 0,
                    'bonus' => 0,
                    'commission' => 0,
                    'deductions' => ($staff->basic_salary ?? 0) * 0.05,
                    'penalties' => 0,
                    'leave_deductions' => 0,
                    'tax' => ($staff->basic_salary ?? 0) > 50000 ? (($staff->basic_salary ?? 0) - 50000) * 0.025 : 0,
                    'pf_employee' => ($staff->basic_salary ?? 0) * 0.05,
                    'pf_employer' => ($staff->basic_salary ?? 0) * 0.075,
                    'net_salary' => ($staff->basic_salary ?? 0) + ($staff->allowances ?? 0) + 500 - (($staff->basic_salary ?? 0) * 0.05),
                    'payment_date' => $month . '-05',
                    'payment_method' => 'bank',
                    'payment_status' => 'paid',
                    'transaction_ref' => 'TXN-' . str_pad($staffId, 6, '0', STR_PAD_LEFT),
                    'bank_name' => null,
                    'account_number' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                    'remarks' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        foreach (array_chunk($salaryData, 100) as $chunk) {
            DB::table('salaries')->insert($chunk);
        }
        $this->command->info("✅ " . count($salaryData) . " salary records created");

        // ==============================================
        // 21. EXAMS (Mid Term + Final Term for all sections)
        // ==============================================
        $this->command->info("\n📝 Creating exams...");
        
        DB::table('exam_types')->insert([
            ['name' => 'Mid Term', 'code' => 'MID', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Final Term', 'code' => 'FINAL', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        DB::table('exam_groups')->insert([
            ['name' => 'Pre-Primary', 'code' => 'PRE', 'description' => 'KG', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Primary', 'code' => 'PRI', 'description' => 'Class 1-5', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Middle', 'code' => 'MID', 'description' => 'Class 6-8', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secondary Science', 'code' => 'SEC-SC', 'description' => '9-10 Science', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secondary Arts', 'code' => 'SEC-AR', 'description' => '9-10 Arts', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        $examTypeIds = DB::table('exam_types')->pluck('id')->toArray();
        $examData = [];
        
        foreach ($classSectionsDb as $section) {
            $classInfo = $classesDb->where('id', $section->class_id)->first();
            $gradeInfo = $gradesDb->where('id', $classInfo->grade_id)->first();
            $streamInfo = DB::table('streams')->where('id', $classInfo->stream_id)->value('name');
            $gradeName = $gradeInfo->name;
            
            // Determine exam group
            if ($gradeName == 'KG') $group = 1;
            elseif (in_array($gradeName, ['1', '2', '3', '4', '5'])) $group = 2;
            elseif (in_array($gradeName, ['6', '7', '8'])) $group = 3;
            elseif ($streamInfo == 'Science') $group = 4;
            else $group = 5;
            
            $examDates = [
                'Mid Term' => ['2024-09-15', '2024-09-25'],
                'Final Term' => ['2024-12-10', '2024-12-20'],
            ];
            
            foreach ($examDates as $name => $dateRange) {
                $examData[] = [
                    'exam_type_id' => $name == 'Mid Term' ? $examTypeIds[0] : $examTypeIds[1],
                    'exam_group_id' => $group,
                    'class_section_id' => $section->id,
                    'name' => $name . ' ' . date('Y'),
                    'start_date' => $dateRange[0],
                    'end_date' => $dateRange[1],
                    'description' => $name . ' Examination for ' . $section->section_name,
                    'is_published' => true,
                    'exam_center' => null,
                    'time_table' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('exams')->insert($examData);
        $examsDb = DB::table('exams')->get();
        $this->command->info("✅ " . count($examData) . " exams created");

        // ==============================================
        // 22. EXAM MARKS
        // ==============================================
        $this->command->info("\n📊 Creating exam marks...");
        
        $marksData = [];
        
        foreach ($examsDb as $exam) {
            $students = DB::table('students')->where('class_section_id', $exam->class_section_id)->get();
            $sectionSubjects = DB::table('subject_assignments')->where('class_section_id', $exam->class_section_id)->get();
            
            foreach ($students as $student) {
                foreach ($sectionSubjects as $assignment) {
                    // Practical subjects have lower max marks
                    $maxMarks = in_array($assignment->subject_id, [7, 9, 14, 15, 16, 19, 20, 21]) ? 75 : 100;
                    $passingMarks = round($maxMarks * 0.40);
                    $marksObtained = $this->faker->numberBetween(round($maxMarks * 0.25), $maxMarks);
                    
                    $marksData[] = [
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'subject_id' => $assignment->subject_id,
                        'marks_obtained' => $marksObtained,
                        'max_marks' => $maxMarks,
                        'passing_marks' => $passingMarks,
                        'remarks' => $marksObtained >= $passingMarks ? 'Pass' : 'Fail - Needs Improvement',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        
        foreach (array_chunk($marksData, 500) as $chunk) {
            DB::table('exam_marks')->insert($chunk);
        }
        $this->command->info("✅ " . count($marksData) . " exam marks created");

        // ==============================================
        // 23. GRADE SCALES
        // ==============================================
        DB::table('grade_scales')->insert([
            'name' => 'Pakistan Standard',
            'grades' => json_encode([
                ['min' => 90, 'max' => 100, 'grade' => 'A+', 'remarks' => 'Outstanding'],
                ['min' => 80, 'max' => 89, 'grade' => 'A', 'remarks' => 'Excellent'],
                ['min' => 70, 'max' => 79, 'grade' => 'B', 'remarks' => 'Very Good'],
                ['min' => 60, 'max' => 69, 'grade' => 'C', 'remarks' => 'Good'],
                ['min' => 50, 'max' => 59, 'grade' => 'D', 'remarks' => 'Satisfactory'],
                ['min' => 40, 'max' => 49, 'grade' => 'E', 'remarks' => 'Pass'],
                ['min' => 0, 'max' => 39, 'grade' => 'F', 'remarks' => 'Fail'],
            ]),
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ==============================================
        // 24. EXAM RESULTS
        // ==============================================
        $this->command->info("\n🏆 Creating exam results...");
        
        $resultData = [];
        
        foreach ($examsDb as $exam) {
            $students = DB::table('students')->where('class_section_id', $exam->class_section_id)->get();
            
            foreach ($students as $student) {
                $marks = DB::table('exam_marks')
                    ->where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->get();
                
                $totalObtained = $marks->sum('marks_obtained');
                $totalMax = $marks->sum('max_marks');
                $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;
                
                // Calculate grade
                $grade = 'F';
                if ($percentage >= 90) $grade = 'A+';
                elseif ($percentage >= 80) $grade = 'A';
                elseif ($percentage >= 70) $grade = 'B';
                elseif ($percentage >= 60) $grade = 'C';
                elseif ($percentage >= 50) $grade = 'D';
                elseif ($percentage >= 40) $grade = 'E';
                
                $resultData[] = [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'class_section_id' => $exam->class_section_id,
                    'total_marks' => $totalObtained,
                    'total_max_marks' => $totalMax,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'remarks' => $percentage >= 40 ? 'Pass' : 'Fail',
                    'rank_in_class' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        foreach (array_chunk($resultData, 500) as $chunk) {
            DB::table('exam_results')->insert($chunk);
        }
        
        // Update ranks within each section per exam
        foreach ($examsDb as $exam) {
            $results = DB::table('exam_results')
                ->where('exam_id', $exam->id)
                ->where('class_section_id', $exam->class_section_id)
                ->orderBy('percentage', 'desc')
                ->get();
            
            $rank = 1;
            foreach ($results as $result) {
                DB::table('exam_results')
                    ->where('id', $result->id)
                    ->update(['rank_in_class' => $rank]);
                $rank++;
            }
        }
        
        $this->command->info("✅ " . count($resultData) . " exam results with ranks");

        // ==============================================
        // 25. STAFF ATTENDANCE
        // ==============================================
        $this->command->info("\n📋 Creating staff attendance records...");
        
        $staffAttendanceData = [];
        $startDate = now()->subDays(30);
        
        for ($date = clone $startDate; $date->lte(now()); $date->addDay()) {
            if ($date->isWeekend()) continue;
            
            foreach ($staffIds as $staffId) {
                $rand = rand(1, 100);
                $status = $rand <= 85 ? 'present' : ($rand <= 92 ? 'late' : ($rand <= 97 ? 'half_day' : 'absent'));
                
                $staffAttendanceData[] = [
                    'staff_id' => $staffId,
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'check_in' => in_array($status, ['present', 'late']) 
                        ? $this->faker->dateTimeBetween($date->format('Y-m-d 07:45:00'), $date->format('Y-m-d 08:30:00'))->format('H:i:s') 
                        : null,
                    'check_out' => in_array($status, ['present', 'late']) 
                        ? $this->faker->dateTimeBetween($date->format('Y-m-d 14:00:00'), $date->format('Y-m-d 15:00:00'))->format('H:i:s') 
                        : null,
                    'late_minutes' => $status == 'late' ? rand(5, 30) : 0,
                    'is_approved' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        foreach (array_chunk($staffAttendanceData, 500) as $chunk) {
            DB::table('staff_attendances')->insert($chunk);
        }
        $this->command->info("✅ " . count($staffAttendanceData) . " staff attendance records");

        // ==============================================
        // 26. STUDENT ATTENDANCE
        // ==============================================
        $this->command->info("\n📝 Creating student attendance records...");
        
        $studentAttendanceData = [];
        
        for ($date = clone $startDate; $date->lte(now()); $date->addDay()) {
            if ($date->isWeekend()) continue;
            
            $dayName = strtolower($date->format('l'));
            
            foreach ($studentsDb as $student) {
                $entry = DB::table('timetable_entries')
                    ->where('class_section_id', $student->class_section_id)
                    ->where('day_of_week', $dayName)
                    ->first();
                
                $rand = rand(1, 100);
                $status = $rand <= 80 ? 'present' : ($rand <= 90 ? 'late' : ($rand <= 95 ? 'half_day' : 'absent'));
                
                $studentAttendanceData[] = [
                    'student_id' => $student->id,
                    'class_section_id' => $student->class_section_id,
                    'subject_id' => $entry ? $entry->subject_id : $subjectsDb->first()->id,
                    'teacher_id' => $entry ? $entry->teacher_id : ($teacherIds[0] ?? 1),
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'remarks' => null,
                    'half_day_type' => $status == 'half_day' ? $this->faker->randomElement(['morning', 'afternoon']) : null,
                    'half_day_reason' => $status == 'half_day' ? 'Sick' : null,
                    'late_minutes' => $status == 'late' ? rand(5, 30) : 0,
                    'late_reason' => $status == 'late' ? 'Traffic' : null,
                    'check_in' => in_array($status, ['present', 'late']) 
                        ? $this->faker->dateTimeBetween($date->format('Y-m-d 07:50:00'), $date->format('Y-m-d 08:20:00'))->format('H:i:s') 
                        : null,
                    'check_out' => in_array($status, ['present', 'late']) ? '14:30:00' : null,
                    'is_approved' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        foreach (array_chunk($studentAttendanceData, 500) as $chunk) {
            DB::table('student_attendance')->insert($chunk);
        }
        $this->command->info("✅ " . count($studentAttendanceData) . " student attendance records");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // ==============================================
        // FINAL SUMMARY
        // ==============================================
        $this->command->info("\n========================================");
        $this->command->info("✅ COMPLETE PAKISTANI SCHOOL DATA SEEDED!");
        $this->command->info("========================================");
        $this->command->info("🏫 School Structure:");
        $this->command->info("   KG: 2 sections × 12 = 24 students");
        $this->command->info("   Class 1-8: 8 grades × 2 sections × 12 = 192 students");
        $this->command->info("   Class 9 Science: 2 sections × 12 = 24 students");
        $this->command->info("   Class 9 Arts: 2 sections × 12 = 24 students");
        $this->command->info("   Class 10 Science: 2 sections × 12 = 24 students");
        $this->command->info("   Class 10 Arts: 2 sections × 12 = 24 students");
        $this->command->info("   📊 Total: " . count($studentData) . " students");
        $this->command->info("");
        $this->command->info("👨‍🏫 Staff: " . count($teacherIds) . " teachers + 2 admin = " . count($staffData));
        $this->command->info("📚 " . count($subjectsDb) . " subjects with Pakistani textbook names");
        $this->command->info("📝 " . count($examData) . " exams (Mid Term + Final Term)");
        $this->command->info("📊 " . count($marksData) . " exam marks");
        $this->command->info("🏆 " . count($resultData) . " exam results with ranks");
        $this->command->info("💰 " . count($installmentData) . " fee installments");
        $this->command->info("📋 " . count($timetableData) . " timetable entries");
        $this->command->info("========================================\n");
    }
}