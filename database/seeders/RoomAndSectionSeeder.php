<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\ClassSection;
use App\Models\Classes;

class RoomAndSectionSeeder extends Seeder
{
    public function run()
    {
        // 1. Insert 45 rooms (one for each section + buffer)
        $rooms = [
            // Your existing 25 rooms (keep them)
            ['name' => 'Room 101', 'room_number' => '101', 'block' => 'Block A', 'floor' => 0, 'type' => 'classroom', 'capacity' => 40, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Room 102', 'room_number' => '102', 'block' => 'Block A', 'floor' => 0, 'type' => 'classroom', 'capacity' => 40, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Room 103', 'room_number' => '103', 'block' => 'Block A', 'floor' => 0, 'type' => 'classroom', 'capacity' => 35, 'has_projector' => true, 'has_ac' => false, 'is_available' => true],
            ['name' => 'Room 104', 'room_number' => '104', 'block' => 'Block A', 'floor' => 0, 'type' => 'classroom', 'capacity' => 45, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Room 201', 'room_number' => '201', 'block' => 'Block A', 'floor' => 1, 'type' => 'classroom', 'capacity' => 40, 'has_projector' => true, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Room 202', 'room_number' => '202', 'block' => 'Block A', 'floor' => 1, 'type' => 'classroom', 'capacity' => 40, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Room 203', 'room_number' => '203', 'block' => 'Block A', 'floor' => 1, 'type' => 'classroom', 'capacity' => 30, 'has_projector' => false, 'has_ac' => false, 'is_available' => true],
            ['name' => 'Biology Lab', 'room_number' => 'B101', 'block' => 'Science Wing', 'floor' => 0, 'type' => 'lab', 'capacity' => 30, 'has_projector' => true, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Chemistry Lab', 'room_number' => 'C102', 'block' => 'Science Wing', 'floor' => 0, 'type' => 'lab', 'capacity' => 30, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Physics Lab', 'room_number' => 'P103', 'block' => 'Science Wing', 'floor' => 0, 'type' => 'lab', 'capacity' => 28, 'has_projector' => true, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Computer Lab 1', 'room_number' => 'CL1', 'block' => 'IT Wing', 'floor' => 1, 'type' => 'lab', 'capacity' => 30, 'has_projector' => true, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Computer Lab 2', 'room_number' => 'CL2', 'block' => 'IT Wing', 'floor' => 1, 'type' => 'lab', 'capacity' => 30, 'has_projector' => true, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Main Library', 'room_number' => 'LIB', 'block' => 'Main Building', 'floor' => 2, 'type' => 'library', 'capacity' => 120, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Junior Library', 'room_number' => 'JLIB', 'block' => 'Primary Block', 'floor' => 0, 'type' => 'library', 'capacity' => 60, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Auditorium', 'room_number' => 'AUD', 'block' => 'Main Building', 'floor' => 1, 'type' => 'auditorium', 'capacity' => 300, 'has_projector' => true, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Multi-Purpose Hall', 'room_number' => 'MPH', 'block' => 'Sports Complex', 'floor' => 0, 'type' => 'auditorium', 'capacity' => 200, 'has_projector' => false, 'has_ac' => false, 'is_available' => true],
            ['name' => 'Art Room', 'room_number' => 'ART', 'block' => 'Arts Block', 'floor' => 0, 'type' => 'activity', 'capacity' => 35, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Music Room', 'room_number' => 'MUS', 'block' => 'Arts Block', 'floor' => 1, 'type' => 'activity', 'capacity' => 30, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Dance Studio', 'room_number' => 'DNC', 'block' => 'Arts Block', 'floor' => 1, 'type' => 'activity', 'capacity' => 40, 'has_projector' => true, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Conference Room', 'room_number' => 'CONF', 'block' => 'Admin Block', 'floor' => 2, 'type' => 'other', 'capacity' => 20, 'has_projector' => true, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Staff Room', 'room_number' => 'STAFF', 'block' => 'Admin Block', 'floor' => 1, 'type' => 'other', 'capacity' => 50, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Medical Room', 'room_number' => 'MED', 'block' => 'Main Building', 'floor' => 0, 'type' => 'other', 'capacity' => 10, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Examination Hall 1', 'room_number' => 'EX1', 'block' => 'Main Building', 'floor' => 2, 'type' => 'auditorium', 'capacity' => 150, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Examination Hall 2', 'room_number' => 'EX2', 'block' => 'Main Building', 'floor' => 3, 'type' => 'auditorium', 'capacity' => 150, 'has_projector' => false, 'has_ac' => true, 'is_available' => true],
            ['name' => 'Store Room', 'room_number' => 'STOR', 'block' => 'Ground Floor', 'floor' => 0, 'type' => 'other', 'capacity' => 5, 'has_projector' => false, 'has_ac' => false, 'is_available' => true],
        ];

        // Add 20 extra rooms to reach 45
        for ($i = 26; $i <= 45; $i++) {
            $rooms[] = [
                'name' => "Extra Room $i",
                'room_number' => "XR$i",
                'block' => 'Extension Wing',
                'floor' => (int)($i / 10),
                'type' => 'classroom',
                'capacity' => rand(35, 45),
                'has_projector' => false,
                'has_ac' => true,
                'is_available' => true,
            ];
        }

        foreach ($rooms as $room) {
            Room::updateOrCreate(['room_number' => $room['room_number']], $room);
        }

      
    }
}