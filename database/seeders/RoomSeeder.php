<?php

namespace Database\Seeders;

use App\Domains\Rooms\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users if they don't exist
        $users = User::count() > 0 ? User::all() : User::factory(10)->create();
        
        // Create default public rooms
        $defaultRooms = [
            [
                'name' => 'General Discussion',
                'description' => 'A place for general conversations',
                'slug' => 'general',
            ],
            [
                'name' => 'Announcements',
                'description' => 'Important announcements and updates',
                'slug' => 'announcements',
            ],
            [
                'name' => 'Random',
                'description' => 'Off-topic discussions and fun',
                'slug' => 'random',
            ],
            [
                'name' => 'Tech Talk',
                'description' => 'Discuss technology and programming',
                'slug' => 'tech-talk',
            ],
        ];
        
        foreach ($defaultRooms as $roomData) {
            // Check if room already exists
            if (Room::where('slug', $roomData['slug'])->exists()) {
                continue;
            }
            
            $creator = $users->random();
            $room = Room::create([
                ...$roomData,
                'is_public' => true,
                'created_by_id' => $creator->id,
            ]);
            
            // Add creator as admin
            $room->addMember($creator, 'admin');
            
            // Add random members
            $memberCount = min(rand(3, 8), $users->count());
            if ($memberCount > 0) {
                $members = $users->random($memberCount);
                foreach ($members as $member) {
                    if (!$room->isMember($member)) {
                        $room->addMember($member);
                    }
                }
            }
        }
        
        // Create some additional random rooms
        Room::factory(5)->create()->each(function (Room $room) use ($users) {
            // Add creator as admin
            $creator = User::find($room->created_by_id);
            $room->addMember($creator, 'admin');
            
            // Add random members
            $memberCount = min(rand(2, 10), $users->count());
            if ($memberCount > 0) {
                $members = $users->random($memberCount);
                foreach ($members as $member) {
                    if (!$room->isMember($member)) {
                        $role = rand(1, 10) === 1 ? 'moderator' : 'member';
                        $room->addMember($member, $role);
                    }
                }
            }
        });
    }
}