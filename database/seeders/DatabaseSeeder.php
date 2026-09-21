<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    private $faker;

    // ============================================================
    // Pakistani student names - Male First Names
    // ============================================================
    private $maleFirstNames = [
        'Ahmed', 'Bilal', 'Daniyal', 'Ehsan', 'Fahad', 'Ghazi', 'Hamza', 'Ibrahim',
        'Junaid', 'Kamran', 'Moiz', 'Nabeel', 'Omar', 'Qasim', 'Rayan', 'Saad',
        'Taha', 'Usman', 'Waleed', 'Yahya', 'Zaid', 'Ali', 'Hassan', 'Hussain',
        'Abdullah', 'Rehan', 'Shayan', 'Farhan', 'Arham', 'Ayaan', 'Mustafa', 'Zain',
        'Arslan', 'Baber', 'Dawood', 'Faizan', 'Haroon', 'Imran', 'Kashif', 'Luqman',
        'Mikaeel', 'Noman', 'Owais', 'Rizwan', 'Salman', 'Tahir', 'Umair', 'Wasim',
        'Adeel', 'Basit', 'Danish', 'Faisal', 'Haider', 'Irfan', 'Jawad', 'Khalid',
    ];

    // ============================================================
    // Pakistani student names - Male Last Names
    // ============================================================
    private $maleLastNames = [
        'Khan', 'Ahmed', 'Butt', 'Chaudhry', 'Dar', 'Ejaz', 'Farooq', 'Ghafoor',
        'Hashmi', 'Iqbal', 'Javed', 'Kamal', 'Lodhi', 'Malik', 'Naeem', 'Qureshi',
        'Rana', 'Sheikh', 'Tariq', 'Umar', 'Waseem', 'Yousaf', 'Zafar', 'Siddiqui',
        'Rashid', 'Nasir', 'Mahmood', 'Shah', 'Aziz', 'Latif', 'Anwar', 'Hameed',
        'Abbasi', 'Bhatti', 'Cheema', 'Dogar', 'Gill', 'Jutt', 'Khokhar', 'Mughal',
    ];

    // ============================================================
    // Pakistani student names - Female First Names
    // ============================================================
    private $femaleFirstNames = [
        'Ayesha', 'Bisma', 'Dua', 'Eman', 'Fatima', 'Ghazala', 'Hira', 'Iqra',
        'Javeria', 'Kainat', 'Laiba', 'Mahnoor', 'Nimra', 'Omama', 'Qurat', 'Rida',
        'Sana', 'Tehreem', 'Uzma', 'Warda', 'Yusra', 'Zainab', 'Amina', 'Bushra',
        'Khadija', 'Maryam', 'Noor', 'Sadia', 'Tahira', 'Zara', 'Anum', 'Sara',
        'Areeba', 'Dania', 'Fiza', 'Hania', 'Jannat', 'Kinza', 'Lubna', 'Mehak',
        'Nashra', 'Palwasha', 'Ramsha', 'Sidra', 'Tayyaba', 'Unzila', 'Wajiha', 'Zoha',
        'Aleena', 'Bareera', 'Durre', 'Eshal', 'Hafsa', 'Inaya', 'Jiya', 'Minahil',
    ];

    // ============================================================
    // Pakistani student names - Female Last Names
    // ============================================================
    private $femaleLastNames = [
        'Khan', 'Ahmed', 'Butt', 'Chaudhry', 'Dar', 'Fatima', 'Ghafoor', 'Hashmi',
        'Iqbal', 'Javed', 'Khalid', 'Lodhi', 'Malik', 'Naeem', 'Qureshi', 'Rana',
        'Sheikh', 'Tariq', 'Umar', 'Waseem', 'Yousaf', 'Zafar', 'Siddiqui', 'Rashid',
        'Nasir', 'Mahmood', 'Shah', 'Aziz', 'Batool', 'Hassan', 'Hussain', 'Abbasi',
        'Bhatti', 'Cheema', 'Gill', 'Jutt', 'Mughal', 'Syed', 'Waraich', 'Zaidi',
    ];

    // ============================================================
    // Pakistani father names
    // ============================================================
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
        'Muhammad Bashir', 'Muhammad Sharif', 'Ghulam Nabi', 'Muhammad Amin',
    ];

    // ============================================================
    // Pakistani cities
    // ============================================================
    private $cities = [
        'Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Faisalabad',
        'Multan', 'Gujranwala', 'Peshawar', 'Quetta', 'Sialkot',
        'Sargodha', 'Bahawalpur', 'Gujrat', 'Jhelum', 'Sheikhupura',
        'Rahim Yar Khan', 'Sahiwal', 'Wah Cantt', 'Mardan', 'Abbottabad',
    ];

    // ============================================================
    // Pakistani city coordinates for transport routes
    // ============================================================
    private $cityCoordinates = [
        'Lahore'     => ['lat' => 31.5204, 'lng' => 74.3587],
        'Karachi'    => ['lat' => 24.8607, 'lng' => 67.0011],
        'Islamabad'  => ['lat' => 33.6844, 'lng' => 73.0479],
        'Rawalpindi' => ['lat' => 33.5651, 'lng' => 73.0169],
        'Faisalabad' => ['lat' => 31.4504, 'lng' => 73.1350],
        'Multan'     => ['lat' => 30.1575, 'lng' => 71.5249],
        'Gujranwala' => ['lat' => 32.1610, 'lng' => 74.1883],
        'Peshawar'   => ['lat' => 34.0151, 'lng' => 71.5249],
        'Quetta'     => ['lat' => 30.1798, 'lng' => 66.9750],
        'Sialkot'    => ['lat' => 32.4945, 'lng' => 74.5229],
    ];

    // ============================================================
    // Pakistani area names for transport stops
    // ============================================================
    private $areaNames = [
        'Gulshan-e-Iqbal', 'Defence', 'Gulberg', 'Johar Town', 'Model Town',
        'Township', 'Wapda Town', 'Valencia', 'Askari', 'Bahria Town',
        'DHA', 'Faisal Town', 'Garden Town', 'Muslim Town', 'Shadman',
        'Samanabad', 'Iqbal Town', 'Green Town', 'Mughalpura', 'Ismail Nagar',
        'Saddar', 'Cantt', 'Race Course', 'Mall Road', 'Queen\'s Road',
        'Punjab University', 'UET', 'LUMS', 'Gulshan-e-Ravi', 'Mohni Road',
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
        $this->command->info('   🚌 Transport System Included');
        $this->command->info('   🏠 Hostel System Included');
        $this->command->info('   👤 Users & Roles Included');
        $this->command->info('========================================');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ============================================================
        // 0. TRUNCATE ALL TABLES
        // ============================================================
        $tables = [
            // Spatie permission tables — truncate FIRST
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'users',

            // Transport tables
            'vehicle_fuel_logs',
            'vehicle_maintenance_logs',
            'vehicle_trip_logs',
            'student_transport_fee_payments',
            'student_transports',
            'transport_fee_types',
            'route_stops',
            'transport_routes',
            'vehicles',
            'drivers',

            // Hostel tables
            'hostel_room_maintenance',
            'hostel_attendance',
            'student_hostel_fee_payments',
            'student_hostel_allocations',
            'hostel_fee_types',
            'hostel_rooms',
            'hostel_room_types',
            'hostel_staff_assignments',
            'hostels',

            // Finance tables
            'student_discounts',
            'invoices',
            'student_fee_submissions',

            // Attendance tables
            'student_attendance_summaries',
            'student_attendance',
            'attendance_summaries',
            'staff_attendances',
            'leaves',

            // Salary tables
            'salaries',
            'payroll_items',
            'payroll_batches',
            'salary_templates',

            // Teaching tables
            'teacher_class_attendance',
            'attendance_teacher_section',
            'common_subject_classes',
            'timetable_entries',
            'teacher_availabilities',
            'teacher_subjects',
            'subject_assignments',
            'time_slots',
            'school_timings',

            // Exam tables
            'exam_results',
            'exam_marks',
            'exams',
            'exam_groups',
            'exam_types',
            'grade_scales',

            // Student/staff tables
            'students',
            'class_sections',
            'classes',
            'elective_tracks',
            'streams',
            'grades',
            'academic_sessions',
            'subjects',
            'payment_methods',
            'banks',
            'discounts',
            'fee_submission_types',
            'fee_types',
            'staff',
            'employee_categories',
            'rooms',
            'floors',
            'blocks',

            // Certificates
            'certificate_distributions',
            'certificate_types',
            'transfer_certificates',
            'student_transfers',
            'attendance_reasons',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        $this->command->info("✅ All tables truncated");

        // ============================================================
        // 1. ATTENDANCE REASONS
        // ============================================================
        $this->command->info("\n📋 Creating attendance reasons...");

        $attendanceReasons = [
            // Half Day
            ['category' => 'half_day', 'reason' => 'Medical Appointment',     'description' => 'Doctor ya medical appointment',       'is_active' => true, 'sort_order' => 1],
            ['category' => 'half_day', 'reason' => 'Family Emergency',        'description' => 'Ghar par emergency',                  'is_active' => true, 'sort_order' => 2],
            ['category' => 'half_day', 'reason' => 'Personal Work',           'description' => 'Personal kaam',                       'is_active' => true, 'sort_order' => 3],
            ['category' => 'half_day', 'reason' => 'Sick',                    'description' => 'Tabiyat kharab',                      'is_active' => true, 'sort_order' => 4],
            ['category' => 'half_day', 'reason' => 'Exam/Test',               'description' => 'Exam ya test dena',                   'is_active' => true, 'sort_order' => 5],
            ['category' => 'half_day', 'reason' => 'Religious Event',         'description' => 'Mazhabi taqreeb',                     'is_active' => true, 'sort_order' => 6],
            ['category' => 'half_day', 'reason' => 'Sports/Activity',         'description' => 'Sports tournament',                   'is_active' => true, 'sort_order' => 7],
            ['category' => 'half_day', 'reason' => 'School Event',            'description' => 'School event mein participation',     'is_active' => true, 'sort_order' => 8],
            ['category' => 'half_day', 'reason' => 'Travel',                  'description' => 'Safar',                               'is_active' => true, 'sort_order' => 9],
            ['category' => 'half_day', 'reason' => 'Other',                   'description' => 'Digar',                               'is_active' => true, 'sort_order' => 10],

            // Late
            ['category' => 'late',     'reason' => 'Traffic',                 'description' => 'Traffic jam',                         'is_active' => true, 'sort_order' => 1],
            ['category' => 'late',     'reason' => 'Transportation Delay',    'description' => 'Bus/van late ya available nahi',      'is_active' => true, 'sort_order' => 2],
            ['category' => 'late',     'reason' => 'Slept Late',              'description' => 'Der se uthna',                        'is_active' => true, 'sort_order' => 3],
            ['category' => 'late',     'reason' => 'Medical Emergency',       'description' => 'Health issue',                        'is_active' => true, 'sort_order' => 4],
            ['category' => 'late',     'reason' => 'Family Emergency',        'description' => 'Ghar par emergency',                  'is_active' => true, 'sort_order' => 5],
            ['category' => 'late',     'reason' => 'Weather',                 'description' => 'Kharab mausam',                       'is_active' => true, 'sort_order' => 6],
            ['category' => 'late',     'reason' => 'Vehicle Issue',           'description' => 'Car/bike kharab',                     'is_active' => true, 'sort_order' => 7],
            ['category' => 'late',     'reason' => 'Heavy Rain',              'description' => 'Barish ki waja se',                   'is_active' => true, 'sort_order' => 8],
            ['category' => 'late',     'reason' => 'Public Transport Strike', 'description' => 'Transport strike',                    'is_active' => true, 'sort_order' => 9],
            ['category' => 'late',     'reason' => 'Other',                   'description' => 'Digar',                               'is_active' => true, 'sort_order' => 10],

            // Absent
            ['category' => 'absent',   'reason' => 'Sick',                    'description' => 'Bemar',                               'is_active' => true, 'sort_order' => 1],
            ['category' => 'absent',   'reason' => 'Family Emergency',        'description' => 'Ghar par emergency',                  'is_active' => true, 'sort_order' => 2],
            ['category' => 'absent',   'reason' => 'Travel',                  'description' => 'Shehar se bahar',                     'is_active' => true, 'sort_order' => 3],
            ['category' => 'absent',   'reason' => 'Unwell',                  'description' => 'Tabiyat theek nahi',                  'is_active' => true, 'sort_order' => 4],
            ['category' => 'absent',   'reason' => 'Wedding',                 'description' => 'Shaadi ki taqreeb',                   'is_active' => true, 'sort_order' => 5],
            ['category' => 'absent',   'reason' => 'Death in Family',         'description' => 'Ghar mein intiqal',                   'is_active' => true, 'sort_order' => 6],
        ];

        foreach ($attendanceReasons as $data) {
            DB::table('attendance_reasons')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $this->command->info("✅ " . count($attendanceReasons) . " attendance reasons created");

        // ============================================================
        // 2. BLOCKS AND FLOORS
        // ============================================================
        $this->command->info("\n🏗️ Creating blocks and floors...");

        $blocks = [
            ['name' => 'Main Building',   'code' => 'MAIN'],
            ['name' => 'Primary Block',   'code' => 'PRI'],
            ['name' => 'Secondary Block', 'code' => 'SEC'],
            ['name' => 'Science Wing',    'code' => 'SCI'],
            ['name' => 'Admin Block',     'code' => 'ADM'],
        ];

        foreach ($blocks as $data) {
            DB::table('blocks')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $blockMap = DB::table('blocks')->pluck('id', 'code')->toArray();

        $floors = [
            ['name' => 'Ground Floor',     'code' => 'G',   'level' => 0, 'block_id' => $blockMap['MAIN']],
            ['name' => 'First Floor',      'code' => '1',   'level' => 1, 'block_id' => $blockMap['MAIN']],
            ['name' => 'Second Floor',     'code' => '2',   'level' => 2, 'block_id' => $blockMap['MAIN']],
            ['name' => 'Primary Ground',   'code' => 'PG',  'level' => 0, 'block_id' => $blockMap['PRI']],
            ['name' => 'Primary First',    'code' => 'PF',  'level' => 1, 'block_id' => $blockMap['PRI']],
            ['name' => 'Secondary Ground', 'code' => 'SG',  'level' => 0, 'block_id' => $blockMap['SEC']],
            ['name' => 'Secondary First',  'code' => 'SF',  'level' => 1, 'block_id' => $blockMap['SEC']],
            ['name' => 'Science Ground',   'code' => 'SCG', 'level' => 0, 'block_id' => $blockMap['SCI']],
            ['name' => 'Science First',    'code' => 'SCF', 'level' => 1, 'block_id' => $blockMap['SCI']],
            ['name' => 'Admin Ground',     'code' => 'AG',  'level' => 0, 'block_id' => $blockMap['ADM']],
        ];

        foreach ($floors as $data) {
            DB::table('floors')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $floorMap = DB::table('floors')->pluck('id', 'code')->toArray();
        $this->command->info("✅ " . count($blocks) . " blocks, " . count($floors) . " floors");

        // ============================================================
        // 3. ROOMS
        // ============================================================
        $this->command->info("\n🏠 Creating rooms...");

        $roomsData = [
            // Main Building - Ground Floor
            ['name' => 'Room 101',        'room_number' => '101',       'type' => 'classroom',  'capacity' => 40,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['G']],
            ['name' => 'Room 102',        'room_number' => '102',       'type' => 'classroom',  'capacity' => 40,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['G']],
            ['name' => 'Room 103',        'room_number' => '103',       'type' => 'classroom',  'capacity' => 35,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['G']],
            ['name' => 'Room 104',        'room_number' => '104',       'type' => 'classroom',  'capacity' => 45,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['G']],

            // Main Building - First Floor
            ['name' => 'Room 201',        'room_number' => '201',       'type' => 'classroom',  'capacity' => 40,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['1']],
            ['name' => 'Room 202',        'room_number' => '202',       'type' => 'classroom',  'capacity' => 40,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['1']],
            ['name' => 'Room 203',        'room_number' => '203',       'type' => 'classroom',  'capacity' => 35,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['1']],
            ['name' => 'Room 204',        'room_number' => '204',       'type' => 'classroom',  'capacity' => 45,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['1']],

            // Primary Block
            ['name' => 'KG Room A',       'room_number' => 'KG-A',      'type' => 'classroom',  'capacity' => 30,  'block_id' => $blockMap['PRI'],  'floor_id' => $floorMap['PG']],
            ['name' => 'KG Room B',       'room_number' => 'KG-B',      'type' => 'classroom',  'capacity' => 30,  'block_id' => $blockMap['PRI'],  'floor_id' => $floorMap['PG']],
            ['name' => 'Class 1 Room',    'room_number' => 'C1',        'type' => 'classroom',  'capacity' => 30,  'block_id' => $blockMap['PRI'],  'floor_id' => $floorMap['PG']],
            ['name' => 'Class 2 Room',    'room_number' => 'C2',        'type' => 'classroom',  'capacity' => 30,  'block_id' => $blockMap['PRI'],  'floor_id' => $floorMap['PF']],
            ['name' => 'Class 3 Room',    'room_number' => 'C3',        'type' => 'classroom',  'capacity' => 30,  'block_id' => $blockMap['PRI'],  'floor_id' => $floorMap['PF']],
            ['name' => 'Class 4 Room',    'room_number' => 'C4',        'type' => 'classroom',  'capacity' => 30,  'block_id' => $blockMap['PRI'],  'floor_id' => $floorMap['PF']],
            ['name' => 'Class 5 Room',    'room_number' => 'C5',        'type' => 'classroom',  'capacity' => 35,  'block_id' => $blockMap['PRI'],  'floor_id' => $floorMap['PF']],

            // Secondary Block
            ['name' => 'Class 6 Room',    'room_number' => 'C6',        'type' => 'classroom',  'capacity' => 40,  'block_id' => $blockMap['SEC'],  'floor_id' => $floorMap['SG']],
            ['name' => 'Class 7 Room',    'room_number' => 'C7',        'type' => 'classroom',  'capacity' => 40,  'block_id' => $blockMap['SEC'],  'floor_id' => $floorMap['SG']],
            ['name' => 'Class 8 Room',    'room_number' => 'C8',        'type' => 'classroom',  'capacity' => 40,  'block_id' => $blockMap['SEC'],  'floor_id' => $floorMap['SF']],
            ['name' => 'Class 9 Room',    'room_number' => 'C9',        'type' => 'classroom',  'capacity' => 45,  'block_id' => $blockMap['SEC'],  'floor_id' => $floorMap['SF']],
            ['name' => 'Class 10 Room',   'room_number' => 'C10',       'type' => 'classroom',  'capacity' => 45,  'block_id' => $blockMap['SEC'],  'floor_id' => $floorMap['SF']],

            // Science Wing - Labs
            ['name' => 'Physics Lab',     'room_number' => 'PHY-LAB',   'type' => 'lab',        'capacity' => 30,  'block_id' => $blockMap['SCI'],  'floor_id' => $floorMap['SCG']],
            ['name' => 'Chemistry Lab',   'room_number' => 'CHEM-LAB',  'type' => 'lab',        'capacity' => 30,  'block_id' => $blockMap['SCI'],  'floor_id' => $floorMap['SCG']],
            ['name' => 'Biology Lab',     'room_number' => 'BIO-LAB',   'type' => 'lab',        'capacity' => 30,  'block_id' => $blockMap['SCI'],  'floor_id' => $floorMap['SCF']],
            ['name' => 'Computer Lab 1',  'room_number' => 'COMP-LAB1', 'type' => 'lab',        'capacity' => 30,  'block_id' => $blockMap['SCI'],  'floor_id' => $floorMap['SCF']],
            ['name' => 'Computer Lab 2',  'room_number' => 'COMP-LAB2', 'type' => 'lab',        'capacity' => 25,  'block_id' => $blockMap['SCI'],  'floor_id' => $floorMap['SCF']],

            // Special Rooms
            ['name' => 'Library',         'room_number' => 'LIB',       'type' => 'library',    'capacity' => 100, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['2']],
            ['name' => 'Auditorium',      'room_number' => 'AUD',       'type' => 'auditorium', 'capacity' => 300, 'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['2']],
            ['name' => 'Art Room',        'room_number' => 'ART',       'type' => 'activity',   'capacity' => 35,  'block_id' => $blockMap['MAIN'], 'floor_id' => $floorMap['2']],

            // Admin
            ['name' => 'Staff Room',      'room_number' => 'STAFF',     'type' => 'other',      'capacity' => 50,  'block_id' => $blockMap['ADM'],  'floor_id' => $floorMap['AG']],
            ['name' => 'Principal Office','room_number' => 'POFF',      'type' => 'other',      'capacity' => 15,  'block_id' => $blockMap['ADM'],  'floor_id' => $floorMap['AG']],
            ['name' => 'Admin Office',    'room_number' => 'ADMIN',     'type' => 'other',      'capacity' => 25,  'block_id' => $blockMap['ADM'],  'floor_id' => $floorMap['AG']],
        ];

        foreach ($roomsData as $data) {
            DB::table('rooms')->insert(array_merge($data, [
                'is_available'   => true,
                'has_ac'         => true,
                'has_projector'  => in_array($data['type'], ['lab', 'classroom']),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]));
        }
        $this->command->info("✅ " . count($roomsData) . " rooms created");

        // ============================================================
        // 4. ACADEMIC SESSION
        // ============================================================
        $this->command->info("\n📅 Creating academic session...");

        DB::table('academic_sessions')->insert([
            'name'        => '2024-2025',
            'start_date'  => '2024-04-01',
            'end_date'    => '2025-03-31',
            'is_active'   => true,
            'description' => 'Current Academic Session 2024-2025',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $sessionId = DB::getPdo()->lastInsertId();
        $this->command->info("✅ Academic Session 2024-2025 created");

        // ============================================================
        // 5. GRADES (KG to 10th)
        // ============================================================
        $this->command->info("\n📚 Creating grades (KG to 10th)...");

        $grades = [
            ['name' => 'KG',  'numeric_value' => 0],
            ['name' => '1',   'numeric_value' => 1],
            ['name' => '2',   'numeric_value' => 2],
            ['name' => '3',   'numeric_value' => 3],
            ['name' => '4',   'numeric_value' => 4],
            ['name' => '5',   'numeric_value' => 5],
            ['name' => '6',   'numeric_value' => 6],
            ['name' => '7',   'numeric_value' => 7],
            ['name' => '8',   'numeric_value' => 8],
            ['name' => '9',   'numeric_value' => 9],
            ['name' => '10',  'numeric_value' => 10],
        ];
        DB::table('grades')->insert($grades);
        $gradesDb = DB::table('grades')->get();
        $gradeMap = $gradesDb->keyBy('name');
        $this->command->info("✅ " . count($grades) . " grades created");

        // ============================================================
        // 6. STREAMS
        // ============================================================
        $this->command->info("\n📊 Creating streams...");

        $streams = [
            ['name' => 'General', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Arts',    'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('streams')->insert($streams);
        $streamMap = DB::table('streams')->pluck('id', 'name')->toArray();
        $this->command->info("✅ Streams created");

        // ============================================================
        // 7. CLASSES
        // ============================================================
        $this->command->info("\n🏫 Creating classes...");

        $classesData = [];

        // KG to 8 - General stream
        foreach (['KG', '1', '2', '3', '4', '5', '6', '7', '8'] as $name) {
            $classesData[] = [
                'academic_session_id' => $sessionId,
                'grade_id'            => $gradeMap[$name]->id,
                'stream_id'           => $streamMap['General'],
                'elective_track_id'   => null,
                'capacity'            => 30,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }

        // 9th Grade - Science
        $classesData[] = [
            'academic_session_id' => $sessionId,
            'grade_id'            => $gradeMap['9']->id,
            'stream_id'           => $streamMap['Science'],
            'elective_track_id'   => null,
            'capacity'            => 40,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];

        // 9th Grade - Arts
        $classesData[] = [
            'academic_session_id' => $sessionId,
            'grade_id'            => $gradeMap['9']->id,
            'stream_id'           => $streamMap['Arts'],
            'elective_track_id'   => null,
            'capacity'            => 35,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];

        // 10th Grade - Science
        $classesData[] = [
            'academic_session_id' => $sessionId,
            'grade_id'            => $gradeMap['10']->id,
            'stream_id'           => $streamMap['Science'],
            'elective_track_id'   => null,
            'capacity'            => 45,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];

        // 10th Grade - Arts
        $classesData[] = [
            'academic_session_id' => $sessionId,
            'grade_id'            => $gradeMap['10']->id,
            'stream_id'           => $streamMap['Arts'],
            'elective_track_id'   => null,
            'capacity'            => 40,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];

        DB::table('classes')->insert($classesData);
        $classesDb = DB::table('classes')->where('academic_session_id', $sessionId)->get();
        $this->command->info("✅ " . count($classesDb) . " classes created");

        // ============================================================
        // 8. CLASS SECTIONS (2 per class)
        // ============================================================
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
                    'class_id'         => $class->id,
                    'section_name'     => $sectionName,
                    'capacity'         => 30,
                    'student_count'    => 12,
                    'student_strength' => 12,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }

        DB::table('class_sections')->insert($classSectionsData);
        $classSectionsDb = DB::table('class_sections')->get();
        $this->command->info("✅ " . count($classSectionsDb) . " class sections (2 each)");

        // ============================================================
        // 9. SUBJECTS (Pakistani Curriculum)
        // ============================================================
        $this->command->info("\n📖 Creating subjects...");

        $subjects = [
            ['name' => 'English',           'code' => 'ENG',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Urdu',              'code' => 'URD',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Mathematics',       'code' => 'MATH',  'type' => 'theory',    'is_active' => true],
            ['name' => 'Islamiyat',         'code' => 'ISL',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Science',           'code' => 'SCI',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Social Studies',    'code' => 'SST',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Computer',          'code' => 'COMP',  'type' => 'practical', 'is_active' => true],
            ['name' => 'General Knowledge', 'code' => 'GK',    'type' => 'theory',    'is_active' => true],
            ['name' => 'Drawing/Art',       'code' => 'ART',   'type' => 'practical', 'is_active' => true],
            ['name' => 'Nazra Quran',       'code' => 'NAZRA', 'type' => 'theory',    'is_active' => true],
            ['name' => 'History',           'code' => 'HIST',  'type' => 'theory',    'is_active' => true],
            ['name' => 'Geography',         'code' => 'GEO',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Pakistan Studies',  'code' => 'PST',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Physics',           'code' => 'PHY',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Chemistry',         'code' => 'CHEM',  'type' => 'theory',    'is_active' => true],
            ['name' => 'Biology',           'code' => 'BIO',   'type' => 'theory',    'is_active' => true],
            ['name' => 'General Science',   'code' => 'GSC',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Economics',         'code' => 'ECO',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Civics',            'code' => 'CIV',   'type' => 'theory',    'is_active' => true],
            ['name' => 'Home Economics',    'code' => 'HECO',  'type' => 'theory',    'is_active' => true],
            ['name' => 'Arabic',            'code' => 'ARB',   'type' => 'theory',    'is_active' => true],
        ];

        DB::table('subjects')->insert($subjects);
        $subjectsDb = DB::table('subjects')->get();
        $subjectsByName = $subjectsDb->keyBy('name');
        $this->command->info("✅ " . count($subjectsDb) . " subjects created");

        // ============================================================
        // 10. SUBJECT ASSIGNMENTS (Per class section)
        // ============================================================
        $this->command->info("\n📋 Assigning subjects to class sections...");

        $gradeSubjects = [
            'KG' => ['English', 'Urdu', 'Mathematics', 'General Knowledge', 'Drawing/Art', 'Islamiyat'],
            '1'  => ['English', 'Urdu', 'Mathematics', 'General Knowledge', 'Islamiyat', 'Computer'],
            '2'  => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies'],
            '3'  => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies'],
            '4'  => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies'],
            '5'  => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies', 'Nazra Quran'],
            '6'  => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies', 'History', 'Geography', 'Nazra Quran'],
            '7'  => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies', 'History', 'Geography', 'Nazra Quran'],
            '8'  => ['English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat', 'Computer', 'Social Studies', 'History', 'Geography', 'Nazra Quran'],
        ];

        $scienceSubjects9th10th = ['English', 'Urdu', 'Mathematics', 'Islamiyat', 'Pakistan Studies', 'Physics', 'Chemistry', 'Biology', 'Computer'];
        $artsSubjects9th10th    = ['English', 'Urdu', 'Mathematics', 'Islamiyat', 'Pakistan Studies', 'General Science', 'Economics', 'Civics', 'Home Economics', 'Arabic'];

        $subjectAssignmentData = [];

        foreach ($classSectionsDb as $section) {
            $classInfo  = $classesDb->where('id', $section->class_id)->first();
            $gradeInfo  = $gradesDb->where('id', $classInfo->grade_id)->first();
            $streamInfo = DB::table('streams')->where('id', $classInfo->stream_id)->value('name');
            $gradeName  = $gradeInfo->name;

            $subjectsForSection = [];

            if (isset($gradeSubjects[$gradeName])) {
                $subjectsForSection = $gradeSubjects[$gradeName];
            } elseif ($gradeName == '9' || $gradeName == '10') {
                $subjectsForSection = $streamInfo == 'Science' ? $scienceSubjects9th10th : $artsSubjects9th10th;
            }

            foreach ($subjectsForSection as $subjectName) {
                if (isset($subjectsByName[$subjectName])) {
                    $weeklyFreq = 5;

                    if (in_array($subjectName, ['Computer', 'Drawing/Art', 'Nazra Quran', 'Arabic', 'Home Economics'])) {
                        $weeklyFreq = 3;
                    }
                    if (in_array($subjectName, ['Physics', 'Chemistry', 'Biology'])) {
                        $weeklyFreq = 6;
                    }

                    $subjectAssignmentData[] = [
                        'class_section_id' => $section->id,
                        'subject_id'       => $subjectsByName[$subjectName]->id,
                        'weekly_frequency' => $weeklyFreq,
                        'is_elective'      => false,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }
        }

        DB::table('subject_assignments')->insert($subjectAssignmentData);
        $this->command->info("✅ " . count($subjectAssignmentData) . " subject assignments created");

        // ============================================================
        // 11. EMPLOYEE CATEGORIES & STAFF
        // ============================================================
        $this->command->info("\n👥 Creating employee categories and staff...");

        DB::table('employee_categories')->insert([
            ['name' => 'Teaching Staff', 'code' => 'TCH', 'description' => 'Teachers',      'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin Staff',    'code' => 'ADM', 'description' => 'Administration', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $teacherSpecializations = [
            'English', 'Urdu', 'Mathematics', 'Science', 'Islamiyat',
            'Social Studies', 'Computer', 'General Knowledge', 'Drawing/Art', 'Nazra Quran',
            'History', 'Geography', 'Pakistan Studies', 'Physics', 'Chemistry',
            'Biology', 'General Science', 'Economics', 'Civics', 'Home Economics',
            'Arabic',
        ];

        $teacherNames = [
            ['first' => 'Muhammad', 'last' => 'Asif',    'gender' => 'Male'],
            ['first' => 'Saima',    'last' => 'Akram',   'gender' => 'Female'],
            ['first' => 'Imran',    'last' => 'Khan',    'gender' => 'Male'],
            ['first' => 'Farah',    'last' => 'Naz',     'gender' => 'Female'],
            ['first' => 'Ahmed',    'last' => 'Raza',    'gender' => 'Male'],
            ['first' => 'Nadia',    'last' => 'Hassan',  'gender' => 'Female'],
            ['first' => 'Khalid',   'last' => 'Mehmood', 'gender' => 'Male'],
            ['first' => 'Rabia',    'last' => 'Basri',   'gender' => 'Female'],
            ['first' => 'Usman',    'last' => 'Ghani',   'gender' => 'Male'],
            ['first' => 'Sadia',    'last' => 'Javed',   'gender' => 'Female'],
            ['first' => 'Shahid',   'last' => 'Afridi',  'gender' => 'Male'],
            ['first' => 'Ayesha',   'last' => 'Siddiqui','gender' => 'Female'],
            ['first' => 'Zahid',    'last' => 'Hameed',  'gender' => 'Male'],
            ['first' => 'Tahira',   'last' => 'Yasmin',  'gender' => 'Female'],
            ['first' => 'Rashid',   'last' => 'Minhas',  'gender' => 'Male'],
            ['first' => 'Samina',   'last' => 'Rafiq',   'gender' => 'Female'],
            ['first' => 'Tariq',    'last' => 'Aziz',    'gender' => 'Male'],
            ['first' => 'Noreen',   'last' => 'Bano',    'gender' => 'Female'],
            ['first' => 'Faisal',   'last' => 'Qureshi', 'gender' => 'Male'],
            ['first' => 'Humaira',  'last' => 'Khalid',  'gender' => 'Female'],
            ['first' => 'Javed',    'last' => 'Iqbal',   'gender' => 'Male'],
        ];

        $qualificationsList = [
            'B.Ed', 'M.Ed', 'M.A. English', 'M.Sc. Mathematics',
            'M.Sc. Physics', 'M.Sc. Chemistry', 'B.Sc.', 'M.A. Urdu', 'M.A. Islamiyat',
        ];

        $staffData = [];

        for ($i = 0; $i < 21; $i++) {
            $basicSalary = $this->faker->numberBetween(35000, 65000);

            $staffData[] = [
                'first_name'                => $teacherNames[$i]['first'],
                'last_name'                 => $teacherNames[$i]['last'],
                'father_name'               => $this->fatherNames[array_rand($this->fatherNames)],
                'gender'                    => $teacherNames[$i]['gender'],
                'date_of_birth'             => $this->faker->dateTimeBetween('-45 years', '-25 years')->format('Y-m-d'),
                'cnic'                      => $this->faker->numerify('35202-#######-#'),
                'nationality'               => 'Pakistani',
                'religion'                  => 'Islam',
                'email'                     => strtolower($teacherNames[$i]['first'] . '.' . $teacherNames[$i]['last'] . '@school.edu.pk'),
                'phone'                     => '03' . $this->faker->numerify('##-#######'),
                'mobile'                    => '03' . $this->faker->numerify('##-#######'),
                'emergency_contact_name'    => $this->faker->name,
                'emergency_contact_relation'=> 'Spouse',
                'emergency_contact_phone'   => '03' . $this->faker->numerify('##-#######'),
                'present_address'           => $this->faker->streetAddress . ', ' . $this->cities[array_rand($this->cities)],
                'permanent_address'         => $this->faker->streetAddress . ', ' . $this->cities[array_rand($this->cities)],
                'city'                      => $this->cities[array_rand($this->cities)],
                'state'                     => 'Punjab',
                'postal_code'               => $this->faker->numerify('#####'),
                'country'                   => 'Pakistan',
                'employee_id'               => 'EMP-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'designation'               => in_array($teacherSpecializations[$i], ['Physics', 'Chemistry', 'Biology', 'Mathematics']) ? 'Senior Teacher' : 'Subject Teacher',
                'department'                => 'Academics',
                'employment_type'           => 'full_time',
                'joining_date'              => $this->faker->dateTimeBetween('-8 years', '-1 year')->format('Y-m-d'),
                'qualification'             => $qualificationsList[array_rand($qualificationsList)],
                'experience'                => $this->faker->numberBetween(2, 20) . ' years teaching experience',
                'specialization'            => $teacherSpecializations[$i],
                'basic_salary'              => $basicSalary,
                'salary_type'               => 'monthly',
                'daily_rate'                => round($basicSalary / 22, 2),
                'allowances'                => $this->faker->numberBetween(3000, 10000),
                'medical_allowance'         => 5000,
                'transport_allowance'       => 3000,
                'pf_percentage'             => 5,
                'attendance_required'       => true,
                'bank_name'                 => 'Habib Bank Ltd',
                'bank_account_number'       => $this->faker->numerify('##########'),
                'bank_iban'                 => 'PK36HABB' . $this->faker->numerify('############'),
                'category_id'               => 1,
                'is_active'                 => true,
                'max_periods_per_day'       => 7,
                'max_periods_per_week'      => 35,
                'is_teacher'                => true,
                'created_at'                => now(),
                'updated_at'                => now(),
            ];
        }

        // Principal
        $staffData[] = [
            'first_name'                => 'Abdul',
            'last_name'                 => 'Rehman',
            'father_name'               => 'Muhammad Latif',
            'gender'                    => 'Male',
            'date_of_birth'             => '1970-03-15',
            'cnic'                      => '35202-1234567-1',
            'nationality'               => 'Pakistani',
            'religion'                  => 'Islam',
            'email'                     => 'principal@school.edu.pk',
            'phone'                     => '0300-1234567',
            'mobile'                    => '0300-1234567',
            'emergency_contact_name'    => 'Mrs. Rehman',
            'emergency_contact_relation'=> 'Wife',
            'emergency_contact_phone'   => '0300-1111111',
            'present_address'           => '123 Main Road, Lahore',
            'permanent_address'         => '123 Main Road, Lahore',
            'city'                      => 'Lahore',
            'state'                     => 'Punjab',
            'postal_code'               => '54000',
            'country'                   => 'Pakistan',
            'employee_id'               => 'EMP-022',
            'designation'               => 'Principal',
            'department'                => 'Administration',
            'employment_type'           => 'full_time',
            'joining_date'              => '2010-04-01',
            'qualification'             => 'M.Phil Education',
            'experience'                => '25 years in education',
            'specialization'            => null,
            'basic_salary'              => 150000,
            'salary_type'               => 'monthly',
            'daily_rate'                => 0,
            'allowances'                => 25000,
            'medical_allowance'         => 10000,
            'transport_allowance'       => 8000,
            'pf_percentage'             => 5,
            'attendance_required'       => true,
            'bank_name'                 => 'Habib Bank Ltd',
            'bank_account_number'       => '1234567890',
            'bank_iban'                 => 'PK36HABB1234567890123456',
            'category_id'               => 2,
            'is_active'                 => true,
            'max_periods_per_day'       => 0,
            'max_periods_per_week'      => 0,
            'is_teacher'                => false,
            'created_at'                => now(),
            'updated_at'                => now(),
        ];

        // Admin Officer
        $staffData[] = [
            'first_name'                => 'Nasreen',
            'last_name'                 => 'Akhtar',
            'father_name'               => 'Ghulam Mustafa',
            'gender'                    => 'Female',
            'date_of_birth'             => '1975-08-20',
            'cnic'                      => '35202-7654321-2',
            'nationality'               => 'Pakistani',
            'religion'                  => 'Islam',
            'email'                     => 'admin@school.edu.pk',
            'phone'                     => '0300-7654321',
            'mobile'                    => '0300-7654321',
            'emergency_contact_name'    => 'Mr. Akhtar',
            'emergency_contact_relation'=> 'Husband',
            'emergency_contact_phone'   => '0300-2222222',
            'present_address'           => '456 Model Town, Lahore',
            'permanent_address'         => '456 Model Town, Lahore',
            'city'                      => 'Lahore',
            'state'                     => 'Punjab',
            'postal_code'               => '54000',
            'country'                   => 'Pakistan',
            'employee_id'               => 'EMP-023',
            'designation'               => 'Admin Officer',
            'department'                => 'Administration',
            'employment_type'           => 'full_time',
            'joining_date'              => '2015-04-01',
            'qualification'             => 'MBA',
            'experience'                => '15 years in administration',
            'specialization'            => null,
            'basic_salary'              => 65000,
            'salary_type'               => 'monthly',
            'daily_rate'                => 0,
            'allowances'                => 10000,
            'medical_allowance'         => 5000,
            'transport_allowance'       => 4000,
            'pf_percentage'             => 5,
            'attendance_required'       => true,
            'bank_name'                 => 'Meezan Bank',
            'bank_account_number'       => '0987654321',
            'bank_iban'                 => 'PK36MEZN0987654321098765',
            'category_id'               => 2,
            'is_active'                 => true,
            'max_periods_per_day'       => 0,
            'max_periods_per_week'      => 0,
            'is_teacher'                => false,
            'created_at'                => now(),
            'updated_at'                => now(),
        ];

        DB::table('staff')->insert($staffData);
        $staffDb = DB::table('staff')->get();
        $teacherIds = DB::table('staff')->where('is_teacher', true)->pluck('id')->toArray();
        $staffIds = DB::table('staff')->pluck('id')->toArray();
        $this->command->info("✅ " . count($staffData) . " staff created (" . count($teacherIds) . " teachers + 2 admin)");

        // ============================================================
        // 12. TEACHER-SUBJECT ASSIGNMENTS
        // ============================================================
        $this->command->info("\n📚 Assigning subjects to teachers...");

        $teacherSubjectData = [];

        foreach ($teacherIds as $index => $teacherId) {
            $specialization = $teacherSpecializations[$index];

            // Primary subject
            if (isset($subjectsByName[$specialization])) {
                $teacherSubjectData[] = [
                    'teacher_id'       => $teacherId,
                    'subject_id'       => $subjectsByName[$specialization]->id,
                    'preference_level' => 1,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }

            // Additional subjects
            $otherSubjects = $subjectsDb->where('name', '!=', $specialization)->random(min(2, $subjectsDb->count() - 1));

            if ($otherSubjects instanceof \stdClass) {
                $otherSubjects = [$otherSubjects];
            }
            if (!is_array($otherSubjects) && !$otherSubjects instanceof \Illuminate\Support\Collection) {
                $otherSubjects = [$otherSubjects];
            }

            foreach ($otherSubjects as $subj) {
                $teacherSubjectData[] = [
                    'teacher_id'       => $teacherId,
                    'subject_id'       => $subj->id,
                    'preference_level' => 2,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }

        DB::table('teacher_subjects')->insert($teacherSubjectData);
        $this->command->info("✅ " . count($teacherSubjectData) . " teacher-subject assignments");

        // ============================================================
        // 13. STUDENTS (12 per section)
        // ============================================================
        $this->command->info("\n👨‍🎓 Creating students (12 per section)...");

        $studentData = [];
        $admissionNum = 1;

        $ageMap = [
            'KG' => 5,  '1' => 6,  '2' => 7,  '3' => 8,  '4' => 9,  '5' => 10,
            '6'  => 11, '7' => 12, '8' => 13, '9' => 14, '10' => 15,
        ];

        foreach ($classSectionsDb as $section) {
            for ($i = 1; $i <= 12; $i++) {
                $gender = $i <= 6 ? 'Male' : 'Female';

                if ($gender == 'Male') {
                    $firstName = $this->maleFirstNames[array_rand($this->maleFirstNames)];
                    $lastName  = $this->maleLastNames[array_rand($this->maleLastNames)];
                } else {
                    $firstName = $this->femaleFirstNames[array_rand($this->femaleFirstNames)];
                    $lastName  = $this->femaleLastNames[array_rand($this->femaleLastNames)];
                }

                $classInfo = $classesDb->where('id', $section->class_id)->first();
                $gradeInfo = $gradesDb->where('id', $classInfo->grade_id)->first();
                $gradeName = $gradeInfo->name;

                $age = $ageMap[$gradeName] ?? 8;
                $dob = date('Y-m-d', strtotime("-{$age} years " . rand(-6, 6) . " months"));

                $fatherName = $this->fatherNames[array_rand($this->fatherNames)];
                $city       = $this->cities[array_rand($this->cities)];

                $fatherOccupation = $this->faker->randomElement([
                    'Businessman', 'Government Servant', 'Doctor', 'Engineer',
                    'Teacher', 'Army Officer', 'Lawyer', 'Banker', 'Shopkeeper', 'Private Job',
                ]);

                $studentData[] = [
                    'first_name'             => $firstName,
                    'middle_name'            => null,
                    'last_name'              => $lastName,
                    'gender'                 => $gender,
                    'date_of_birth'          => $dob,
                    'dob_in_words'           => null,
                    'religion'               => 'Islam',
                    'caste_subcaste'         => null,
                    'blood_group'            => $this->faker->randomElement(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-']),
                    'address'                => $this->faker->streetAddress . ', ' . $city,
                    'phone'                  => '03' . $this->faker->numerify('##-#######'),
                    'email'                  => strtolower($firstName . '.' . $lastName . $admissionNum . '@gmail.com'),
                    'city'                   => $city,
                    'state'                  => 'Punjab',
                    'country'                => 'Pakistan',
                    'extra_note'             => null,
                    'mother_tongue'          => 'Urdu',
                    'birth_place'            => $city,
                    'previous_school_name'   => $gradeName == 'KG' ? null : $this->faker->randomElement(['The Educators', 'Beaconhouse', 'Allied School', 'Dar-e-Arqam', 'LACAS', null]),
                    'previous_school_address'=> null,
                    'previous_class'         => null,
                    'passout_year'           => null,
                    'previous_category'      => null,
                    'admission_date'         => '2024-04-01',
                    'student_type'           => $gradeName == 'KG' ? 'New' : 'Old',
                    'admission_number'       => 'ADM-' . str_pad($admissionNum, 4, '0', STR_PAD_LEFT),
                    'roll_number'            => str_pad($admissionNum, 3, '0', STR_PAD_LEFT),
                    'profile_photo'          => null,
                    'father_name'            => $fatherName,
                    'father_phone'           => '03' . $this->faker->numerify('##-#######'),
                    'father_occupation'      => $fatherOccupation,
                    'mother_name'            => $this->faker->firstName('female') . ' ' . $this->faker->lastName,
                    'mother_phone'           => '03' . $this->faker->numerify('##-#######'),
                    'mother_occupation'      => $this->faker->randomElement(['Housewife', 'Teacher', 'Doctor', 'Housewife', 'Housewife', 'Lecturer']),
                    'parent_id_proof'        => null,
                    'parent_signature'       => null,
                    'assigned_concession'    => $this->faker->randomElement([null, null, null, null, 'Sibling Discount', 'Merit Scholarship']),
                    'status'                 => 'Active',
                    'suspension_start_date'  => null,
                    'suspension_end_date'    => null,
                    'suspension_message'     => null,
                    'class_section_id'       => $section->id,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ];

                $admissionNum++;
            }
        }

        DB::table('students')->insert($studentData);
        $studentsDb = DB::table('students')->get();
        $this->command->info("✅ " . count($studentData) . " students created (12 per section × 26 sections = 312)");

        // Update student counts
        $counts = DB::table('students')
            ->select('class_section_id', DB::raw('count(*) as total'))
            ->groupBy('class_section_id')
            ->get();

        foreach ($counts as $count) {
            DB::table('class_sections')
                ->where('id', $count->class_section_id)
                ->update([
                    'student_count'    => $count->total,
                    'student_strength' => $count->total,
                ]);
        }

        // ============================================================
        // 14. BANKS & FINANCIAL SETUP
        // ============================================================
        $this->command->info("\n🏦 Creating financial setup...");

        $banks = [
            [
                'name'           => 'Habib Bank Ltd',
                'branch_name'    => 'Main Branch',
                'account_title'  => 'School Fee Account',
                'account_number' => '1234-567890-01',
                'iban'           => 'PK36HABB0012345678901234',
                'routing_number' => '123456',
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Meezan Bank',
                'branch_name'    => 'Islamic Banking',
                'account_title'  => 'School Fee',
                'account_number' => '5566-778899-03',
                'iban'           => 'PK36MEZN0012345678909012',
                'routing_number' => '654321',
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];
        DB::table('banks')->insert($banks);

        $discounts = [
            ['name' => 'Sibling Discount 10%', 'type' => 'percentage', 'value' => 10, 'description' => 'For siblings studying in same school', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Merit Scholarship 25%','type' => 'percentage', 'value' => 25, 'description' => 'For top performing students',       'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Need Based 20%',       'type' => 'percentage', 'value' => 20, 'description' => 'Financial assistance',              'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Orphan Support 50%',   'type' => 'percentage', 'value' => 50, 'description' => 'For orphan students',               'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('discounts')->insert($discounts);

        $feeTypes = [
            ['name' => 'Admission Fee',       'description' => 'One-time admission fee',       'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Tuition Fee', 'description' => 'Monthly tuition charges',      'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Exam Fee',            'description' => 'Per term exam fee',            'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lab Fee',             'description' => 'Science/Computer lab charges', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('fee_types')->insert($feeTypes);

        $feeSubmissionTypes = [
            ['name' => 'Admission Fee',              'period' => 'one_time',  'amount' => 8000, 'description' => 'One time at admission',              'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Fee (KG)',           'period' => 'monthly',   'amount' => 2500, 'description' => 'Monthly fee for KG',                 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Fee (Class 1-5)',    'period' => 'monthly',   'amount' => 3000, 'description' => 'Monthly fee for Class 1-5',          'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Fee (Class 6-8)',    'period' => 'monthly',   'amount' => 3500, 'description' => 'Monthly fee for Class 6-8',          'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly Fee (Class 9-10)',   'period' => 'monthly',   'amount' => 4000, 'description' => 'Monthly fee for Class 9-10',         'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Exam Fee',                   'period' => 'quarterly', 'amount' => 1500, 'description' => 'Per term examination fee',           'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science Lab Fee',            'period' => 'annually',  'amount' => 3000, 'description' => 'Annual lab fee for 9-10 Science',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('fee_submission_types')->insert($feeSubmissionTypes);

        $this->command->info("✅ Financial setup complete");

        // ============================================================
        // 15. STUDENT FEE INSTALLMENTS & INVOICES
        // ============================================================
        $this->command->info("\n💰 Creating fee installments and invoices...");

        $gradeFeeMap = [
            'KG' => 2, '1' => 3, '2' => 3, '3' => 3, '4' => 3, '5' => 3,
            '6'  => 4, '7' => 4, '8' => 4, '9' => 5, '10' => 5,
        ];

        $installmentData = [];
        $invoiceData     = [];
        $installmentId   = 1;

        foreach ($studentsDb as $student) {
            $classInfo = $classesDb->where('id', $student->class_section_id)->first();
            if (!$classInfo) continue;

            $gradeInfo = $gradesDb->where('id', $classInfo->grade_id)->first();
            if (!$gradeInfo) continue;

            $streamInfo          = DB::table('streams')->where('id', $classInfo->stream_id)->value('name');
            $feeSubmissionTypeId = $gradeFeeMap[$gradeInfo->name] ?? 2;
            $feeAmount           = DB::table('fee_submission_types')->where('id', $feeSubmissionTypeId)->value('amount');

            for ($month = 1; $month <= 4; $month++) {
                $dueDate     = date('Y-m-d', strtotime("2024-" . (3 + $month) . "-10"));
                $status      = $month <= 3 ? 'paid' : 'pending';
                $paidAmount  = $status == 'paid' ? $feeAmount : 0;
                $paymentDate = $status == 'paid' ? date('Y-m-d', strtotime($dueDate . ' -2 days')) : null;
                $paidAt      = $status == 'paid' ? date('Y-m-d H:i:s', strtotime($dueDate . ' -2 days 10:30:00')) : null;

                $installmentData[] = [
                    'student_id'             => $student->id,
                    'fee_submission_type_id' => $feeSubmissionTypeId,
                    'installment_number'     => $month,
                    'amount'                 => $feeAmount,
                    'due_date'               => $dueDate,
                    'status'                 => $status,
                    'paid_amount'            => $paidAmount,
                    'payment_date'           => $paymentDate,
                    'receipt_number'         => $status == 'paid' ? 'RCP-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT) : null,
                    'remarks'                => 'Monthly Fee - ' . date('F Y', strtotime($dueDate)),
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ];

                $invoiceData[] = [
                    'invoice_number'            => 'INV-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT),
                    'student_id'                => $student->id,
                    'bank_id'                   => rand(1, 2),
                    'student_fee_submission_id' => $installmentId,
                    'amount'                    => $feeAmount,
                    'discount_amount'           => 0,
                    'due_date'                  => $dueDate,
                    'status'                    => $status == 'paid' ? 'paid' : 'pending',
                    'challan_file'              => null,
                    'payment_proof_file'        => null,
                    'payment_remarks'           => null,
                    'paid_at'                   => $paidAt,
                    'approved_by'               => null,
                    'discount_id'               => null,
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ];

                $installmentId++;
            }

            // Science Lab Fee for 9-10 Science
            if (($gradeInfo->name == '9' || $gradeInfo->name == '10') && $streamInfo == 'Science') {
                $installmentData[] = [
                    'student_id'             => $student->id,
                    'fee_submission_type_id' => 7,
                    'installment_number'     => 1,
                    'amount'                 => 3000,
                    'due_date'               => '2024-04-15',
                    'status'                 => 'paid',
                    'paid_amount'            => 3000,
                    'payment_date'           => '2024-04-13',
                    'receipt_number'         => 'RCP-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT),
                    'remarks'                => 'Science Lab Fee - Annual',
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ];

                $invoiceData[] = [
                    'invoice_number'            => 'INV-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT),
                    'student_id'                => $student->id,
                    'bank_id'                   => 1,
                    'student_fee_submission_id' => $installmentId,
                    'amount'                    => 3000,
                    'discount_amount'           => 0,
                    'due_date'                  => '2024-04-15',
                    'status'                    => 'paid',
                    'challan_file'              => null,
                    'payment_proof_file'        => null,
                    'payment_remarks'           => null,
                    'paid_at'                   => '2024-04-13 10:30:00',
                    'approved_by'               => null,
                    'discount_id'               => null,
                    'created_at'                => now(),
                    'updated_at'                => now(),
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

        // ============================================================
        // 16. SCHOOL TIMING & TIME SLOTS
        // ============================================================
        $this->command->info("\n⏰ Creating school timings and time slots...");

        DB::table('school_timings')->insert([
            'session_name'     => 'Regular 2024-2025',
            'session_start'    => '2024-04-01',
            'session_end'      => '2025-03-31',
            'school_start'     => '08:00:00',
            'school_end'       => '14:30:00',
            'period_duration'  => 45,
            'breaks'           => json_encode([
                ['label' => 'Short Break', 'start' => '09:40', 'end' => '09:55'],
                ['label' => 'Lunch Break', 'start' => '11:25', 'end' => '11:55'],
            ]),
            'has_activity'     => true,
            'activity_label'   => 'Assembly/Dua',
            'activity_start'   => '08:00:00',
            'activity_end'     => '08:10:00',
            'is_active'        => true,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
        $schoolTimingId = DB::getPdo()->lastInsertId();

        $timeSlotsList = [
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 1,  'label' => 'Assembly',   'start_time' => '08:00', 'end_time' => '08:10', 'type' => 'activity', 'period_number' => null, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 2,  'label' => 'Period 1',   'start_time' => '08:10', 'end_time' => '08:55', 'type' => 'period',   'period_number' => 1,    'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 3,  'label' => 'Period 2',   'start_time' => '08:55', 'end_time' => '09:40', 'type' => 'period',   'period_number' => 2,    'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 4,  'label' => 'Short Break','start_time' => '09:40', 'end_time' => '09:55', 'type' => 'break',    'period_number' => null, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 5,  'label' => 'Period 3',   'start_time' => '09:55', 'end_time' => '10:40', 'type' => 'period',   'period_number' => 3,    'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 6,  'label' => 'Period 4',   'start_time' => '10:40', 'end_time' => '11:25', 'type' => 'period',   'period_number' => 4,    'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 7,  'label' => 'Lunch Break','start_time' => '11:25', 'end_time' => '11:55', 'type' => 'break',    'period_number' => null, 'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 8,  'label' => 'Period 5',   'start_time' => '11:55', 'end_time' => '12:40', 'type' => 'period',   'period_number' => 5,    'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 9,  'label' => 'Period 6',   'start_time' => '12:40', 'end_time' => '13:25', 'type' => 'period',   'period_number' => 6,    'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 10, 'label' => 'Period 7',   'start_time' => '13:25', 'end_time' => '14:10', 'type' => 'period',   'period_number' => 7,    'is_active' => true],
            ['school_timing_id' => $schoolTimingId, 'sort_order' => 11, 'label' => 'Period 8',   'start_time' => '14:10', 'end_time' => '14:30', 'type' => 'period',   'period_number' => 8,    'is_active' => true],
        ];

        foreach ($timeSlotsList as $data) {
            DB::table('time_slots')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $timeSlotsDb = DB::table('time_slots')->where('type', 'period')->get();
        $this->command->info("✅ School timing and " . count($timeSlotsList) . " time slots created");

        // ============================================================
        // 17. TEACHER AVAILABILITIES
        // ============================================================
        $this->command->info("\n📅 Creating teacher availabilities...");

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $availabilityData = [];

        foreach ($teacherIds as $teacherId) {
            foreach ($days as $day) {
                foreach ($timeSlotsDb as $slot) {
                    $availabilityData[] = [
                        'teacher_id'   => $teacherId,
                        'day_of_week'  => $day,
                        'time_slot_id' => $slot->id,
                        'is_available' => rand(1, 100) <= 90,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
            }
        }

        foreach (array_chunk($availabilityData, 500) as $chunk) {
            DB::table('teacher_availabilities')->insert($chunk);
        }
        $this->command->info("✅ " . count($availabilityData) . " teacher availability records");

        // ============================================================
        // 18. TIMETABLE ENTRIES
        // ============================================================
        $this->command->info("\n📅 Creating timetable entries...");

        $rooms           = DB::table('rooms')->where('type', 'classroom')->get();
        $timetableData   = [];
        $usedTeacherSlots = [];

        foreach ($classSectionsDb as $section) {
            $sectionSubjects = DB::table('subject_assignments')
                ->where('class_section_id', $section->id)
                ->get();

            $room = $rooms->random();

            foreach ($days as $day) {
                $periodSlots  = $timeSlotsDb->sortBy('sort_order');
                $subjectIndex = 0;

                foreach ($periodSlots as $slot) {
                    if ($subjectIndex >= $sectionSubjects->count()) break;

                    $assignment = $sectionSubjects[$subjectIndex];

                    $teacher = DB::table('teacher_subjects')
                        ->where('subject_id', $assignment->subject_id)
                        ->inRandomOrder()
                        ->first();

                    if ($teacher) {
                        $key = $teacher->teacher_id . '-' . $day . '-' . $slot->id;

                        if (!in_array($key, $usedTeacherSlots)) {
                            $usedTeacherSlots[] = $key;

                            $timetableData[] = [
                                'class_section_id' => $section->id,
                                'subject_id'       => $assignment->subject_id,
                                'teacher_id'       => $teacher->teacher_id,
                                'room_id'          => $room->id,
                                'time_slot_id'     => $slot->id,
                                'day_of_week'      => $day,
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ];
                        }
                    }

                    $subjectIndex++;
                }
            }
        }

        foreach (array_chunk($timetableData, 100) as $chunk) {
            DB::table('timetable_entries')->insertOrIgnore($chunk);
        }

        $this->command->info("✅ " . count($timetableData) . " timetable entries created");

        // ============================================================
        // 19. SALARY TEMPLATES
        // ============================================================
        $this->command->info("\n💰 Creating salary templates...");

        $salaryTemplates = [
            ['name' => 'Teacher Basic',  'description' => 'Basic teacher salary',   'basic_salary' => 45000, 'allowances' => 8000,  'medical_allowance' => 5000, 'transport_allowance' => 3000, 'pf_percentage' => 5, 'tax_percentage' => 0,   'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senior Teacher', 'description' => 'Senior teacher salary',  'basic_salary' => 65000, 'allowances' => 12000, 'medical_allowance' => 7000, 'transport_allowance' => 5000, 'pf_percentage' => 5, 'tax_percentage' => 2.5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin Staff',    'description' => 'Admin staff salary',     'basic_salary' => 55000, 'allowances' => 10000, 'medical_allowance' => 6000, 'transport_allowance' => 4000, 'pf_percentage' => 5, 'tax_percentage' => 0,   'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('salary_templates')->insert($salaryTemplates);
        $this->command->info("✅ Salary templates created");

        // ============================================================
        // 20. SALARIES
        // ============================================================
        $this->command->info("\n💰 Creating salary records...");

        $months     = ['2024-04', '2024-05', '2024-06', '2024-07'];
        $salaryData = [];

        foreach ($staffIds as $staffId) {
            $staff = DB::table('staff')->where('id', $staffId)->first();

            foreach ($months as $month) {
                $salaryData[] = [
                    'staff_id'          => $staffId,
                    'month'             => $month,
                    'total_working_days'=> 22,
                    'days_present'      => 21,
                    'days_absent'       => 1,
                    'days_late'         => 0,
                    'basic_salary'      => $staff->basic_salary,
                    'daily_rate'        => round($staff->basic_salary / 22, 2),
                    'attendance_bonus'  => 500,
                    'overtime_pay'      => 0,
                    'allowances'        => $staff->allowances ?? 0,
                    'bonus'             => 0,
                    'commission'        => 0,
                    'deductions'        => ($staff->basic_salary ?? 0) * 0.05,
                    'penalties'         => 0,
                    'leave_deductions'  => 0,
                    'tax'               => ($staff->basic_salary ?? 0) > 50000 ? (($staff->basic_salary ?? 0) - 50000) * 0.025 : 0,
                    'pf_employee'       => ($staff->basic_salary ?? 0) * 0.05,
                    'pf_employer'       => ($staff->basic_salary ?? 0) * 0.075,
                    'net_salary'        => ($staff->basic_salary ?? 0) + ($staff->allowances ?? 0) + 500 - (($staff->basic_salary ?? 0) * 0.05),
                    'payment_date'      => $month . '-05',
                    'payment_method'    => 'bank',
                    'payment_status'    => 'paid',
                    'transaction_ref'   => 'TXN-' . str_pad($staffId, 6, '0', STR_PAD_LEFT),
                    'bank_name'         => null,
                    'account_number'    => null,
                    'approved_by'       => null,
                    'approved_at'       => null,
                    'remarks'           => null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }

        foreach (array_chunk($salaryData, 100) as $chunk) {
            DB::table('salaries')->insert($chunk);
        }
        $this->command->info("✅ " . count($salaryData) . " salary records created");

        // ============================================================
        // 21. EXAMS
        // ============================================================
        $this->command->info("\n📝 Creating exams...");

        DB::table('exam_types')->insert([
            ['name' => 'Mid Term',   'code' => 'MID',   'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Final Term', 'code' => 'FINAL', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('exam_groups')->insert([
            ['name' => 'Pre-Primary',       'code' => 'PRE',    'description' => 'KG',          'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Primary',           'code' => 'PRI',    'description' => 'Class 1-5',   'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Middle',            'code' => 'MID',    'description' => 'Class 6-8',   'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secondary Science', 'code' => 'SEC-SC', 'description' => '9-10 Science','sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secondary Arts',    'code' => 'SEC-AR', 'description' => '9-10 Arts',   'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $examTypeIds = DB::table('exam_types')->pluck('id')->toArray();
        $examData    = [];

        foreach ($classSectionsDb as $section) {
            $classInfo  = $classesDb->where('id', $section->class_id)->first();
            $gradeInfo  = $gradesDb->where('id', $classInfo->grade_id)->first();
            $streamInfo = DB::table('streams')->where('id', $classInfo->stream_id)->value('name');
            $gradeName  = $gradeInfo->name;

            if ($gradeName == 'KG') $group = 1;
            elseif (in_array($gradeName, ['1', '2', '3', '4', '5'])) $group = 2;
            elseif (in_array($gradeName, ['6', '7', '8'])) $group = 3;
            elseif ($streamInfo == 'Science') $group = 4;
            else $group = 5;

            $examDates = [
                'Mid Term'   => ['2024-09-15', '2024-09-25'],
                'Final Term' => ['2024-12-10', '2024-12-20'],
            ];

            foreach ($examDates as $name => $dateRange) {
                $examData[] = [
                    'exam_type_id'     => $name == 'Mid Term' ? $examTypeIds[0] : $examTypeIds[1],
                    'exam_group_id'    => $group,
                    'class_section_id' => $section->id,
                    'name'             => $name . ' ' . date('Y'),
                    'start_date'       => $dateRange[0],
                    'end_date'         => $dateRange[1],
                    'description'      => $name . ' Examination for ' . $section->section_name,
                    'is_published'     => true,
                    'exam_center'      => null,
                    'time_table'       => null,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }
        DB::table('exams')->insert($examData);
        $examsDb = DB::table('exams')->get();
        $this->command->info("✅ " . count($examData) . " exams created");

        // ============================================================
        // 22. EXAM MARKS
        // ============================================================
        $this->command->info("\n📊 Creating exam marks...");

        $marksData = [];

        foreach ($examsDb as $exam) {
            $students        = DB::table('students')->where('class_section_id', $exam->class_section_id)->get();
            $sectionSubjects = DB::table('subject_assignments')->where('class_section_id', $exam->class_section_id)->get();

            foreach ($students as $student) {
                foreach ($sectionSubjects as $assignment) {
                    $maxMarks      = in_array($assignment->subject_id, [7, 9, 14, 15, 16, 19, 20, 21]) ? 75 : 100;
                    $passingMarks  = round($maxMarks * 0.40);
                    $marksObtained = $this->faker->numberBetween(round($maxMarks * 0.25), $maxMarks);

                    $marksData[] = [
                        'exam_id'        => $exam->id,
                        'student_id'     => $student->id,
                        'subject_id'     => $assignment->subject_id,
                        'marks_obtained' => $marksObtained,
                        'max_marks'      => $maxMarks,
                        'passing_marks'  => $passingMarks,
                        'remarks'        => $marksObtained >= $passingMarks ? 'Pass' : 'Fail - Needs Improvement',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
            }
        }

        foreach (array_chunk($marksData, 500) as $chunk) {
            DB::table('exam_marks')->insert($chunk);
        }
        $this->command->info("✅ " . count($marksData) . " exam marks created");

        // ============================================================
        // 23. GRADE SCALES
        // ============================================================
        DB::table('grade_scales')->insert([
            'name'       => 'Pakistan Standard',
            'grades'     => json_encode([
                ['min' => 90, 'max' => 100, 'grade' => 'A+', 'remarks' => 'Outstanding'],
                ['min' => 80, 'max' => 89,  'grade' => 'A',  'remarks' => 'Excellent'],
                ['min' => 70, 'max' => 79,  'grade' => 'B',  'remarks' => 'Very Good'],
                ['min' => 60, 'max' => 69,  'grade' => 'C',  'remarks' => 'Good'],
                ['min' => 50, 'max' => 59,  'grade' => 'D',  'remarks' => 'Satisfactory'],
                ['min' => 40, 'max' => 49,  'grade' => 'E',  'remarks' => 'Pass'],
                ['min' => 0,  'max' => 39,  'grade' => 'F',  'remarks' => 'Fail'],
            ]),
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================================================
        // 24. EXAM RESULTS
        // ============================================================
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
                $totalMax      = $marks->sum('max_marks');
                $percentage    = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;

                $grade = 'F';
                if ($percentage >= 90) $grade = 'A+';
                elseif ($percentage >= 80) $grade = 'A';
                elseif ($percentage >= 70) $grade = 'B';
                elseif ($percentage >= 60) $grade = 'C';
                elseif ($percentage >= 50) $grade = 'D';
                elseif ($percentage >= 40) $grade = 'E';

                $resultData[] = [
                    'exam_id'         => $exam->id,
                    'student_id'      => $student->id,
                    'class_section_id'=> $exam->class_section_id,
                    'total_marks'     => $totalObtained,
                    'total_max_marks' => $totalMax,
                    'percentage'      => $percentage,
                    'grade'           => $grade,
                    'remarks'         => $percentage >= 40 ? 'Pass' : 'Fail',
                    'rank_in_class'   => 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        foreach (array_chunk($resultData, 500) as $chunk) {
            DB::table('exam_results')->insert($chunk);
        }

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

        // ============================================================
        // 25. STAFF ATTENDANCE
        // ============================================================
        $this->command->info("\n📋 Creating staff attendance records...");

        $staffAttendanceData = [];
        $startDate           = now()->subDays(30);

        for ($date = clone $startDate; $date->lte(now()); $date->addDay()) {
            if ($date->isWeekend()) continue;

            foreach ($staffIds as $staffId) {
                $rand   = rand(1, 100);
                $status = $rand <= 85 ? 'present' : ($rand <= 92 ? 'late' : ($rand <= 97 ? 'half_day' : 'absent'));

                $staffAttendanceData[] = [
                    'staff_id'     => $staffId,
                    'date'         => $date->format('Y-m-d'),
                    'status'       => $status,
                    'check_in'     => in_array($status, ['present', 'late'])
                        ? $this->faker->dateTimeBetween($date->format('Y-m-d 07:45:00'), $date->format('Y-m-d 08:30:00'))->format('H:i:s')
                        : null,
                    'check_out'    => in_array($status, ['present', 'late'])
                        ? $this->faker->dateTimeBetween($date->format('Y-m-d 14:00:00'), $date->format('Y-m-d 15:00:00'))->format('H:i:s')
                        : null,
                    'late_minutes' => $status == 'late' ? rand(5, 30) : 0,
                    'is_approved'  => true,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        }

        foreach (array_chunk($staffAttendanceData, 500) as $chunk) {
            DB::table('staff_attendances')->insert($chunk);
        }
        $this->command->info("✅ " . count($staffAttendanceData) . " staff attendance records");

        // ============================================================
        // 26. STUDENT ATTENDANCE
        // ============================================================
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

                $rand   = rand(1, 100);
                $status = $rand <= 80 ? 'present' : ($rand <= 90 ? 'late' : ($rand <= 95 ? 'half_day' : 'absent'));

                $studentAttendanceData[] = [
                    'student_id'       => $student->id,
                    'class_section_id' => $student->class_section_id,
                    'subject_id'       => $entry ? $entry->subject_id : $subjectsDb->first()->id,
                    'teacher_id'       => $entry ? $entry->teacher_id : ($teacherIds[0] ?? 1),
                    'date'             => $date->format('Y-m-d'),
                    'status'           => $status,
                    'remarks'          => null,
                    'half_day_type'    => $status == 'half_day' ? $this->faker->randomElement(['morning', 'afternoon']) : null,
                    'half_day_reason'  => $status == 'half_day' ? 'Sick' : null,
                    'late_minutes'     => $status == 'late' ? rand(5, 30) : 0,
                    'late_reason'      => $status == 'late' ? 'Traffic' : null,
                    'check_in'         => in_array($status, ['present', 'late'])
                        ? $this->faker->dateTimeBetween($date->format('Y-m-d 07:50:00'), $date->format('Y-m-d 08:20:00'))->format('H:i:s')
                        : null,
                    'check_out'        => in_array($status, ['present', 'late']) ? '14:30:00' : null,
                    'is_approved'      => true,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }

        foreach (array_chunk($studentAttendanceData, 500) as $chunk) {
            DB::table('student_attendance')->insert($chunk);
        }
        $this->command->info("✅ " . count($studentAttendanceData) . " student attendance records");

        // ============================================================
        // 27. TRANSPORT DATA
        // ============================================================
        $this->command->info("\n🚌 Creating transport data...");

        // 27a. Drivers
        $driverData = [];
        $driverNames = [
            ['name' => 'Muhammad Aslam',  'cnic' => '35201-1234567-1', 'license' => 'L-1001'],
            ['name' => 'Abdul Rashid',    'cnic' => '35202-2345678-2', 'license' => 'L-1002'],
            ['name' => 'Muhammad Yousaf', 'cnic' => '35203-3456789-3', 'license' => 'L-1003'],
            ['name' => 'Ghulam Nabi',     'cnic' => '35204-4567890-4', 'license' => 'L-1004'],
            ['name' => 'Muhammad Arif',   'cnic' => '35205-5678901-5', 'license' => 'L-1005'],
            ['name' => 'Rana Shahbaz',    'cnic' => '35206-6789012-6', 'license' => 'L-1006'],
            ['name' => 'Muhammad Naeem',  'cnic' => '35207-7890123-7', 'license' => 'L-1007'],
            ['name' => 'Khalid Mahmood',  'cnic' => '35208-8901234-8', 'license' => 'L-1008'],
        ];

        foreach ($driverNames as $index => $driver) {
            $driverData[] = [
                'name'              => $driver['name'],
                'cnic'              => $driver['cnic'],
                'license_number'    => $driver['license'],
                'license_expiry'    => date('Y-m-d', strtotime('+2 years ' . rand(-6, 6) . ' months')),
                'phone'             => '03' . rand(0, 9) . rand(0, 9) . '-' . rand(1000000, 9999999),
                'emergency_contact' => '03' . rand(0, 9) . rand(0, 9) . '-' . rand(1000000, 9999999),
                'address'           => $this->faker->streetAddress . ', ' . $this->cities[array_rand($this->cities)],
                'photo'             => null,
                'license_document'  => null,
                'cnic_document'     => null,
                'joining_date'      => date('Y-m-d', strtotime('-2 years +' . rand(0, 365) . ' days')),
                'is_active'         => true,
                'remarks'           => $index % 3 == 0 ? 'Experienced driver with 5+ years' : null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }
        DB::table('drivers')->insert($driverData);
        $driversDb = DB::table('drivers')->get();
        $this->command->info("✅ " . count($driverData) . " drivers created");

        // 27b. Vehicles
        $vehicleData   = [];
        $vehicleTypes  = ['bus', 'van', 'coaster', 'bus', 'bus', 'van', 'coaster', 'bus'];
        $manufacturers = ['Toyota', 'Hino', 'Isuzu', 'Mitsubishi', 'Nissan', 'Hyundai'];
        $models        = ['Coaster', 'Hiace', 'Super Deluxe', 'Hino AK', 'Isuzu Journey', 'Mitsubishi Rosa'];

        foreach ($driversDb as $index => $driver) {
            $capacity = $vehicleTypes[$index] == 'bus' ? rand(40, 60) : ($vehicleTypes[$index] == 'coaster' ? rand(25, 35) : rand(12, 18));

            $vehicleData[] = [
                'vehicle_number'        => 'V-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '-' . strtoupper($this->faker->lexify('??')),
                'type'                  => $vehicleTypes[$index % count($vehicleTypes)],
                'model'                 => $models[array_rand($models)],
                'manufacturer'          => $manufacturers[array_rand($manufacturers)],
                'manufacture_year'      => rand(2015, 2023),
                'seating_capacity'      => $capacity,
                'registration_number'   => 'REG-' . strtoupper($this->faker->lexify('???')) . '-' . rand(100, 999),
                'registration_expiry'   => date('Y-m-d', strtotime('+1 year +' . rand(1, 365) . ' days')),
                'insurance_expiry'      => date('Y-m-d', strtotime('+1 year +' . rand(1, 365) . ' days')),
                'fitness_expiry'        => date('Y-m-d', strtotime('+6 months +' . rand(1, 180) . ' days')),
                'driver_id'             => $driver->id,
                'status'                => $index % 5 == 0 ? 'maintenance' : ($index % 7 == 0 ? 'inactive' : 'active'),
                'photo'                 => null,
                'registration_document' => null,
                'insurance_document'    => null,
                'fitness_document'      => null,
                'notes'                 => $index % 4 == 0 ? 'Regular maintenance scheduled' : null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ];
        }
        DB::table('vehicles')->insert($vehicleData);
        $vehiclesDb = DB::table('vehicles')->get();
        $this->command->info("✅ " . count($vehicleData) . " vehicles created");

        // 27c. Transport Routes
        $routeData  = [];
        $routeNames = [
            'Gulshan Route', 'Defence Route', 'Johar Town Route', 'Model Town Route',
            'Valencia Route', 'Bahria Town Route', 'DHA Route', 'Cantt Route',
            'Garden Town Route', 'Samanabad Route', 'Iqbal Town Route', 'Green Town Route',
        ];

        $citiesList = array_keys($this->cityCoordinates);

        foreach ($routeNames as $index => $name) {
            $city       = $citiesList[array_rand($citiesList)];
            $startPoint = $this->areaNames[array_rand($this->areaNames)];
            $endPoint   = $this->areaNames[array_rand($this->areaNames)];

            while ($startPoint == $endPoint) {
                $endPoint = $this->areaNames[array_rand($this->areaNames)];
            }

            $routeData[] = [
                'name'                   => $name,
                'code'                   => 'R-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'start_point'            => $startPoint . ', ' . $city,
                'end_point'              => $endPoint . ', ' . $city,
                'distance_km'            => rand(5, 25) + rand(0, 99) / 100,
                'estimated_time_minutes' => rand(20, 60),
                'vehicle_id'             => $vehiclesDb[$index % $vehiclesDb->count()]->id,
                'driver_id'              => $driversDb[$index % $driversDb->count()]->id,
                'is_active'              => true,
                'description'            => 'Route connecting ' . $startPoint . ' to ' . $endPoint,
                'created_at'             => now(),
                'updated_at'             => now(),
            ];
        }
        DB::table('transport_routes')->insert($routeData);
        $routesDb = DB::table('transport_routes')->get();
        $this->command->info("✅ " . count($routeData) . " transport routes created");

        // 27d. Route Stops
        $stopData  = [];
        $stopNames = [
            'Main Stop', 'Garden Stop', 'Market Stop', 'Hospital Stop',
            'Park Stop', 'Station Stop', 'Chowk Stop', 'Roundabout Stop',
            'Mosque Stop', 'School Stop', 'Bank Stop', 'Mall Stop',
        ];

        foreach ($routesDb as $route) {
            $numStops = rand(4, 8);
            $city     = explode(',', $route->start_point)[1] ?? 'Lahore';
            $city     = trim($city);

            for ($i = 0; $i < $numStops; $i++) {
                $area   = $this->areaNames[array_rand($this->areaNames)];
                $coords = $this->cityCoordinates[$city] ?? ['lat' => 31.5204, 'lng' => 74.3587];

                $stopData[] = [
                    'route_id'    => $route->id,
                    'stop_name'   => $stopNames[array_rand($stopNames)] . ' - ' . $area,
                    'stop_order'  => $i + 1,
                    'pickup_time' => date('H:i:s', strtotime('07:' . (30 + $i * 5) . ':00')),
                    'drop_time'   => date('H:i:s', strtotime('14:' . (30 + $i * 3) . ':00')),
                    'latitude'    => $coords['lat'] + (rand(-100, 100) / 1000),
                    'longitude'   => $coords['lng'] + (rand(-100, 100) / 1000),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }
        DB::table('route_stops')->insert($stopData);
        $stopsDb = DB::table('route_stops')->get();
        $this->command->info("✅ " . count($stopData) . " route stops created");

        // 27e. Transport Fee Types
        $transportFeeData = [];
        $periods          = ['monthly', 'quarterly', 'annually'];

        foreach ($routesDb as $index => $route) {
            $baseAmount = rand(800, 2000) + rand(0, 50);
            $transportFeeData[] = [
                'name'        => 'Transport Fee - ' . $route->name,
                'route_id'    => $route->id,
                'period'      => $periods[$index % 3],
                'amount'      => $periods[$index % 3] == 'monthly' ? $baseAmount : ($periods[$index % 3] == 'quarterly' ? $baseAmount * 3 : $baseAmount * 12),
                'description' => 'Transport fee for ' . $route->name,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
        DB::table('transport_fee_types')->insert($transportFeeData);
        $transportFeeTypesDb = DB::table('transport_fee_types')->get();
        $this->command->info("✅ " . count($transportFeeData) . " transport fee types created");

        // 27f. Student Transport Assignments
        $studentTransportData = [];
        $studentsList         = DB::table('students')->inRandomOrder()->take(100)->get();

        foreach ($studentsList as $student) {
            $route   = $routesDb->random();
            $stop    = DB::table('route_stops')->where('route_id', $route->id)->inRandomOrder()->first();
            $feeType = DB::table('transport_fee_types')->where('route_id', $route->id)->first();

            $startDate = '2024-04-01';
            $status    = $student->id % 5 == 0 ? 'inactive' : ($student->id % 7 == 0 ? 'pending' : 'active');
            $endDate   = $status == 'inactive' ? date('Y-m-d', strtotime('2024-07-31')) : null;

            $studentTransportData[] = [
                'student_id'             => $student->id,
                'route_id'               => $route->id,
                'route_stop_id'          => $stop ? $stop->id : null,
                'vehicle_id'             => $route->vehicle_id,
                'transport_fee_type_id'  => $feeType ? $feeType->id : null,
                'start_date'             => $startDate,
                'end_date'               => $endDate,
                'status'                 => $status,
                'remarks'                => $student->id % 3 == 0 ? 'Special needs student' : null,
                'created_at'             => now(),
                'updated_at'             => now(),
            ];
        }

        foreach (array_chunk($studentTransportData, 50) as $chunk) {
            DB::table('student_transports')->insertOrIgnore($chunk);
        }
        $studentTransportsDb = DB::table('student_transports')->get();
        $this->command->info("✅ " . $studentTransportsDb->count() . " student transport assignments created");

        // 27g. Student Transport Fee Payments
        $transportPaymentData = [];
        $months               = ['2024-04', '2024-05', '2024-06', '2024-07'];

        foreach ($studentTransportsDb as $assignment) {
            $feeType = DB::table('transport_fee_types')->where('id', $assignment->transport_fee_type_id)->first();
            if (!$feeType) continue;

            $amount     = $feeType->amount;
            $monthIndex = 0;

            foreach ($months as $month) {
                $status      = $monthIndex < 2 ? 'paid' : ($monthIndex == 2 ? 'pending' : 'overdue');
                $paidAmount  = $status == 'paid' ? $amount : ($status == 'partial' ? $amount * 0.5 : 0);
                $paymentDate = $status == 'paid' ? date('Y-m-d', strtotime($month . '-05')) : null;

                $transportPaymentData[] = [
                    'student_transport_id' => $assignment->id,
                    'month'                => $month,
                    'amount'               => $amount,
                    'paid_amount'          => $paidAmount,
                    'due_date'             => date('Y-m-d', strtotime($month . '-10')),
                    'payment_date'         => $paymentDate,
                    'status'               => $status,
                    'receipt_number'       => $status == 'paid' ? 'TRCP-' . str_pad(count($transportPaymentData) + 1, 6, '0', STR_PAD_LEFT) : null,
                    'remarks'              => $status == 'overdue' ? 'Payment overdue - please clear' : null,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];
                $monthIndex++;
            }
        }

        foreach (array_chunk($transportPaymentData, 100) as $chunk) {
            DB::table('student_transport_fee_payments')->insert($chunk);
        }
        $this->command->info("✅ " . count($transportPaymentData) . " transport fee payments created");

        // 27h. Vehicle Trip Logs
        $tripLogData  = [];
        $tripTypes    = ['pickup', 'drop', 'both'];

        foreach ($vehiclesDb as $vehicle) {
            $route  = $routesDb->where('vehicle_id', $vehicle->id)->first();
            $driver = $driversDb->where('id', $vehicle->driver_id)->first();

            for ($i = 0; $i < 20; $i++) {
                $date       = date('Y-m-d', strtotime('2024-04-01 +' . rand(0, 120) . ' days'));
                $tripStatus = $i % 10 == 0 ? 'cancelled' : ($i % 7 == 0 ? 'scheduled' : 'completed');

                $tripLogData[] = [
                    'vehicle_id'      => $vehicle->id,
                    'route_id'        => $route ? $route->id : null,
                    'driver_id'       => $driver ? $driver->id : null,
                    'trip_date'       => $date,
                    'trip_type'       => $tripTypes[array_rand($tripTypes)],
                    'start_time'      => date('H:i:s', strtotime('07:' . rand(0, 59) . ':00')),
                    'end_time'        => $tripStatus == 'completed' ? date('H:i:s', strtotime('14:' . rand(0, 59) . ':00')) : null,
                    'start_latitude'  => 31.5204 + (rand(-100, 100) / 1000),
                    'start_longitude' => 74.3587 + (rand(-100, 100) / 1000),
                    'end_latitude'    => 31.5204 + (rand(-100, 100) / 1000),
                    'end_longitude'   => 74.3587 + (rand(-100, 100) / 1000),
                    'status'          => $tripStatus,
                    'notes'           => $tripStatus == 'cancelled' ? 'Route cancelled due to maintenance' : null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        foreach (array_chunk($tripLogData, 100) as $chunk) {
            DB::table('vehicle_trip_logs')->insert($chunk);
        }
        $this->command->info("✅ " . count($tripLogData) . " vehicle trip logs created");

        // 27i. Vehicle Maintenance Logs
        $maintenanceData  = [];
        $maintenanceTypes = ['Oil Change', 'Tire Rotation', 'Brake Check', 'Engine Tune', 'Full Service', 'AC Repair', 'Battery Check'];

        foreach ($vehiclesDb as $vehicle) {
            for ($i = 0; $i < rand(2, 5); $i++) {
                $maintenanceData[] = [
                    'vehicle_id'       => $vehicle->id,
                    'maintenance_date' => date('Y-m-d', strtotime('2024-04-01 +' . rand(0, 180) . ' days')),
                    'type'             => $maintenanceTypes[array_rand($maintenanceTypes)],
                    'description'      => 'Regular maintenance for vehicle ' . $vehicle->vehicle_number,
                    'cost'             => rand(2000, 15000) + rand(0, 99),
                    'next_due_date'    => date('Y-m-d', strtotime('+3 months +' . rand(0, 30) . ' days')),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }
        DB::table('vehicle_maintenance_logs')->insert($maintenanceData);
        $this->command->info("✅ " . count($maintenanceData) . " vehicle maintenance logs created");

        // 27j. Vehicle Fuel Logs
        $fuelLogData = [];
        $stations    = ['Shell', 'Caltex', 'PSO', 'Total', 'Gasoil', 'Hascol'];

        foreach ($vehiclesDb as $vehicle) {
            for ($i = 0; $i < rand(5, 15); $i++) {
                $liters       = rand(20, 80) + rand(0, 9) / 10;
                $costPerLiter = rand(250, 280) + rand(0, 9) / 10;

                $fuelLogData[] = [
                    'vehicle_id'       => $vehicle->id,
                    'fuel_date'        => date('Y-m-d', strtotime('2024-04-01 +' . rand(0, 180) . ' days')),
                    'liters'           => $liters,
                    'cost_per_liter'   => $costPerLiter,
                    'total_cost'       => $liters * $costPerLiter,
                    'odometer_reading' => rand(10000, 50000),
                    'station_name'     => $stations[array_rand($stations)],
                    'notes'            => $i % 3 == 0 ? 'Fuel filled for route' : null,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }
        DB::table('vehicle_fuel_logs')->insert($fuelLogData);
        $this->command->info("✅ " . count($fuelLogData) . " vehicle fuel logs created");

        // ============================================================
        // 28. HOSTEL DATA
        // ============================================================
        $this->command->info("\n🏠 Creating hostel data...");

        // 28a. Hostels
        $hostelData  = [];
        $hostelTypes = ['boys', 'girls', 'boys', 'girls', 'boys'];
        $hostelNames = [
            'Al-Muhammad Boys Hostel',
            'Al-Muhammad Girls Hostel',
            'Iqbal Boys Hostel',
            'Fatima Girls Hostel',
            'Jinnah Boys Hostel',
        ];

        $staffIds = DB::table('staff')->pluck('id')->toArray();

        foreach ($hostelNames as $index => $name) {
            $hostelData[] = [
                'name'           => $name,
                'code'           => 'H-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'type'           => $hostelTypes[$index],
                'address'        => $this->faker->streetAddress . ', ' . $this->cities[array_rand($this->cities)],
                'warden_id'      => $staffIds[array_rand($staffIds)],
                'total_capacity' => rand(50, 100),
                'description'    => 'Modern hostel with all facilities',
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        DB::table('hostels')->insert($hostelData);
        $hostelsDb = DB::table('hostels')->get();
        $this->command->info("✅ " . count($hostelData) . " hostels created");

        // 28b. Hostel Staff Assignments
        $hostelStaffData = [];
        $roles           = ['warden', 'deputy_warden', 'caretaker', 'security', 'other'];

        foreach ($hostelsDb as $hostel) {
            $numStaff       = rand(2, 4);
            $assignedStaff  = [];

            for ($i = 0; $i < $numStaff; $i++) {
                $staffId = $staffIds[array_rand($staffIds)];
                if (in_array($staffId, $assignedStaff)) continue;
                $assignedStaff[] = $staffId;

                $hostelStaffData[] = [
                    'hostel_id'     => $hostel->id,
                    'staff_id'      => $staffId,
                    'role'          => $i == 0 ? 'warden' : $roles[array_rand(array_slice($roles, 1))],
                    'assigned_date' => date('Y-m-d', strtotime('-1 year +' . rand(0, 365) . ' days')),
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }
        DB::table('hostel_staff_assignments')->insert($hostelStaffData);
        $this->command->info("✅ " . count($hostelStaffData) . " hostel staff assignments created");

        // 28c. Hostel Room Types
        $roomTypeData = [
            ['name' => 'Standard Single', 'default_capacity' => 1, 'description' => 'Single room with basic facilities'],
            ['name' => 'Standard Double', 'default_capacity' => 2, 'description' => 'Double room with basic facilities'],
            ['name' => 'Triple Sharing',  'default_capacity' => 3, 'description' => 'Triple sharing room'],
            ['name' => 'Dormitory',       'default_capacity' => 4, 'description' => 'Dormitory with 4 beds'],
            ['name' => 'Deluxe Single',   'default_capacity' => 1, 'description' => 'Deluxe single room with AC and attached bathroom'],
        ];
        DB::table('hostel_room_types')->insert($roomTypeData);
        $roomTypesDb = DB::table('hostel_room_types')->get();
        $this->command->info("✅ " . count($roomTypeData) . " hostel room types created");

        // 28d. Hostel Rooms
        $hostelRoomData = [];
        $floors         = ['Ground', '1st', '2nd', '3rd'];

        foreach ($hostelsDb as $hostel) {
            $numRooms = rand(15, 30);

            for ($i = 1; $i <= $numRooms; $i++) {
                $roomType = $roomTypesDb->random();
                $capacity = $roomType->default_capacity;

                $status           = $i % 3 == 0 ? 'full' : ($i % 7 == 0 ? 'maintenance' : 'available');
                $currentOccupancy = $status == 'full' ? $capacity : ($status == 'available' ? rand(0, $capacity - 1) : 0);

                $hostelRoomData[] = [
                    'hostel_id'         => $hostel->id,
                    'room_type_id'      => $roomType->id,
                    'room_number'       => 'R' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'floor'             => $floors[$i % count($floors)],
                    'capacity'          => $capacity,
                    'current_occupancy' => $currentOccupancy,
                    'status'            => $status,
                    'notes'             => $status == 'maintenance' ? 'Under renovation' : null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }
        DB::table('hostel_rooms')->insert($hostelRoomData);
        $hostelRoomsDb = DB::table('hostel_rooms')->get();
        $this->command->info("✅ " . count($hostelRoomData) . " hostel rooms created");

        // 28e. Hostel Fee Types
        $hostelFeeData = [];
        $periods       = ['monthly', 'quarterly', 'annually'];
        $feeNames      = [
            'Hostel Fee - Standard',
            'Hostel Fee - Deluxe',
            'Hostel Fee - Dormitory',
            'Hostel Fee - Executive',
        ];

        foreach ($hostelsDb as $hostel) {
            foreach ($feeNames as $index => $name) {
                $baseAmount = rand(5000, 15000);
                $period     = $periods[$index % 3];

                $hostelFeeData[] = [
                    'name'        => $name . ' - ' . $hostel->name,
                    'hostel_id'   => $hostel->id,
                    'period'      => $period,
                    'amount'      => $period == 'monthly' ? $baseAmount : ($period == 'quarterly' ? $baseAmount * 3 : $baseAmount * 12),
                    'description' => $name . ' fee for ' . $hostel->name,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }
        DB::table('hostel_fee_types')->insert($hostelFeeData);
        $hostelFeeTypesDb = DB::table('hostel_fee_types')->get();
        $this->command->info("✅ " . count($hostelFeeData) . " hostel fee types created");

        // 28f. Student Hostel Allocations
        $hostelAllocationData = [];
        $studentsList         = DB::table('students')->inRandomOrder()->take(80)->get();
        $studentIdsAllocated  = [];

        foreach ($studentsList as $student) {
            if (in_array($student->id, $studentIdsAllocated)) continue;
            $studentIdsAllocated[] = $student->id;

            $hostel = $hostelsDb->random();
            $room   = DB::table('hostel_rooms')
                ->where('hostel_id', $hostel->id)
                ->where('status', '!=', 'maintenance')
                ->inRandomOrder()
                ->first();

            if (!$room) continue;

            $feeType = DB::table('hostel_fee_types')
                ->where('hostel_id', $hostel->id)
                ->inRandomOrder()
                ->first();

            $status     = $student->id % 5 == 0 ? 'vacated' : ($student->id % 7 == 0 ? 'pending' : 'active');
            $vacateDate = $status == 'vacated' ? date('Y-m-d', strtotime('2024-06-30')) : null;

            $hostelAllocationData[] = [
                'student_id'          => $student->id,
                'hostel_id'           => $hostel->id,
                'room_id'             => $room->id,
                'hostel_fee_type_id'  => $feeType ? $feeType->id : null,
                'bed_number'          => 'B' . rand(1, $room->capacity),
                'allocation_date'     => '2024-04-01',
                'vacate_date'         => $vacateDate,
                'status'              => $status,
                'remarks'             => $student->id % 3 == 0 ? 'Special requests noted' : null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }
        DB::table('student_hostel_allocations')->insert($hostelAllocationData);
        $hostelAllocationsDb = DB::table('student_hostel_allocations')->get();
        $this->command->info("✅ " . count($hostelAllocationData) . " student hostel allocations created");

        // 28g. Student Hostel Fee Payments
        $hostelPaymentData = [];
        $months            = ['2024-04', '2024-05', '2024-06', '2024-07'];

        foreach ($hostelAllocationsDb as $allocation) {
            $feeType = DB::table('hostel_fee_types')->where('id', $allocation->hostel_fee_type_id)->first();
            if (!$feeType) continue;

            $amount     = $feeType->amount;
            $monthIndex = 0;

            foreach ($months as $month) {
                $status = $allocation->status == 'active'
                    ? ($monthIndex < 2 ? 'paid' : 'pending')
                    : ($monthIndex < 2 ? 'paid' : 'overdue');

                $paidAmount  = $status == 'paid' ? $amount : ($status == 'partial' ? $amount * 0.5 : 0);
                $paymentDate = $status == 'paid' ? date('Y-m-d', strtotime($month . '-05')) : null;

                $hostelPaymentData[] = [
                    'student_hostel_allocation_id' => $allocation->id,
                    'month'                        => $month,
                    'amount'                       => $amount,
                    'paid_amount'                  => $paidAmount,
                    'due_date'                     => date('Y-m-d', strtotime($month . '-10')),
                    'payment_date'                 => $paymentDate,
                    'status'                       => $status,
                    'receipt_number'               => $status == 'paid' ? 'HCP-' . str_pad(count($hostelPaymentData) + 1, 6, '0', STR_PAD_LEFT) : null,
                    'remarks'                      => $status == 'overdue' ? 'Hostel fee overdue' : null,
                    'created_at'                   => now(),
                    'updated_at'                   => now(),
                ];
                $monthIndex++;
            }
        }

        foreach (array_chunk($hostelPaymentData, 100) as $chunk) {
            DB::table('student_hostel_fee_payments')->insert($chunk);
        }
        $this->command->info("✅ " . count($hostelPaymentData) . " hostel fee payments created");

        // 28h. Hostel Attendance
        $hostelAttendanceData = [];
        $attendanceStatuses   = ['present', 'absent', 'excused', 'leave'];
        $startDate            = now()->subDays(30);

        foreach ($hostelAllocationsDb->take(40) as $allocation) {
            for ($date = clone $startDate; $date->lte(now()); $date->addDay()) {
                if ($date->isWeekend()) continue;

                $status       = rand(1, 100) <= 85 ? 'present' : $attendanceStatuses[rand(1, 3)];
                $checkInTime  = $status == 'present' ? date('H:i:s', strtotime('19:' . rand(0, 59) . ':00')) : null;
                $checkOutTime = $status == 'present' ? date('H:i:s', strtotime('07:' . rand(0, 59) . ':00')) : null;

                $hostelAttendanceData[] = [
                    'student_hostel_allocation_id' => $allocation->id,
                    'attendance_date'              => $date->format('Y-m-d'),
                    'status'                       => $status,
                    'check_in_time'                => $checkInTime,
                    'check_out_time'               => $checkOutTime,
                    'remarks'                      => $status != 'present' ? 'Not present in hostel' : null,
                    'created_at'                   => now(),
                    'updated_at'                   => now(),
                ];
            }
        }

        foreach (array_chunk($hostelAttendanceData, 500) as $chunk) {
            DB::table('hostel_attendance')->insert($chunk);
        }
        $this->command->info("✅ " . count($hostelAttendanceData) . " hostel attendance records created");

        // 28i. Hostel Room Maintenance
        $roomMaintenanceData    = [];
        $issueTypes             = ['Plumbing', 'Electrical', 'Furniture', 'AC Repair', 'Pest Control', 'Cleaning', 'Painting'];
        $priorities             = ['low', 'medium', 'high', 'urgent'];
        $maintenanceStatuses    = ['reported', 'in_progress', 'completed', 'cancelled'];

        foreach ($hostelRoomsDb->take(20) as $room) {
            for ($i = 0; $i < rand(0, 2); $i++) {
                $status        = $maintenanceStatuses[array_rand($maintenanceStatuses)];
                $completedDate = $status == 'completed' ? date('Y-m-d', strtotime('+3 days')) : null;

                $roomMaintenanceData[] = [
                    'room_id'        => $room->id,
                    'reported_by'    => $staffIds[array_rand($staffIds)],
                    'issue_type'     => $issueTypes[array_rand($issueTypes)],
                    'description'    => 'Maintenance issue in room ' . $room->room_number,
                    'priority'       => $priorities[array_rand($priorities)],
                    'status'         => $status,
                    'reported_date'  => date('Y-m-d', strtotime('-30 days +' . rand(0, 60) . ' days')),
                    'completed_date' => $completedDate,
                    'cost'           => $status == 'completed' ? rand(500, 5000) : 0,
                    'remarks'        => $status == 'completed' ? 'Resolved successfully' : null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }
        }
        DB::table('hostel_room_maintenance')->insert($roomMaintenanceData);
        $this->command->info("✅ " . count($roomMaintenanceData) . " hostel room maintenance records created");

        // Update hostel allocations with correct fee type IDs
        $this->command->info("\n🔧 Updating hostel fee type IDs...");

        $feeTypesMap = [];
        foreach ($hostelFeeTypesDb as $type) {
            if (!isset($feeTypesMap[$type->hostel_id])) {
                $feeTypesMap[$type->hostel_id] = [];
            }
            $feeTypesMap[$type->hostel_id][] = $type->id;
        }

        foreach ($hostelAllocationsDb as $allocation) {
            if (!$allocation->hostel_fee_type_id && isset($feeTypesMap[$allocation->hostel_id])) {
                $feeId = $feeTypesMap[$allocation->hostel_id][array_rand($feeTypesMap[$allocation->hostel_id])];
                DB::table('student_hostel_allocations')
                    ->where('id', $allocation->id)
                    ->update(['hostel_fee_type_id' => $feeId]);
            }
        }
        $this->command->info("✅ Hostel allocations updated with correct fee type IDs");

        // ============================================================
        // 29. USERS + ROLE ASSIGNMENT
        // ============================================================
        $this->command->info("\n👤 Creating users and assigning roles...");

           $this->call(RolePermissionSeeder::class);
        // Clear Spatie caches
               if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $usersInserted = 0;
        $roleCount     = ['admin' => 0, 'teacher' => 0, 'student' => 0];
        $now           = now();

        // 29a. Staff Users (teachers + admins)
        $staffForUsers = DB::table('staff')->get();

        foreach ($staffForUsers as $staff) {
            $role = 'teacher';
            if (in_array($staff->designation, ['Principal', 'Admin Officer'])) {
                $role = 'admin';
            }

            $userId = DB::table('users')->insertGetId([
                'name'       => trim($staff->first_name . ' ' . $staff->last_name),
                'email'      => $staff->email,
                'password'   => bcrypt('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->assignRole($role);
                $roleCount[$role]++;
                $usersInserted++;
            }
        }

        // 29b. Student Users
        $studentsForUsers = DB::table('students')->get();

        foreach ($studentsForUsers as $student) {
            $userId = DB::table('users')->insertGetId([
                'name'       => trim($student->first_name . ' ' . $student->last_name),
                'email'      => $student->email ?: ('student' . $student->id . '@school.test'),
                'password'   => bcrypt('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->assignRole('student');
                $roleCount['student']++;
                $usersInserted++;
            }
        }

        $this->command->info("✅ {$usersInserted} users created");
        $this->command->info("   🔑 admin:   {$roleCount['admin']}");
        $this->command->info("   🎓 teacher: {$roleCount['teacher']}");
        $this->command->info("   👨‍🎓 student: {$roleCount['student']}");
        $this->command->info("   📧 All users password: password123");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ============================================================
        // FINAL SUMMARY
        // ============================================================
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
        $this->command->info("   📊 Total: " . $studentsDb->count() . " students");
        $this->command->info("");
        $this->command->info("👨‍🏫 Staff: " . count($teacherIds) . " teachers + 2 admin = " . count($staffData));
        $this->command->info("👤 Users: " . $usersInserted . " (admin: {$roleCount['admin']}, teacher: {$roleCount['teacher']}, student: {$roleCount['student']})");
        $this->command->info("📚 " . $subjectsDb->count() . " subjects with Pakistani textbook names");
        $this->command->info("📝 " . count($examData) . " exams (Mid Term + Final Term)");
        $this->command->info("📊 " . count($marksData) . " exam marks");
        $this->command->info("🏆 " . count($resultData) . " exam results with ranks");
        $this->command->info("💰 " . count($installmentData) . " fee installments");
        $this->command->info("📋 " . count($timetableData) . " timetable entries");
        $this->command->info("");
        $this->command->info("🚌 Transport System:");
        $this->command->info("   🚗 " . count($driverData) . " drivers");
        $this->command->info("   🚌 " . count($vehicleData) . " vehicles");
        $this->command->info("   🗺️ " . count($routeData) . " transport routes");
        $this->command->info("   🚏 " . count($stopData) . " route stops");
        $this->command->info("   💰 " . count($transportFeeData) . " transport fee types");
        $this->command->info("   👨‍🎓 " . $studentTransportsDb->count() . " student transport assignments");
        $this->command->info("   💳 " . count($transportPaymentData) . " transport fee payments");
        $this->command->info("   📋 " . count($tripLogData) . " vehicle trip logs");
        $this->command->info("   🔧 " . count($maintenanceData) . " vehicle maintenance logs");
        $this->command->info("   ⛽ " . count($fuelLogData) . " vehicle fuel logs");
        $this->command->info("");
        $this->command->info("🏠 Hostel System:");
        $this->command->info("   🏢 " . count($hostelData) . " hostels");
        $this->command->info("   👔 " . count($hostelStaffData) . " hostel staff assignments");
        $this->command->info("   🚪 " . count($roomTypeData) . " hostel room types");
        $this->command->info("   🛏️ " . count($hostelRoomData) . " hostel rooms");
        $this->command->info("   💰 " . count($hostelFeeData) . " hostel fee types");
        $this->command->info("   👨‍🎓 " . count($hostelAllocationData) . " student hostel allocations");
        $this->command->info("   💳 " . count($hostelPaymentData) . " hostel fee payments");
        $this->command->info("   📋 " . count($hostelAttendanceData) . " hostel attendance records");
        $this->command->info("   🔧 " . count($roomMaintenanceData) . " hostel room maintenance records");
        $this->command->info("========================================\n");
    }
}