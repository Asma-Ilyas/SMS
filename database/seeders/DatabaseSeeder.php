<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    // ---------- ACADEMIC SESSIONS ----------
    public static $ACADEMIC_SESSIONS = [
        ['name' => '2023-2024', 'start_date' => '2023-08-01', 'end_date' => '2024-07-31', 'is_active' => false, 'description' => null],
        ['name' => '2024-2025', 'start_date' => '2024-08-01', 'end_date' => '2025-07-31', 'is_active' => true,  'description' => 'Current session'],
        ['name' => '2025-2026', 'start_date' => '2025-08-01', 'end_date' => '2026-07-31', 'is_active' => false, 'description' => null],
    ];

    // ---------- GRADES ----------
    public static $GRADES = [
        ['name' => 'Nursery', 'numeric_value' => 0],
        ['name' => 'KG',      'numeric_value' => 0],
        ['name' => '1',       'numeric_value' => 1],
        ['name' => '2',       'numeric_value' => 2],
        ['name' => '3',       'numeric_value' => 3],
        ['name' => '4',       'numeric_value' => 4],
        ['name' => '5',       'numeric_value' => 5],
        ['name' => '6',       'numeric_value' => 6],
        ['name' => '7',       'numeric_value' => 7],
        ['name' => '8',       'numeric_value' => 8],
        ['name' => '9',       'numeric_value' => 9],
        ['name' => '10',      'numeric_value' => 10],
    ];

    // ---------- STREAMS ----------
    public static $STREAMS = [
        ['name' => 'Science'],
        ['name' => 'Arts'],
        ['name' => 'Commerce'],
        ['name' => 'General'],
    ];

    // ---------- ELECTIVE TRACKS ----------
    public static $ELECTIVE_TRACKS = [
        ['stream_id' => 1, 'name' => 'Biology'],
        ['stream_id' => 1, 'name' => 'Computer Science'],
        ['stream_id' => 1, 'name' => 'Physics Advanced'],
        ['stream_id' => 1, 'name' => 'Chemistry Advanced'],
    ];

    // ---------- CLASSES ----------
    public static $CLASSES = [
        // Nursery (grade_id 1)
        ['academic_session_id' => 2, 'grade_id' => 1, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 30],
        ['academic_session_id' => 2, 'grade_id' => 1, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 30],
        // KG (grade_id 2)
        ['academic_session_id' => 2, 'grade_id' => 2, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 30],
        ['academic_session_id' => 2, 'grade_id' => 2, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 30],
        // Grade 1 (grade_id 3)
        ['academic_session_id' => 2, 'grade_id' => 3, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 35],
        ['academic_session_id' => 2, 'grade_id' => 3, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 35],
        // Grade 2 (grade_id 4)
        ['academic_session_id' => 2, 'grade_id' => 4, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 35],
        ['academic_session_id' => 2, 'grade_id' => 4, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 35],
        // Grade 3 (grade_id 5)
        ['academic_session_id' => 2, 'grade_id' => 5, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 35],
        ['academic_session_id' => 2, 'grade_id' => 5, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 35],
        // Grade 4 (grade_id 6)
        ['academic_session_id' => 2, 'grade_id' => 6, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 35],
        ['academic_session_id' => 2, 'grade_id' => 6, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 35],
        // Grade 5 (grade_id 7)
        ['academic_session_id' => 2, 'grade_id' => 7, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 35],
        ['academic_session_id' => 2, 'grade_id' => 7, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 35],
        // Grade 6 (grade_id 8)
        ['academic_session_id' => 2, 'grade_id' => 8, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 8, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 40],
        // Grade 7 (grade_id 9)
        ['academic_session_id' => 2, 'grade_id' => 9, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 9, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 40],
        // Grade 8 (grade_id 10)
        ['academic_session_id' => 2, 'grade_id' => 10, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 10, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 10, 'stream_id' => 4, 'elective_track_id' => null, 'section' => 'C', 'capacity' => 40],
        // Grade 9 – Science Biology (track id 1)
        ['academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 1, 'elective_track_id' => 1, 'section' => 'A', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 1, 'elective_track_id' => 1, 'section' => 'B', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 1, 'elective_track_id' => 1, 'section' => 'C', 'capacity' => 40],
        // Grade 9 – Science Computer Science (track id 2)
        ['academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 1, 'elective_track_id' => 2, 'section' => 'A', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 1, 'elective_track_id' => 2, 'section' => 'B', 'capacity' => 40],
        // Grade 9 – Arts (stream_id 2)
        ['academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 2, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 35],
        ['academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 2, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 35],
        // Grade 10 – Science Biology
        ['academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 1, 'elective_track_id' => 1, 'section' => 'A', 'capacity' => 45],
        ['academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 1, 'elective_track_id' => 1, 'section' => 'B', 'capacity' => 45],
        ['academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 1, 'elective_track_id' => 1, 'section' => 'C', 'capacity' => 45],
        // Grade 10 – Science Computer Science
        ['academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 1, 'elective_track_id' => 2, 'section' => 'A', 'capacity' => 45],
        ['academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 1, 'elective_track_id' => 2, 'section' => 'B', 'capacity' => 45],
        // Grade 10 – Arts
        ['academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 2, 'elective_track_id' => null, 'section' => 'A', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 2, 'elective_track_id' => null, 'section' => 'B', 'capacity' => 40],
        ['academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 2, 'elective_track_id' => null, 'section' => 'C', 'capacity' => 40],
    ];

    // ---------- BANKS ----------
    public static $BANKS = [
        ['name' => 'Habib Bank Ltd', 'branch_name' => 'Main Branch', 'account_title' => 'School Fee Account', 'account_number' => '1234-567890-01', 'iban' => 'PK36HABB0012345678901234', 'routing_number' => '123456789', 'address' => '123 Main Street, City', 'is_active' => true],
        ['name' => 'United Bank Ltd', 'branch_name' => 'City Branch', 'account_title' => 'School Fee Collection', 'account_number' => '9876-543210-02', 'iban' => 'PK36UNIL0012345678905678', 'routing_number' => '987654321', 'address' => '456 Secondary Road, City', 'is_active' => true],
        ['name' => 'Meezan Bank', 'branch_name' => 'Islamic Banking', 'account_title' => 'School Fee', 'account_number' => '5566-778899-03', 'iban' => 'PK36MEZN0012345678909012', 'routing_number' => '112233445', 'address' => '789 Islamic Center', 'is_active' => true],
    ];

    // ---------- DISCOUNTS ----------
    public static $DISCOUNTS = [
        ['name' => 'Merit Scholarship 10%', 'description' => 'For top performers', 'type' => 'percentage', 'value' => 10, 'is_active' => true],
        ['name' => 'Sibling Discount 5%',  'description' => 'For siblings',        'type' => 'percentage', 'value' => 5,  'is_active' => true],
        ['name' => 'Need Based 20%',       'description' => 'Financial aid',       'type' => 'percentage', 'value' => 20, 'is_active' => true],
        ['name' => 'Staff Ward 50%',       'description' => 'Employees children',  'type' => 'percentage', 'value' => 50, 'is_active' => true],
        ['name' => 'Early Bird Fixed ₹1000','description' => 'Pay before due date', 'type' => 'fixed',      'value' => 1000,'is_active' => true],
    ];

    // ---------- FEE SUBMISSION TYPES ----------
    public static $FEE_SUBMISSION_TYPES = [
        ['name' => 'General Fee',      'period' => 'annually',  'amount' => 2800,  'description' => 'Annual fee', 'is_active' => true],
        ['name' => 'Admission Fee',    'period' => 'one_time',  'amount' => 8000,  'description' => 'One time admission', 'is_active' => true],
        ['name' => 'Sports Fee',       'period' => 'annually',  'amount' => 3500,  'description' => 'Annual sports', 'is_active' => true],
        ['name' => 'Library Fee',      'period' => 'annually',  'amount' => 2000,  'description' => 'Library charges', 'is_active' => true],
        ['name' => 'Exam Fee',         'period' => 'quarterly', 'amount' => 1500,  'description' => 'Examination fee', 'is_active' => true],
        ['name' => 'Transport Fee',    'period' => 'monthly',   'amount' => 1200,  'description' => 'Bus service', 'is_active' => true],
        ['name' => 'Lab Fee',          'period' => 'annually',  'amount' => 2500,  'description' => 'Science lab', 'is_active' => true],
        ['name' => 'Security Fee',     'period' => 'one_time',  'amount' => 3000,  'description' => 'Refundable', 'is_active' => true],
        ['name' => 'Late Fee Fine',    'period' => 'monthly',   'amount' => 500,   'description' => 'Late payment penalty', 'is_active' => true],
    ];

    // ---------- SIMPLE FEE TYPES ----------
    public static $FEE_TYPES_SIMPLE = [
        ['name' => 'General Fee',   'description' => 'Basic tuition fee (annual)', 'is_active' => true],
        ['name' => 'Admission Fee', 'description' => 'One time fee at admission', 'is_active' => true],
        ['name' => 'Sports Fee',    'description' => 'Annual sports charges', 'is_active' => true],
        ['name' => 'Library Fee',   'description' => 'Annual library charges', 'is_active' => true],
        ['name' => 'Exam Fee',      'description' => 'Per examination fee', 'is_active' => true],
        ['name' => 'Transport Fee', 'description' => 'Monthly bus service fee', 'is_active' => true],
        ['name' => 'Lab Fee',       'description' => 'Science lab fee', 'is_active' => true],
        ['name' => 'Security Fee',  'description' => 'Refundable security deposit', 'is_active' => true],
    ];

    // ---------- EMPLOYEE CATEGORIES (for staff) ----------
    public static $EMPLOYEE_CATEGORIES = [
        ['name' => 'Teaching',        'code' => 'TCH', 'description' => 'Teaching staff', 'is_active' => true],
        ['name' => 'Administrative',  'code' => 'ADM', 'description' => 'Admin staff',    'is_active' => true],
        ['name' => 'Support',         'code' => 'SUP', 'description' => 'Support staff',   'is_active' => true],
        ['name' => 'Management',      'code' => 'MGT', 'description' => 'Management',       'is_active' => true],
    ];

    // ---------- EXAM TYPES ----------
    public static $EXAM_TYPES = [
        ['name' => 'Monthly Test', 'code' => 'MONTHLY', 'sort_order' => 1, 'is_active' => true],
        ['name' => 'Half Yearly',  'code' => 'HALF',    'sort_order' => 2, 'is_active' => true],
        ['name' => 'Final',        'code' => 'FINAL',   'sort_order' => 3, 'is_active' => true],
    ];

    // ---------- EXAM GROUPS ----------
    public static $EXAM_GROUPS = [
        ['name' => 'Primary',   'code' => 'PRI', 'description' => 'Grades 1-5',     'sort_order' => 1, 'is_active' => true],
        ['name' => 'Middle',    'code' => 'MID', 'description' => 'Grades 6-8',     'sort_order' => 2, 'is_active' => true],
        ['name' => 'Secondary', 'code' => 'SEC', 'description' => 'Grades 9-10',    'sort_order' => 3, 'is_active' => true],
    ];

    // ---------- SUBJECTS (for exam marks) ----------
    public static $SUBJECTS = [
        ['name' => 'Mathematics', 'code' => 'MATH101', 'is_active' => true],
        ['name' => 'English',     'code' => 'ENG101',  'is_active' => true],
        ['name' => 'Urdu',        'code' => 'URD101',  'is_active' => true],
        ['name' => 'Science',     'code' => 'SCI101',  'is_active' => true],
        ['name' => 'Islamiyat',   'code' => 'ISL101',  'is_active' => true],
        ['name' => 'Physics',     'code' => 'PHY101',  'is_active' => true],
        ['name' => 'Chemistry',   'code' => 'CHEM101', 'is_active' => true],
        ['name' => 'Biology',     'code' => 'BIO101',  'is_active' => true],
        ['name' => 'Computer',    'code' => 'CS101',   'is_active' => true],
        ['name' => 'History',     'code' => 'HIS101',  'is_active' => true],
        ['name' => 'Economics',   'code' => 'ECO101',  'is_active' => true],
    ];

    // ---------- GRADE SCALES ----------
  // ---------- GRADE SCALES ----------
public static $GRADE_SCALES = [
    [
        'name' => 'Default',
        'grades' => '[{"min":90,"max":100,"grade":"A+"},{"min":80,"max":89,"grade":"A"},{"min":70,"max":79,"grade":"B+"},{"min":60,"max":69,"grade":"B"},{"min":50,"max":59,"grade":"C"},{"min":40,"max":49,"grade":"D"},{"min":0,"max":39,"grade":"F"}]',
        'is_default' => true,
        'created_at' => null,
        'updated_at' => null,
    ],
];

    // ------------------------------------------------------------------
    // Student-related static arrays (filled by initStaticData)
    // ------------------------------------------------------------------
    public static $STUDENTS;
    public static $INSTALLMENTS;
    public static $INVOICES;
    public static $STUDENT_DISCOUNTS;

    /**
     * Generate all student-related static data.
     */
    public static function initStaticData(): void
    {
        if (self::$STUDENTS !== null) {
            return;
        }

        $faker = Faker::create();

        $students = [];
        $installments = [];
        $invoices = [];
        $discounts = [];

        $generalFeeId = 1;
        $sportsFeeId = 3;
        $bankIds = [1, 2, 3];

        $studentId = 1;
        $installmentId = 1;

        foreach (self::$CLASSES as $classIndex => $classDef) {
            if ($classDef['academic_session_id'] != 2) {
                continue;
            }
            $classId = $classIndex + 1;

            for ($i = 1; $i <= 20; $i++) {
                $firstName = ($i <= 3) ? 'SharedNameStudent' : $faker->firstName;
                $middleName = $faker->optional()->firstName;
                $lastName = $faker->lastName;
                $gender = $faker->randomElement(['Male', 'Female']);
                $dateOfBirth = $faker->dateTimeBetween('-15 years', '-5 years')->format('Y-m-d');
                $dobInWords = $faker->sentence(3);
                $religion = 'Islam';
                $caste = $faker->word;
                $bloodGroup = $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']);
                $address = $faker->address;
                $phone = $faker->phoneNumber;
                $email = $faker->unique()->safeEmail;
                $city = $faker->city;
                $state = $faker->state;
                $country = 'Pakistan';
                $motherTongue = 'Urdu';
                $birthPlace = $faker->city;
                $prevSchool = $faker->company;
                $prevAddress = $faker->address;
                $prevClass = $faker->randomElement(['1','2','3','4','5','6','7','8','9','10']);
                $passoutYear = '2023';
                $prevCategory = 'General';
                $admissionDate = '2024-03-01';
                $studentType = 'New';
                $section = $classDef['section'];
                $admissionNumber = 'STU-' . str_pad($studentId, 4, '0', STR_PAD_LEFT);
                $rollNumber = (string) $studentId;
                $fatherName = $faker->name('male');
                $fatherPhone = $faker->phoneNumber;
                $fatherOcc = $faker->jobTitle;
                $motherName = $faker->name('female');
                $motherPhone = $faker->optional()->phoneNumber;
                $motherOcc = $faker->optional()->jobTitle;

                $students[] = [
                    'first_name'            => $firstName,
                    'middle_name'           => $middleName,
                    'last_name'             => $lastName,
                    'gender'                => $gender,
                    'date_of_birth'         => $dateOfBirth,
                    'dob_in_words'          => $dobInWords,
                    'religion'              => $religion,
                    'caste_subcaste'        => $caste,
                    'blood_group'           => $bloodGroup,
                    'address'               => $address,
                    'phone'                 => $phone,
                    'email'                 => $email,
                    'city'                  => $city,
                    'state'                 => $state,
                    'country'               => $country,
                    'extra_note'            => null,
                    'mother_tongue'         => $motherTongue,
                    'birth_place'           => $birthPlace,
                    'previous_school_name'  => $prevSchool,
                    'previous_school_address'=> $prevAddress,
                    'previous_class'        => $prevClass,
                    'passout_year'          => $passoutYear,
                    'previous_category'     => $prevCategory,
                    'admission_date'        => $admissionDate,
                    'student_type'          => $studentType,
                    'class_id'              => $classId,
                    'section'               => $section,
                    'admission_number'      => $admissionNumber,
                    'roll_number'           => $rollNumber,
                    'profile_photo'         => null,
                    'father_name'           => $fatherName,
                    'father_phone'          => $fatherPhone,
                    'father_occupation'     => $fatherOcc,
                    'mother_name'           => $motherName,
                    'mother_phone'          => $motherPhone,
                    'mother_occupation'     => $motherOcc,
                    'parent_id_proof'       => null,
                    'parent_signature'      => null,
                    'assigned_concession'   => null,
                    'status'                => 'Active',
                    'suspension_start_date' => null,
                    'suspension_end_date'   => null,
                    'suspension_message'    => null,
                ];

                $installments[] = [
                    'student_id'              => $studentId,
                    'fee_submission_type_id'  => $generalFeeId,
                    'installment_number'      => 1,
                    'amount'                  => 2800,
                    'due_date'                => '2024-08-15',
                    'status'                  => 'pending',
                    'paid_amount'             => 0,
                    'payment_date'            => null,
                    'receipt_number'          => null,
                    'remarks'                 => 'Annual General Fee',
                ];

                $invoices[] = [
                    'invoice_number'          => 'INV-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT),
                    'student_id'              => $studentId,
                    'bank_id'                 => $faker->randomElement($bankIds),
                    'student_fee_installment_id' => $installmentId,
                    'amount'                  => 2800,
                    'due_date'                => '2024-08-15',
                    'status'                  => 'pending',
                    'challan_file'            => null,
                    'payment_proof_file'      => null,
                    'payment_remarks'         => null,
                    'paid_at'                 => null,
                    'approved_by'             => null,
                ];

                $studentId++;
                $installmentId++;
            }
        }

        // Extra sports fee for first 20 students
        for ($i = 1; $i <= min(20, $studentId - 1); $i++) {
            $installments[] = [
                'student_id'              => $i,
                'fee_submission_type_id'  => $sportsFeeId,
                'installment_number'      => 1,
                'amount'                  => 3500,
                'due_date'                => '2024-09-30',
                'status'                  => 'pending',
                'paid_amount'             => 0,
                'payment_date'            => null,
                'receipt_number'          => null,
                'remarks'                 => 'Annual Sports Fee',
            ];
            $invoices[] = [
                'invoice_number'          => 'INV-' . str_pad($installmentId, 6, '0', STR_PAD_LEFT),
                'student_id'              => $i,
                'bank_id'                 => $faker->randomElement($bankIds),
                'student_fee_installment_id' => $installmentId,
                'amount'                  => 3500,
                'due_date'                => '2024-09-30',
                'status'                  => 'pending',
                'challan_file'            => null,
                'payment_proof_file'      => null,
                'payment_remarks'         => null,
                'paid_at'                 => null,
                'approved_by'             => null,
            ];
            $installmentId++;
        }

        // Random discounts for 10% of students
        $totalStudents = count($students);
        $discountIds = [1,2,3,4,5];
        for ($i = 1; $i <= min(ceil($totalStudents * 0.1), $totalStudents); $i++) {
            $discounts[] = [
                'student_id'              => $i,
                'discount_id'             => $faker->randomElement($discountIds),
                'fee_submission_type_id'  => null,
                'valid_from'              => '2024-01-01',
                'valid_until'             => '2024-12-31',
            ];
        }

        self::$STUDENTS          = $students;
        self::$INSTALLMENTS      = $installments;
        self::$INVOICES          = $invoices;
        self::$STUDENT_DISCOUNTS = $discounts;
    }

    // ------------------------------------------------------------------
    // Staff & Attendance Generation
    // ------------------------------------------------------------------

    protected function seedEmployeeCategories()
    {
        DB::table('employee_categories')->insert(self::$EMPLOYEE_CATEGORIES);
    }

    protected function generateStaff()
    {
        $faker = Faker::create();
        $staff = [];
        $categoryIds = DB::table('employee_categories')->pluck('id')->toArray();
        if (empty($categoryIds)) {
            $categoryIds = [1,2,3,4];
        }

        for ($i = 1; $i <= 50; $i++) {
            $confirmationDate = $faker->optional()->dateTimeBetween('-4 years', 'now');
            $confirmationDate = $confirmationDate ? $confirmationDate->format('Y-m-d') : null;

            $staff[] = [
                'first_name'               => $faker->firstName,
                'last_name'                => $faker->lastName,
                'father_name'              => $faker->optional()->name('male'),
                'mother_name'              => $faker->optional()->name('female'),
                'gender'                   => $faker->randomElement(['Male', 'Female']),
                'date_of_birth'            => $faker->dateTimeBetween('-50 years', '-22 years')->format('Y-m-d'),
                'cnic'                     => $faker->unique()->numerify('#####-#######-#'),
                'passport_number'          => $faker->optional()->bothify('??#######'),
                'nationality'              => 'Pakistani',
                'religion'                 => $faker->randomElement(['Islam', 'Christianity', 'Hinduism']),
                'email'                    => $faker->unique()->safeEmail,
                'phone'                    => $faker->phoneNumber,
                'mobile'                   => $faker->phoneNumber,
                'emergency_contact_name'   => $faker->name,
                'emergency_contact_relation'=> 'Relative',
                'emergency_contact_phone'  => $faker->phoneNumber,
                'present_address'          => $faker->address,
                'permanent_address'        => $faker->address,
                'city'                     => $faker->city,
                'state'                    => $faker->state,
                'postal_code'              => $faker->postcode,
                'country'                  => 'Pakistan',
                'employee_id'              => 'EMP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'designation'              => $faker->jobTitle,
                'department'               => $faker->randomElement(['Science', 'Arts', 'Admin', 'Accounts']),
                'employment_type'          => $faker->randomElement(['full_time', 'part_time', 'contract']),
                'joining_date'             => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                'confirmation_date'        => $confirmationDate,
                'qualification'            => $faker->sentence(3),
                'experience'               => $faker->paragraph(1),
                'specialization'           => $faker->word,
                'basic_salary'             => $faker->numberBetween(25000, 100000),
                'bank_name'                => $faker->company,
                'bank_account_number'      => $faker->bankAccountNumber,
                'bank_iban'                => $faker->iban('PK'),
                'category_id'              => $faker->randomElement($categoryIds),
                'is_active'                => true,
                'created_at'               => now(),
                'updated_at'               => now(),
            ];
        }
        return $staff;
    }

    protected function generateStaffAttendance($staffIds)
    {
        $faker = Faker::create();
        $attendances = [];
        $startDate = now()->subDays(30);

        for ($date = clone $startDate; $date->lte(now()); $date->addDay()) {
            foreach ($staffIds as $staffId) {
                $isPresent = $faker->boolean(80);
                if ($isPresent) {
                    $checkIn = $faker->dateTimeBetween($date->format('Y-m-d 08:00:00'), $date->format('Y-m-d 10:00:00'));
                    $checkOut = $faker->dateTimeBetween($date->format('Y-m-d 16:00:00'), $date->format('Y-m-d 18:00:00'));
                    $lateDuration = null;
                    if ($checkIn->format('H:i') > '09:00') {
                        $lateMinutes = (strtotime($checkIn->format('H:i')) - strtotime('09:00')) / 60;
                        $lateDuration = sprintf("%02d:%02d:00", floor($lateMinutes/60), $lateMinutes%60);
                    }
                    $attendances[] = [
                        'staff_id'      => $staffId,
                        'date'          => $date->toDateString(),
                        'check_in'      => $checkIn->format('H:i:s'),
                        'check_out'     => $checkOut->format('H:i:s'),
                        'late_duration' => $lateDuration,
                        'overtime'      => null,
                        'status'        => $lateDuration ? 'late' : 'present',
                        'is_approved'   => true,
                        'remarks'       => 'Auto seeded',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                } else {
                    $attendances[] = [
                        'staff_id'      => $staffId,
                        'date'          => $date->toDateString(),
                        'check_in'      => null,
                        'check_out'     => null,
                        'late_duration' => null,
                        'overtime'      => null,
                        'status'        => 'absent',
                        'is_approved'   => true,
                        'remarks'       => 'Absent',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
            }
        }
        return $attendances;
    }

    // ------------------------------------------------------------------
    // Exams & Marks Generation
    // ------------------------------------------------------------------

    protected function seedSubjects()
    {
        DB::table('subjects')->insert(self::$SUBJECTS);
    }

    protected function generateExamsAndMarks()
    {
        $faker = Faker::create();

        DB::table('exam_types')->insert(self::$EXAM_TYPES);
        DB::table('exam_groups')->insert(self::$EXAM_GROUPS);
        $examTypeIds = DB::table('exam_types')->pluck('id', 'code')->toArray();
        $examGroupIds = DB::table('exam_groups')->pluck('id')->toArray();

        $classIds = DB::table('classes')->pluck('id')->toArray();
        if (empty($classIds)) return;

        $exams = [];
        foreach ($classIds as $classId) {
            foreach ($examTypeIds as $typeCode => $typeId) {
                $startDate = $faker->dateTimeBetween('2024-08-01', '2025-02-28')->format('Y-m-d');
                $endDate = date('Y-m-d', strtotime($startDate . ' +5 days'));
                $exams[] = [
                    'exam_type_id'  => $typeId,
                    'class_id'      => $classId,
                    'exam_group_id' => $faker->randomElement($examGroupIds),
                    'name'          => ucfirst($typeCode) . ' Exam - Class ' . $classId,
                    'start_date'    => $startDate,
                    'end_date'      => $endDate,
                    'description'   => 'Auto seeded exam',
                    'is_published'  => true,
                    'exam_center'   => $faker->city,
                    'time_table'    => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }
        DB::table('exams')->insert($exams);
        $examIds = DB::table('exams')->pluck('id')->toArray();

        $subjects = DB::table('subjects')->get();
        if ($subjects->isEmpty()) return;

        $students = DB::table('students')->select('id', 'class_id')->get();
        if ($students->isEmpty()) return;

        $examMarks = [];
        foreach ($examIds as $examId) {
            $exam = DB::table('exams')->where('id', $examId)->first();
            if (!$exam) continue;
            foreach ($students as $student) {
                if ($student->class_id != $exam->class_id) continue;
                $randomSubjects = $subjects->random(min(5, $subjects->count()));
                foreach ($randomSubjects as $subject) {
                    $maxMarks = 100;
                    $passingMarks = 33;
                    $marksObtained = $faker->numberBetween(0, $maxMarks);
                    $examMarks[] = [
                        'exam_id'        => $examId,
                        'student_id'     => $student->id,
                        'subject_id'     => $subject->id,
                        'marks_obtained' => $marksObtained,
                        'max_marks'      => $maxMarks,
                        'passing_marks'  => $passingMarks,
                        'remarks'        => $marksObtained >= $passingMarks ? 'Pass' : 'Fail',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
            }
        }

        if (!empty($examMarks)) {
            foreach (array_chunk($examMarks, 500) as $chunk) {
                DB::table('exam_marks')->insert($chunk);
            }
        }
    }

    // ------------------------------------------------------------------
    // Grade Scales Seeding
    // ------------------------------------------------------------------
    protected function seedGradeScales()
    {
        if (DB::table('grade_scales')->count() == 0) {
            foreach (self::$GRADE_SCALES as $scale) {
                DB::table('grade_scales')->insert([
                    'name' => $scale['name'],
                    'grades' => $scale['grades'],
                    'is_default' => $scale['is_default'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    // ------------------------------------------------------------------
    // Exam Results Seeding (aggregated)
    // ------------------------------------------------------------------
    protected function seedExamResults()
    {
        $exams = DB::table('exams')->get();
        $students = DB::table('students')->get();
        $gradeScale = DB::table('grade_scales')->where('is_default', true)->first();
        $gradeScaleData = $gradeScale ? json_decode($gradeScale->grades, true) : [];

        $getGrade = function($percentage) use ($gradeScaleData) {
            foreach ($gradeScaleData as $range) {
                if ($percentage >= $range['min'] && $percentage <= $range['max']) {
                    return $range['grade'];
                }
            }
            return 'F';
        };

        DB::table('exam_results')->truncate();

        foreach ($exams as $exam) {
            $marks = DB::table('exam_marks')
                ->where('exam_id', $exam->id)
                ->get()
                ->groupBy('student_id');

            $studentResults = [];
            foreach ($marks as $studentId => $studentMarks) {
                $totalObtained = $studentMarks->sum('marks_obtained');
                $totalMax = $studentMarks->sum('max_marks');
                $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;
                $grade = $getGrade($percentage);
                $remarks = $percentage >= 40 ? 'Pass' : 'Fail';

                $student = $students->where('id', $studentId)->first();
                if (!$student) continue;

                $studentResults[] = [
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                    'class_id' => $student->class_id,
                    'section_id' => $student->section_id ?? null,
                    'total_marks' => $totalObtained,
                    'total_max_marks' => $totalMax,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'remarks' => $remarks,
                    'rank_in_class' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($studentResults)) {
                DB::table('exam_results')->insert($studentResults);
            }

            // Calculate ranks per class
            $classGroups = DB::table('exam_results')
                ->where('exam_id', $exam->id)
                ->orderBy('percentage', 'desc')
                ->get()
                ->groupBy('class_id');

            foreach ($classGroups as $classId => $classResults) {
                $rank = 1;
                $prevPercentage = null;
                foreach ($classResults as $index => $result) {
                    if ($prevPercentage !== null && $result->percentage < $prevPercentage) {
                        $rank = $index + 1;
                    }
                    DB::table('exam_results')
                        ->where('exam_id', $exam->id)
                        ->where('student_id', $result->student_id)
                        ->update(['rank_in_class' => $rank]);
                    $prevPercentage = $result->percentage;
                }
            }
        }

        $this->command->info('Exam results seeded with ranks.');
    }

    // ------------------------------------------------------------------
    // Subject Assignment, Timetable & Student Attendance
    // ------------------------------------------------------------------
    protected function seedSubjectAssignments()
    {
        $faker = Faker::create();

        $classes = DB::table('classes')->select('id')->get();
        $subjects = DB::table('subjects')->pluck('id')->toArray();
        $staff = DB::table('staff')->pluck('id')->toArray();

        if ($classes->isEmpty() || empty($subjects) || empty($staff)) {
            return;
        }

        $assignments = [];
        foreach ($classes as $class) {
            $numSubjects = rand(5, 8);
            $selectedSubjects = $faker->randomElements($subjects, min($numSubjects, count($subjects)));
            foreach ($selectedSubjects as $subjectId) {
                $assignments[] = [
                    'class_id'          => $class->id,
                    'subject_id'        => $subjectId,
                    'teacher_id'        => $faker->randomElement($staff),
                    'max_weekly_periods'=> rand(3, 6),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }

        if (!empty($assignments)) {
            DB::table('class_subject_teacher')->insert($assignments);
            $this->command->info('Seeded ' . count($assignments) . ' subject assignments.');
        }
    }

    protected function seedTimeTables()
    {
        $assignments = DB::table('class_subject_teacher')
            ->select('class_id', 'subject_id', 'teacher_id')
            ->get()
            ->groupBy('class_id');

        if ($assignments->isEmpty()) {
            return;
        }

        $timetables = [];
        $days = range(1, 6); // Monday to Saturday
        $periods = range(1, 6); // 6 periods per day

        foreach ($assignments as $classId => $subjectsList) {
            $subjectIds = $subjectsList->pluck('subject_id')->toArray();
            $teacherIds = $subjectsList->pluck('teacher_id')->toArray();
            if (empty($subjectIds)) continue;

            foreach ($days as $day) {
                foreach ($periods as $period) {
                    $idx = array_rand($subjectIds);
                    $subjectId = $subjectIds[$idx];
                    $teacherId = $teacherIds[$idx];
                    $startHour = 8 + floor(($period-1) / 2);
                    $startMin = ($period % 2 == 0) ? 30 : 0;
                    $endHour = $startHour + 1;
                    $endMin = $startMin + 30;
                    if ($endMin >= 60) {
                        $endMin -= 60;
                        $endHour++;
                    }
                    $startTime = sprintf('%02d:%02d:00', $startHour, $startMin);
                    $endTime = sprintf('%02d:%02d:00', $endHour, $endMin);

                    $timetables[] = [
                        'class_id'      => $classId,
                        'day_of_week'   => $day,
                        'period_number' => $period,
                        'start_time'    => $startTime,
                        'end_time'      => $endTime,
                        'subject_id'    => $subjectId,
                        'teacher_id'    => $teacherId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
            }
        }

        if (!empty($timetables)) {
            DB::table('time_tables')->truncate();
            foreach (array_chunk($timetables, 500) as $chunk) {
                DB::table('time_tables')->insert($chunk);
            }
            $this->command->info('Seeded ' . count($timetables) . ' timetable entries.');
        }
    }

    protected function seedStudentAttendance()
    {
        $students = DB::table('students')->select('id', 'class_id')->get();
        if ($students->isEmpty()) {
            return;
        }

        $attendance = [];
        $endDate = now();
        $startDate = now()->subDays(30);
        $dateRange = [];

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $dateRange[] = $date->toDateString();
        }

        foreach ($students as $student) {
            foreach ($dateRange as $date) {
                $dayOfWeek = (int) date('N', strtotime($date));
                $firstPeriod = DB::table('time_tables')
                    ->where('class_id', $student->class_id)
                    ->where('day_of_week', $dayOfWeek)
                    ->orderBy('period_number')
                    ->first();

                if (!$firstPeriod) continue;

                $faker = Faker::create();
                $status = $faker->randomElement(['present', 'absent', 'late', 'half_day']);
                $remarks = $status === 'present' ? null : $faker->sentence;

                $attendance[] = [
                    'student_id'   => $student->id,
                    'class_id'     => $student->class_id,
                    'subject_id'   => $firstPeriod->subject_id,
                    'teacher_id'   => $firstPeriod->teacher_id,
                    'date'         => $date,
                    'status'       => $status,
                    'remarks'      => $remarks,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        }

        if (!empty($attendance)) {
            DB::table('student_attendance')->truncate();
            foreach (array_chunk($attendance, 500) as $chunk) {
                DB::table('student_attendance')->insert($chunk);
            }
            $this->command->info('Seeded ' . count($attendance) . ' student attendance records.');
        }
    }

    // ------------------------------------------------------------------
    // MAIN RUN METHOD
    // ------------------------------------------------------------------
    public function run(): void
    {
        self::initStaticData();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'student_discounts', 'invoices', 'student_fee_installments', 'students',
            'classes', 'elective_tracks', 'streams', 'grades', 'academic_sessions',
            'banks', 'discounts', 'fee_submission_types', 'fee_types',
            'employee_categories', 'staff', 'attendances', 'leaves', 'salaries',
            'exam_types', 'exam_groups', 'exams', 'exam_marks', 'subjects',
            'class_subject_teacher', 'time_tables', 'student_attendance',
            'grade_scales', 'exam_results'   // added these two
        ];

        foreach ($tables as $table) {
            if (DB::table($table)->exists()) {
                DB::table($table)->truncate();
            }
        }

        // Insert core data
        DB::table('academic_sessions')->insert(self::$ACADEMIC_SESSIONS);
        DB::table('grades')->insert(self::$GRADES);
        DB::table('streams')->insert(self::$STREAMS);
        DB::table('elective_tracks')->insert(self::$ELECTIVE_TRACKS);
        DB::table('classes')->insert(self::$CLASSES);
        DB::table('banks')->insert(self::$BANKS);
        DB::table('discounts')->insert(self::$DISCOUNTS);
        DB::table('fee_submission_types')->insert(self::$FEE_SUBMISSION_TYPES);
        DB::table('fee_types')->insert(self::$FEE_TYPES_SIMPLE);
        DB::table('students')->insert(self::$STUDENTS);
        DB::table('student_fee_installments')->insert(self::$INSTALLMENTS);
        DB::table('invoices')->insert(self::$INVOICES);
        DB::table('student_discounts')->insert(self::$STUDENT_DISCOUNTS);

        // Subjects
        $this->seedSubjects();

        // Employee categories and staff
        $this->seedEmployeeCategories();
        $staffData = $this->generateStaff();
        DB::table('staff')->insert($staffData);
        $staffIds = DB::table('staff')->pluck('id')->toArray();

        // Staff attendance
        $attendanceData = $this->generateStaffAttendance($staffIds);
        foreach (array_chunk($attendanceData, 500) as $chunk) {
            DB::table('attendances')->insert($chunk);
        }

        // Exams and marks
        $this->generateExamsAndMarks();

        // Grade scales
        $this->seedGradeScales();

        // Exam results (aggregated)
        $this->seedExamResults();

        // Subject assignments (section-wise distribution)
        $this->seedSubjectAssignments();

        // Time tables
        $this->seedTimeTables();

        // Student attendance (based on first period)
        $this->seedStudentAttendance();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('All data seeded successfully: ' 
            . count(self::$STUDENTS) . ' students, '
            . count($staffData) . ' staff members, '
            . count(self::$SUBJECTS) . ' subjects, and related assignments, exams, and results.');
    }
}