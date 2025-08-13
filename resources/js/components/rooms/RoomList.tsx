import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Users, Lock, Globe, ChevronRight } from 'lucide-react';

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

interface RoomListProps {
    rooms: Room[];
    onJoinRoom?: (roomId: number) => void;
    showJoinButton?: boolean;
}

export function RoomList({ rooms, onJoinRoom, showJoinButton = true }: RoomListProps) {
    const [joiningRoom, setJoiningRoom] = useState<number | null>(null);

    const handleJoinRoom = async (roomId: number) => {
        if (!onJoinRoom) return;
        
        setJoiningRoom(roomId);
        try {
            await onJoinRoom(roomId);
        } finally {
            setJoiningRoom(null);
        }
    };

    if (rooms.length === 0) {
        return (
            <div className="text-center py-12">
                <p className="text-muted-foreground">No rooms available</p>
            </div>
        );
    }

    return (
        <div className="grid gap-4">
            {rooms.map((room) => (
                <Card key={room.id} className="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <div className="flex items-start justify-between">
                            <div>
                                <CardTitle className="flex items-center gap-2">
                                    {room.name}
                                    {room.is_public ? (
                                        <Globe className="h-4 w-4 text-muted-foreground" />
                                    ) : (
                                        <Lock className="h-4 w-4 text-muted-foreground" />
                                    )}
                                </CardTitle>
                                {room.description && (
                                    <CardDescription className="mt-1">
                                        {room.description}
                                    </CardDescription>
                                )}
                            </div>
                            <div className="flex items-center gap-2">
                                {room.is_member && (
                                    <Badge variant="secondary">
                                        {room.user_role === 'admin' ? 'Admin' : 'Member'}
                                    </Badge>
                                )}
                                <Badge variant="outline" className="flex items-center gap-1">
                                    <Users className="h-3 w-3" />
                                    {room.member_count}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="flex justify-between items-center">
                            <span className="text-sm text-muted-foreground">
                                #{room.slug}
                            </span>
                            <div className="flex gap-2">
                                {showJoinButton && !room.is_member && room.is_public && (
                                    <Button
                                        size="sm"
                                        onClick={() => handleJoinRoom(room.id)}
                                        disabled={joiningRoom === room.id}
                                    >
                                        {joiningRoom === room.id ? 'Joining...' : 'Join Room'}
                                    </Button>
                                )}
                                {room.is_member && (
                                    <Link href={`/rooms/${room.slug}`}>
                                        <Button size="sm" variant="outline">
                                            Enter Room
                                            <ChevronRight className="ml-1 h-4 w-4" />
                                        </Button>
                                    </Link>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            ))}
        </div>
    );
}