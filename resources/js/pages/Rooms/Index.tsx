import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { RoomList } from '@/components/rooms/RoomList';
import { CreateRoom } from '@/components/rooms/CreateRoom';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

interface Room {
    id: number;
    name: string;
    description?: string;
    slug: string;
    is_public: boolean;
    member_count: number;
    is_member?: boolean;
    user_role?: string;
    created_at: string;
}

interface Props {
    rooms: {
        data: Room[];
        meta?: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        };
    };
}

export default function RoomsIndex({ rooms }: Props) {
    const [activeTab, setActiveTab] = useState('all');
    
    const publicRooms = rooms.data.filter(room => room.is_public);
    const myRooms = rooms.data.filter(room => room.is_member);

    const handleJoinRoom = async (roomId: number) => {
        try {
            const response = await fetch(route('api.v1.rooms.join'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'include',
                body: JSON.stringify({ room_id: roomId }),
            });

            if (response.ok) {
                router.reload({ only: ['rooms'] });
            }
        } catch (error) {
            console.error('Failed to join room:', error);
        }
    };

    const handleRoomCreated = () => {
        router.reload({ only: ['rooms'] });
    };

    return (
        <AppLayout>
            <Head title="Rooms" />
            
            <div className="container mx-auto py-6 px-4">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-3xl font-bold">Chat Rooms</h1>
                    <CreateRoom onRoomCreated={handleRoomCreated} />
                </div>

                <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                    <TabsList className="grid w-full grid-cols-3">
                        <TabsTrigger value="all">All Rooms</TabsTrigger>
                        <TabsTrigger value="public">Public Rooms</TabsTrigger>
                        <TabsTrigger value="my">My Rooms</TabsTrigger>
                    </TabsList>
                    
                    <TabsContent value="all" className="mt-6">
                        <RoomList 
                            rooms={rooms.data} 
                            onJoinRoom={handleJoinRoom}
                        />
                    </TabsContent>
                    
                    <TabsContent value="public" className="mt-6">
                        <RoomList 
                            rooms={publicRooms} 
                            onJoinRoom={handleJoinRoom}
                        />
                    </TabsContent>
                    
                    <TabsContent value="my" className="mt-6">
                        <RoomList 
                            rooms={myRooms} 
                            onJoinRoom={handleJoinRoom}
                            showJoinButton={false}
                        />
                    </TabsContent>
                </Tabs>

                {rooms.meta && rooms.meta.last_page > 1 && (
                    <div className="mt-8 flex justify-center gap-2">
                        {Array.from({ length: rooms.meta.last_page }, (_, i) => i + 1).map(page => (
                            <Button
                                key={page}
                                variant={page === rooms.meta!.current_page ? 'default' : 'outline'}
                                size="sm"
                                onClick={() => router.get(`/rooms?page=${page}`)}
                            >
                                {page}
                            </Button>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}