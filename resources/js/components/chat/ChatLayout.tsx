import { Button } from '@/components/ui/button';
import { useIsMobile } from '@/hooks/use-mobile';
import api from '@/lib/api';
import { Room } from '@/types/messages';
import { router } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useEffect, useState } from 'react';
import ChatWindow from './ChatWindow';
import RoomList from './RoomList';

interface ChatLayoutProps {
    initialRooms?: Room[];
    selectedRoomId?: number | null;
}

export default function ChatLayout({ initialRooms = [], selectedRoomId }: ChatLayoutProps) {
    const [rooms, setRooms] = useState<Room[]>(initialRooms);
    const [selectedRoom, setSelectedRoom] = useState<Room | null>(null);
    const isMobile = useIsMobile();

    useEffect(() => {
        fetchRooms();
    }, []);

    useEffect(() => {
        // If we have a selectedRoomId from the URL, find and select that room
        if (selectedRoomId && rooms.length > 0) {
            const room = rooms.find((r) => r.id === selectedRoomId);
            if (room) {
                setSelectedRoom(room);
            }
        }
    }, [selectedRoomId, rooms]);

    const fetchRooms = async () => {
        try {
            const response = await api.get(route('api.rooms.index'));
            setRooms(response.data);
        } catch (error) {
            console.error('Failed to fetch rooms:', error);
        }
    };

    const handleRoomSelect = (room: Room) => {
        // Navigate to the room URL
        router.visit(route('chat.room', { room: room.id }), {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleRoomCreate = async (name: string, type: 'public' | 'private') => {
        try {
            const response = await api.post(route('api.rooms.store'), {
                name,
                type,
                isPrivate: type === 'private',
            });

            const newRoom = response.data;
            console.log('Room created:', newRoom);
            // Navigate to the new room
            router.visit(route('chat.room', { room: newRoom.id }));
        } catch (error) {
            console.error('Failed to create room:', error);
        }
    };

    const handleBackToRooms = () => {
        router.visit(route('chat'), {
            preserveState: true,
        });
    };

    // Mobile layout: Show either room list OR chat window
    if (isMobile) {
        return (
            <div className="h-full bg-zinc-50 dark:bg-zinc-900">
                {selectedRoom ? (
                    <div className="flex h-full flex-col">
                        {/* Mobile chat header with back button */}
                        <div className="flex items-center border-b border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
                            <Button variant="ghost" size="icon" onClick={handleBackToRooms} className="mr-3">
                                <ArrowLeft className="h-5 w-5" />
                            </Button>
                            <div className="flex-1">
                                <h3 className="text-base font-semibold text-zinc-900 dark:text-white">{selectedRoom.name}</h3>
                                {selectedRoom.users && (
                                    <p className="text-xs text-zinc-500 dark:text-zinc-400">{selectedRoom.users.length} members</p>
                                )}
                            </div>
                        </div>
                        {/* Chat window takes full height minus header */}
                        <div className="flex-1 overflow-hidden">
                            <ChatWindow room={selectedRoom} isMobile={true} />
                        </div>
                    </div>
                ) : (
                    <RoomList rooms={rooms} selectedRoom={selectedRoom} onRoomSelect={handleRoomSelect} onRoomCreate={handleRoomCreate} />
                )}
            </div>
        );
    }

    // Desktop layout: Side-by-side view
    return (
        <div className="flex h-full bg-zinc-50 dark:bg-zinc-900">
            <div className="hidden w-80 border-r border-zinc-200 lg:block dark:border-zinc-700">
                <RoomList rooms={rooms} selectedRoom={selectedRoom} onRoomSelect={handleRoomSelect} onRoomCreate={handleRoomCreate} />
            </div>
            <div className="flex-1">
                {selectedRoom ? (
                    <ChatWindow room={selectedRoom} />
                ) : (
                    <div className="flex h-full items-center justify-center p-4 text-center">
                        <div>
                            <div className="text-lg text-zinc-500 dark:text-zinc-400">Select a room to start chatting</div>
                            <div className="mt-2 lg:hidden">
                                <Button onClick={handleBackToRooms} variant="outline">
                                    View Rooms
                                </Button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
