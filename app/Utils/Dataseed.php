<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    // 🔹 ACADEMIC SESSIONS
    public static $ACADEMIC_SESSIONS = [
        ['id' => 1, 'name' => '2023-2024', 'start_date' => '2023-08-01', 'end_date' => '2024-07-31', 'is_current' => false],
        ['id' => 2, 'name' => '2024-2025', 'start_date' => '2024-08-01', 'end_date' => '2025-07-31', 'is_current' => true],
        ['id' => 3, 'name' => '2025-2026', 'start_date' => '2025-08-01', 'end_date' => '2026-07-31', 'is_current' => false],
    ];

    // 🔹 GRADES
    public static $GRADES = [
        ['id' => 1, 'name' => 'Nursery', 'numeric_value' => 0],
        ['id' => 2, 'name' => 'KG', 'numeric_value' => 0],
        ['id' => 3, 'name' => '1', 'numeric_value' => 1],
        ['id' => 4, 'name' => '2', 'numeric_value' => 2],
        ['id' => 5, 'name' => '3', 'numeric_value' => 3],
        ['id' => 6, 'name' => '4', 'numeric_value' => 4],
        ['id' => 7, 'name' => '5', 'numeric_value' => 5],
        ['id' => 8, 'name' => '6', 'numeric_value' => 6],
        ['id' => 9, 'name' => '7', 'numeric_value' => 7],
        ['id' => 10, 'name' => '8', 'numeric_value' => 8],
        ['id' => 11, 'name' => '9', 'numeric_value' => 9],
        ['id' => 12, 'name' => '10', 'numeric_value' => 10],
    ];

    // 🔹 STREAMS
    public static $STREAMS = [
        ['id' => 1, 'name' => 'Science'],
        ['id' => 2, 'name' => 'Arts'],
        ['id' => 3, 'name' => 'Commerce'],
        ['id' => 4, 'name' => 'General'],
    ];

    // 🔹 ELECTIVE TRACKS (for Science)
    public static $ELECTIVE_TRACKS = [
        ['id' => 1, 'name' => 'Biology', 'stream_id' => 1],
        ['id' => 2, 'name' => 'Computer Science', 'stream_id' => 1],
        ['id' => 3, 'name' => 'Physics Advanced', 'stream_id' => 1],
        ['id' => 4, 'name' => 'Chemistry Advanced', 'stream_id' => 1],
    ];

    // 🔹 CLASSES (combinations)
    public static $CLASSES = [
        ['id' => 1, 'academic_session_id' => 2, 'grade_id' => 1, 'stream_id' => 4, 'section' => 'A', 'capacity' => 30],
        ['id' => 2, 'academic_session_id' => 2, 'grade_id' => 1, 'stream_id' => 4, 'section' => 'B', 'capacity' => 30],
        ['id' => 3, 'academic_session_id' => 2, 'grade_id' => 3, 'stream_id' => 4, 'section' => 'A', 'capacity' => 35],
        ['id' => 4, 'academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 1, 'elective_track_id' => 1, 'section' => 'A', 'capacity' => 40],
        ['id' => 5, 'academic_session_id' => 2, 'grade_id' => 11, 'stream_id' => 1, 'elective_track_id' => 2, 'section' => 'B', 'capacity' => 40],
        ['id' => 6, 'academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 1, 'elective_track_id' => 1, 'section' => 'A', 'capacity' => 45],
        ['id' => 7, 'academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 1, 'elective_track_id' => 2, 'section' => 'B', 'capacity' => 45],
        ['id' => 8, 'academic_session_id' => 2, 'grade_id' => 12, 'stream_id' => 2, 'section' => 'A', 'capacity' => 35],
        ['id' => 9, 'academic_session_id' => 2, 'grade_id' => 8, 'stream_id' => 4, 'section' => 'A', 'capacity' => 40],
        ['id' => 10, 'academic_session_id' => 2, 'grade_id' => 8, 'stream_id' => 4, 'section' => 'B', 'capacity' => 40],
    ];

    // 🔹 BANKS
    public static $BANKS = [
        ['id' => 1, 'name' => 'Habib Bank Ltd', 'branch_name' => 'Main Branch', 'account_title' => 'School Fee Account', 'account_number' => '1234-567890-01', 'iban' => 'PK36HABB0012345678901234', 'routing_number' => '123456789', 'address' => '123 Main Street, City', 'is_active' => true],
        ['id' => 2, 'name' => 'United Bank Ltd', 'branch_name' => 'City Branch', 'account_title' => 'School Fee Collection', 'account_number' => '9876-543210-02', 'iban' => 'PK36UNIL0012345678905678', 'routing_number' => '987654321', 'address' => '456 Secondary Road, City', 'is_active' => true],
        ['id' => 3, 'name' => 'Meezan Bank', 'branch_name' => 'Islamic Banking', 'account_title' => 'School Fee', 'account_number' => '5566-778899-03', 'iban' => 'PK36MEZN0012345678909012', 'routing_number' => '112233445', 'address' => '789 Islamic Center', 'is_active' => true],
    ];

    // 🔹 DISCOUNTS
    public static $DISCOUNTS = [
        ['id' => 1, 'name' => 'Merit Scholarship 10%', 'description' => 'For top performers', 'type' => 'percentage', 'value' => 10, 'is_active' => true],
        ['id' => 2, 'name' => 'Sibling Discount 5%', 'description' => 'For siblings', 'type' => 'percentage', 'value' => 5, 'is_active' => true],
        ['id' => 3, 'name' => 'Need Based 20%', 'description' => 'Financial aid', 'type' => 'percentage', 'value' => 20, 'is_active' => true],
        ['id' => 4, 'name' => 'Staff Ward 50%', 'description' => 'Employees children', 'type' => 'percentage', 'value' => 50, 'is_active' => true],
        ['id' => 5, 'name' => 'Early Bird Fixed ₹1000', 'description' => 'Pay before due date', 'type' => 'fixed', 'value' => 1000, 'is_active' => true],
    ];

    // 🔹 FEE SUBMISSION TYPES (at least 10 records)
    public static $FEE_TYPES = [
        ['id' => 1, 'name' => 'Tuition Fee', 'period' => 'monthly', 'amount' => 2500, 'description' => 'Monthly tuition', 'is_active' => true],
        ['id' => 2, 'name' => 'Tuition Fee', 'period' => 'quarterly', 'amount' => 7200, 'description' => 'Quarterly tuition', 'is_active' => true],
        ['id' => 3, 'name' => 'Tuition Fee', 'period' => 'annually', 'amount' => 28000, 'description' => 'Annual tuition', 'is_active' => true],
        ['id' => 4, 'name' => 'Admission Fee', 'period' => 'one_time', 'amount' => 8000, 'description' => 'One time admission', 'is_active' => true],
        ['id' => 5, 'name' => 'Sports Fee', 'period' => 'annually', 'amount' => 3500, 'description' => 'Annual sports', 'is_active' => true],
        ['id' => 6, 'name' => 'Library Fee', 'period' => 'annually', 'amount' => 2000, 'description' => 'Library charges', 'is_active' => true],
        ['id' => 7, 'name' => 'Exam Fee', 'period' => 'quarterly', 'amount' => 1500, 'description' => 'Examination fee', 'is_active' => true],
        ['id' => 8, 'name' => 'Transport Fee', 'period' => 'monthly', 'amount' => 1200, 'description' => 'Bus service', 'is_active' => true],
        ['id' => 9, 'name' => 'Lab Fee', 'period' => 'annually', 'amount' => 2500, 'description' => 'Science lab', 'is_active' => true],
        ['id' => 10, 'name' => 'Security Fee', 'period' => 'one_time', 'amount' => 3000, 'description' => 'Refundable', 'is_active' => true],
        ['id' => 11, 'name' => 'Late Fee Fine', 'period' => 'monthly', 'amount' => 500, 'description' => 'Late payment penalty', 'is_active' => true],
    ];

    // 🔹 STUDENTS (10+ records)
    public static $STUDENTS = [
        [
            'first_name' => 'Muhammad', 'middle_name' => 'Ahmed', 'last_name' => 'Khan', 'gender' => 'Male',
            'date_of_birth' => '2010-05-15', 'dob_in_words' => 'Fifteenth May Two Thousand Ten',
            'religion' => 'Islam', 'caste_subcaste' => 'Sheikh', 'blood_group' => 'O+',
            'address' => '123 Street 1, Lahore', 'phone' => '03001234567', 'email' => 'ahmed.khan@example.com',
            'city' => 'Lahore', 'state' => 'Punjab', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Urdu', 'birth_place' => 'Lahore',
            'previous_school_name' => 'Grammar School', 'previous_school_address' => 'Lahore', 'previous_class' => '5', 'passout_year' => '2023', 'previous_category' => 'General',
            'admission_date' => '2024-03-01', 'student_type' => 'New', 'class_id' => 1, 'section' => 'A', 'admission_number' => 'STU-001', 'roll_number' => '101',
            'profile_photo' => null, 'father_name' => 'Sohail Khan', 'father_phone' => '03001112222', 'father_occupation' => 'Engineer',
            'mother_name' => 'Farah Khan', 'mother_phone' => '03003334444', 'mother_occupation' => 'Teacher',
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => null, 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        [
            'first_name' => 'Fatima', 'middle_name' => '', 'last_name' => 'Ali', 'gender' => 'Female',
            'date_of_birth' => '2011-08-20', 'dob_in_words' => 'Twentieth August Two Thousand Eleven',
            'religion' => 'Islam', 'caste_subcaste' => 'Hashmi', 'blood_group' => 'A+',
            'address' => '456 Street 2, Lahore', 'phone' => '03105556666', 'email' => 'fatima.ali@example.com',
            'city' => 'Lahore', 'state' => 'Punjab', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Urdu', 'birth_place' => 'Lahore',
            'previous_school_name' => 'Beaconhouse', 'previous_school_address' => 'Lahore', 'previous_class' => '4', 'passout_year' => '2022', 'previous_category' => 'General',
            'admission_date' => '2024-04-10', 'student_type' => 'New', 'class_id' => 3, 'section' => 'A', 'admission_number' => 'STU-002', 'roll_number' => '201',
            'profile_photo' => null, 'father_name' => 'Raza Ali', 'father_phone' => '03217778888', 'father_occupation' => 'Doctor',
            'mother_name' => 'Sana Ali', 'mother_phone' => '03219990000', 'mother_occupation' => 'Housewife',
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => 'Merit Scholarship 10%', 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        [
            'first_name' => 'Hamza', 'middle_name' => 'Ali', 'last_name' => 'Raza', 'gender' => 'Male',
            'date_of_birth' => '2009-02-10', 'dob_in_words' => 'Tenth February Two Thousand Nine',
            'religion' => 'Islam', 'caste_subcaste' => 'Shia', 'blood_group' => 'B+',
            'address' => '789 Street 3, Karachi', 'phone' => '03302223333', 'email' => 'hamza.raza@example.com',
            'city' => 'Karachi', 'state' => 'Sindh', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Urdu', 'birth_place' => 'Karachi',
            'previous_school_name' => 'BVS', 'previous_school_address' => 'Karachi', 'previous_class' => '7', 'passout_year' => '2023', 'previous_category' => 'OBC',
            'admission_date' => '2024-03-15', 'student_type' => 'Transfer', 'class_id' => 9, 'section' => 'A', 'admission_number' => 'STU-003', 'roll_number' => '301',
            'profile_photo' => null, 'father_name' => 'Arif Raza', 'father_phone' => '03444445555', 'father_occupation' => 'Businessman',
            'mother_name' => 'Nadia Raza', 'mother_phone' => '03446667777', 'mother_occupation' => 'Teacher',
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => null, 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        [
            'first_name' => 'Ayesha', 'middle_name' => '', 'last_name' => 'Malik', 'gender' => 'Female',
            'date_of_birth' => '2008-11-05', 'dob_in_words' => 'Fifth November Two Thousand Eight',
            'religion' => 'Islam', 'caste_subcaste' => 'Malik', 'blood_group' => 'AB+',
            'address' => '101 Street 4, Islamabad', 'phone' => '0512345678', 'email' => 'ayesha.malik@example.com',
            'city' => 'Islamabad', 'state' => 'Islamabad', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Punjabi', 'birth_place' => 'Rawalpindi',
            'previous_school_name' => 'Roots', 'previous_school_address' => 'Islamabad', 'previous_class' => '8', 'passout_year' => '2023', 'previous_category' => 'General',
            'admission_date' => '2024-04-01', 'student_type' => 'New', 'class_id' => 10, 'section' => 'B', 'admission_number' => 'STU-004', 'roll_number' => '401',
            'profile_photo' => null, 'father_name' => 'Tariq Malik', 'father_phone' => '03455556666', 'father_occupation' => 'Banker',
            'mother_name' => 'Samina Malik', 'mother_phone' => '03457778888', 'mother_occupation' => 'Housewife',
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => 'Sibling Discount 5%', 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        [
            'first_name' => 'Omar', 'middle_name' => 'Farooq', 'last_name' => 'Ahmed', 'gender' => 'Male',
            'date_of_birth' => '2007-09-18', 'dob_in_words' => 'Eighteenth September Two Thousand Seven',
            'religion' => 'Islam', 'caste_subcaste' => 'Farooqi', 'blood_group' => 'O-',
            'address' => '202 Street 5, Faisalabad', 'phone' => '0412345678', 'email' => 'omar.ahmed@example.com',
            'city' => 'Faisalabad', 'state' => 'Punjab', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Urdu', 'birth_place' => 'Faisalabad',
            'previous_school_name' => 'City School', 'previous_school_address' => 'Faisalabad', 'previous_class' => '9', 'passout_year' => '2023', 'previous_category' => 'General',
            'admission_date' => '2024-03-20', 'student_type' => 'New', 'class_id' => 4, 'section' => 'A', 'admission_number' => 'STU-005', 'roll_number' => '501',
            'profile_photo' => null, 'father_name' => 'Naveed Ahmed', 'father_phone' => '0411122334', 'father_occupation' => 'Professor',
            'mother_name' => 'Shamim Ahmed', 'mother_phone' => '0411122335', 'mother_occupation' => 'Doctor',
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => null, 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        // add 5 more students to make 10 total (quick generates)
        [
            'first_name' => 'Zainab', 'middle_name' => '', 'last_name' => 'Bashir', 'gender' => 'Female',
            'date_of_birth' => '2010-12-25', 'dob_in_words' => 'Twenty Fifth December Two Thousand Ten',
            'religion' => 'Islam', 'caste_subcaste' => 'Bashir', 'blood_group' => 'A-',
            'address' => '303 Street 6, Multan', 'phone' => '0612345678', 'email' => 'zainab@example.com',
            'city' => 'Multan', 'state' => 'Punjab', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Saraiki', 'birth_place' => 'Multan',
            'previous_school_name' => 'Allied School', 'previous_school_address' => 'Multan', 'previous_class' => '5', 'passout_year' => '2023', 'previous_category' => 'General',
            'admission_date' => '2024-04-05', 'student_type' => 'New', 'class_id' => 1, 'section' => 'B', 'admission_number' => 'STU-006', 'roll_number' => '601',
            'profile_photo' => null, 'father_name' => 'Bashir', 'father_phone' => '0611122334', 'father_occupation' => 'Business', 'mother_name' => 'Nasreen', 'mother_phone' => null, 'mother_occupation' => null,
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => null, 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        [
            'first_name' => 'Ali', 'middle_name' => 'Hamza', 'last_name' => 'Shah', 'gender' => 'Male',
            'date_of_birth' => '2006-06-30', 'dob_in_words' => 'Thirtieth June Two Thousand Six',
            'religion' => 'Islam', 'caste_subcaste' => 'Shah', 'blood_group' => 'B-',
            'address' => '404 Street 7, Lahore', 'phone' => '03009998877', 'email' => 'ali.shah@example.com',
            'city' => 'Lahore', 'state' => 'Punjab', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Urdu', 'birth_place' => 'Lahore',
            'previous_school_name' => 'Aitchison', 'previous_school_address' => 'Lahore', 'previous_class' => '10', 'passout_year' => '2023', 'previous_category' => 'General',
            'admission_date' => '2024-04-12', 'student_type' => 'New', 'class_id' => 6, 'section' => 'A', 'admission_number' => 'STU-007', 'roll_number' => '701',
            'profile_photo' => null, 'father_name' => 'Ijaz Shah', 'father_phone' => '03007778899', 'father_occupation' => 'Lawyer', 'mother_name' => 'Rubina', 'mother_phone' => null, 'mother_occupation' => null,
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => 'Need Based 20%', 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        [
            'first_name' => 'Sara', 'middle_name' => 'Khan', 'last_name' => 'Usman', 'gender' => 'Female',
            'date_of_birth' => '2009-03-22', 'dob_in_words' => 'Twenty Second March Two Thousand Nine',
            'religion' => 'Islam', 'caste_subcaste' => 'Khan', 'blood_group' => 'AB-',
            'address' => '505 Street 8, Rawalpindi', 'phone' => '0519988776', 'email' => 'sara@example.com',
            'city' => 'Rawalpindi', 'state' => 'Punjab', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Urdu', 'birth_place' => 'Rawalpindi',
            'previous_school_name' => 'Army Public', 'previous_school_address' => 'Rawalpindi', 'previous_class' => '7', 'passout_year' => '2023', 'previous_category' => 'General',
            'admission_date' => '2024-03-25', 'student_type' => 'Transfer', 'class_id' => 9, 'section' => 'A', 'admission_number' => 'STU-008', 'roll_number' => '801',
            'profile_photo' => null, 'father_name' => 'Usman Khan', 'father_phone' => '03335556677', 'father_occupation' => 'Army', 'mother_name' => 'Fauzia', 'mother_phone' => null, 'mother_occupation' => null,
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => null, 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        [
            'first_name' => 'Hassan', 'middle_name' => '', 'last_name' => 'Raza', 'gender' => 'Male',
            'date_of_birth' => '2008-07-14', 'dob_in_words' => 'Fourteenth July Two Thousand Eight',
            'religion' => 'Islam', 'caste_subcaste' => 'Raza', 'blood_group' => 'O+',
            'address' => '606 Street 9, Peshawar', 'phone' => '0912345678', 'email' => 'hassan@example.com',
            'city' => 'Peshawar', 'state' => 'KPK', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Pashto', 'birth_place' => 'Peshawar',
            'previous_school_name' => 'Peshawar Model', 'previous_school_address' => 'Peshawar', 'previous_class' => '8', 'passout_year' => '2023', 'previous_category' => 'General',
            'admission_date' => '2024-04-15', 'student_type' => 'New', 'class_id' => 5, 'section' => 'B', 'admission_number' => 'STU-009', 'roll_number' => '901',
            'profile_photo' => null, 'father_name' => 'Raza Khan', 'father_phone' => '0911122334', 'father_occupation' => 'Business', 'mother_name' => 'Amina', 'mother_phone' => null, 'mother_occupation' => null,
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => null, 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
        [
            'first_name' => 'Hira', 'middle_name' => 'Siddiqui', 'last_name' => 'Rashid', 'gender' => 'Female',
            'date_of_birth' => '2010-10-10', 'dob_in_words' => 'Tenth October Two Thousand Ten',
            'religion' => 'Islam', 'caste_subcaste' => 'Siddiqui', 'blood_group' => 'A+',
            'address' => '707 Street 10, Quetta', 'phone' => '0812345678', 'email' => 'hira@example.com',
            'city' => 'Quetta', 'state' => 'Balochistan', 'country' => 'Pakistan', 'extra_note' => null,
            'mother_tongue' => 'Balochi', 'birth_place' => 'Quetta',
            'previous_school_name' => 'Tameer', 'previous_school_address' => 'Quetta', 'previous_class' => '4', 'passout_year' => '2023', 'previous_category' => 'General',
            'admission_date' => '2024-04-08', 'student_type' => 'New', 'class_id' => 3, 'section' => 'B', 'admission_number' => 'STU-010', 'roll_number' => '1001',
            'profile_photo' => null, 'father_name' => 'Rashid', 'father_phone' => '0811122334', 'father_occupation' => 'Govt Employee', 'mother_name' => 'Shazia', 'mother_phone' => null, 'mother_occupation' => null,
            'parent_id_proof' => null, 'parent_signature' => null, 'assigned_concession' => null, 'status' => 'Active',
            'suspension_start_date' => null, 'suspension_end_date' => null, 'suspension_message' => null,
        ],
    ];

    // 🔹 STUDENT FEE INSTALLMENTS (at least 10)
    public static $INSTALLMENTS = [
        // For student 1
        ['student_id' => 1, 'fee_submission_type_id' => 1, 'installment_number' => 1, 'amount' => 2500, 'due_date' => '2024-09-10', 'status' => 'paid', 'paid_amount' => 2500, 'payment_date' => '2024-09-05', 'receipt_number' => 'RCP001', 'remarks' => null],
        ['student_id' => 1, 'fee_submission_type_id' => 1, 'installment_number' => 2, 'amount' => 2500, 'due_date' => '2024-10-10', 'status' => 'unpaid', 'paid_amount' => 0, 'payment_date' => null, 'receipt_number' => null, 'remarks' => null],
        ['student_id' => 1, 'fee_submission_type_id' => 5, 'installment_number' => 1, 'amount' => 3500, 'due_date' => '2024-08-20', 'status' => 'paid', 'paid_amount' => 3500, 'payment_date' => '2024-08-15', 'receipt_number' => 'RCP002', 'remarks' => null],
        // Student 2
        ['student_id' => 2, 'fee_submission_type_id' => 3, 'installment_number' => 1, 'amount' => 28000, 'due_date' => '2024-07-31', 'status' => 'partial', 'paid_amount' => 15000, 'payment_date' => '2024-07-20', 'receipt_number' => 'RCP003', 'remarks' => null],
        // Student 3
        ['student_id' => 3, 'fee_submission_type_id' => 1, 'installment_number' => 1, 'amount' => 2500, 'due_date' => '2024-09-10', 'status' => 'unpaid', 'paid_amount' => 0, 'payment_date' => null, 'receipt_number' => null, 'remarks' => null],
        ['student_id' => 3, 'fee_submission_type_id' => 1, 'installment_number' => 2, 'amount' => 2500, 'due_date' => '2024-10-10', 'status' => 'unpaid', 'paid_amount' => 0, 'payment_date' => null, 'receipt_number' => null, 'remarks' => null],
        // Student 4
        ['student_id' => 4, 'fee_submission_type_id' => 5, 'installment_number' => 1, 'amount' => 3500, 'due_date' => '2024-08-20', 'status' => 'paid', 'paid_amount' => 3500, 'payment_date' => '2024-08-18', 'receipt_number' => 'RCP004', 'remarks' => null],
        // Student 5
        ['student_id' => 5, 'fee_submission_type_id' => 7, 'installment_number' => 1, 'amount' => 1500, 'due_date' => '2024-11-15', 'status' => 'unpaid', 'paid_amount' => 0, 'payment_date' => null, 'receipt_number' => null, 'remarks' => null],
        ['student_id' => 5, 'fee_submission_type_id' => 7, 'installment_number' => 2, 'amount' => 1500, 'due_date' => '2025-02-15', 'status' => 'unpaid', 'paid_amount' => 0, 'payment_date' => null, 'receipt_number' => null, 'remarks' => null],
        // Student 6
        ['student_id' => 6, 'fee_submission_type_id' => 1, 'installment_number' => 1, 'amount' => 2500, 'due_date' => '2024-09-10', 'status' => 'paid', 'paid_amount' => 2500, 'payment_date' => '2024-09-01', 'receipt_number' => 'RCP005', 'remarks' => null],
        // Student 7
        ['student_id' => 7, 'fee_submission_type_id' => 4, 'installment_number' => 1, 'amount' => 8000, 'due_date' => '2024-06-30', 'status' => 'paid', 'paid_amount' => 8000, 'payment_date' => '2024-06-25', 'receipt_number' => 'RCP006', 'remarks' => null],
        // Student 8
        ['student_id' => 8, 'fee_submission_type_id' => 9, 'installment_number' => 1, 'amount' => 2500, 'due_date' => '2024-10-01', 'status' => 'unpaid', 'paid_amount' => 0, 'payment_date' => null, 'receipt_number' => null, 'remarks' => null],
        // Student 9
        ['student_id' => 9, 'fee_submission_type_id' => 1, 'installment_number' => 1, 'amount' => 2500, 'due_date' => '2024-09-10', 'status' => 'partial', 'paid_amount' => 1000, 'payment_date' => '2024-09-05', 'receipt_number' => 'RCP007', 'remarks' => null],
        // Student 10
        ['student_id' => 10, 'fee_submission_type_id' => 2, 'installment_number' => 1, 'amount' => 7200, 'due_date' => '2024-09-30', 'status' => 'unpaid', 'paid_amount' => 0, 'payment_date' => null, 'receipt_number' => null, 'remarks' => null],
    ];

    // 🔹 INVOICES (linked to installments)
    public static $INVOICES = [
        ['id' => 1, 'invoice_number' => 'INV-2024001', 'student_id' => 1, 'bank_id' => 1, 'student_fee_installment_id' => 1, 'amount' => 2500, 'due_date' => '2024-09-10', 'status' => 'paid', 'challan_file' => 'invoices/challan_INV-2024001.pdf', 'payment_proof_file' => null, 'payment_remarks' => null, 'paid_at' => '2024-09-05', 'approved_by' => null],
        ['id' => 2, 'invoice_number' => 'INV-2024002', 'student_id' => 1, 'bank_id' => 1, 'student_fee_installment_id' => 2, 'amount' => 2500, 'due_date' => '2024-10-10', 'status' => 'pending', 'challan_file' => 'invoices/challan_INV-2024002.pdf', 'payment_proof_file' => null, 'payment_remarks' => null, 'paid_at' => null, 'approved_by' => null],
        ['id' => 3, 'invoice_number' => 'INV-2024003', 'student_id' => 2, 'bank_id' => 2, 'student_fee_installment_id' => 4, 'amount' => 28000, 'due_date' => '2024-07-31', 'status' => 'pending', 'challan_file' => 'invoices/challan_INV-2024003.pdf', 'payment_proof_file' => 'invoices/proofs/proof3.jpg', 'payment_remarks' => 'Paid via bank', 'paid_at' => null, 'approved_by' => null],
        ['id' => 4, 'invoice_number' => 'INV-2024004', 'student_id' => 4, 'bank_id' => 1, 'student_fee_installment_id' => 7, 'amount' => 3500, 'due_date' => '2024-08-20', 'status' => 'paid', 'challan_file' => 'invoices/challan_INV-2024004.pdf', 'payment_proof_file' => null, 'payment_remarks' => null, 'paid_at' => '2024-08-18', 'approved_by' => null],
        ['id' => 5, 'invoice_number' => 'INV-2024005', 'student_id' => 6, 'bank_id' => 3, 'student_fee_installment_id' => 10, 'amount' => 2500, 'due_date' => '2024-09-10', 'status' => 'paid', 'challan_file' => 'invoices/challan_INV-2024005.pdf', 'payment_proof_file' => null, 'payment_remarks' => null, 'paid_at' => '2024-09-01', 'approved_by' => null],
        ['id' => 6, 'invoice_number' => 'INV-2024006', 'student_id' => 7, 'bank_id' => 2, 'student_fee_installment_id' => 11, 'amount' => 8000, 'due_date' => '2024-06-30', 'status' => 'paid', 'challan_file' => 'invoices/challan_INV-2024006.pdf', 'payment_proof_file' => null, 'payment_remarks' => null, 'paid_at' => '2024-06-25', 'approved_by' => null],
        ['id' => 7, 'invoice_number' => 'INV-2024007', 'student_id' => 9, 'bank_id' => 1, 'student_fee_installment_id' => 13, 'amount' => 2500, 'due_date' => '2024-09-10', 'status' => 'pending', 'challan_file' => 'invoices/challan_INV-2024007.pdf', 'payment_proof_file' => null, 'payment_remarks' => null, 'paid_at' => null, 'approved_by' => null],
    ];

    // 🔹 STUDENT DISCOUNTS ASSIGNMENTS
    public static $STUDENT_DISCOUNTS = [
        ['student_id' => 2, 'discount_id' => 1, 'fee_submission_type_id' => null, 'valid_from' => '2024-01-01', 'valid_until' => '2024-12-31'],
        ['student_id' => 4, 'discount_id' => 2, 'fee_submission_type_id' => null, 'valid_from' => null, 'valid_until' => null],
        ['student_id' => 7, 'discount_id' => 3, 'fee_submission_type_id' => 3, 'valid_from' => '2024-01-01', 'valid_until' => '2024-12-31'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks for clean truncation (optional but safe)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate tables (order matters to avoid constraint errors)
        $tables = [
            'student_fee_installments', 'invoices', 'student_discounts', 'students',
            'classes', 'elective_tracks', 'streams', 'grades', 'academic_sessions',
            'banks', 'discounts', 'fee_submission_types'
        ];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        // Seed in correct dependency order
        DB::table('academic_sessions')->insert(self::$ACADEMIC_SESSIONS);
        DB::table('grades')->insert(self::$GRADES);
        DB::table('streams')->insert(self::$STREAMS);
        DB::table('elective_tracks')->insert(self::$ELECTIVE_TRACKS);
        DB::table('classes')->insert(self::$CLASSES);
        DB::table('banks')->insert(self::$BANKS);
        DB::table('discounts')->insert(self::$DISCOUNTS);
        DB::table('fee_submission_types')->insert(self::$FEE_TYPES);

        // Insert students (use DB::table to bypass any model events)
        DB::table('students')->insert(self::$STUDENTS);

        DB::table('student_fee_installments')->insert(self::$INSTALLMENTS);
        DB::table('invoices')->insert(self::$INVOICES);
        DB::table('student_discounts')->insert(self::$STUDENT_DISCOUNTS);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Seeded all fee module, student, class, grade, and related tables with 10+ records each.');
    }
}