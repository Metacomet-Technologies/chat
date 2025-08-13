import React, { useEffect, useState, useRef } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { RoomSidebar } from '@/components/rooms/RoomSidebar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Send } from 'lucide-react';
import { useEchoPresence } from '@laravel/echo-react';
import { format } from 'date-fns';

interface RoomMember {
    user_id: number;
    name: string;
    email: string;
    role: 'admin' | 'moderator' | 'member';
    joined_at: string;
    is_online: boolean;
}

interface Room {
    id: number;
    name: string;
    description?: string;
    slug: string;
    is_public: boolean;
    member_count: number;
    user_role?: string;
}

interface Message {
    id: number;
    content: string;
    sender: {
        id: number;
        name: string;
    };
    created_at: string;
}

interface Props {
    room: Room;
    members: {
        data: RoomMember[];
    };
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
        };
    };
}

export default function RoomShow({ room, members, auth }: Props) {
    const [messages, setMessages] = useState<Message[]>([]);
    const [newMessage, setNewMessage] = useState('');
    const [onlineMembers, setOnlineMembers] = useState<Set<number>>(new Set());
    const messagesEndRef = useRef<HTMLDivElement>(null);

    // Set up presence channel for online status
    const { here, joining, leaving } = useEchoPresence<{ id: number; name: string }>(
        `room.${room.id}`,
        '.MessageSent',
        (e: { message: Message }) => {
            setMessages(prev => [...prev, e.message]);
        },
        [room.id],
        'presence'
    );

    useEffect(() => {
        if (here) {
            setOnlineMembers(new Set(here.map(user => user.id)));
        }
    }, [here]);

    useEffect(() => {
        if (joining) {
            setOnlineMembers(prev => new Set([...prev, joining.id]));
        }
    }, [joining]);

    useEffect(() => {
        if (leaving) {
            setOnlineMembers(prev => {
                const newSet = new Set(prev);
                newSet.delete(leaving.id);
                return newSet;
            });
        }
    }, [leaving]);

    // Load initial messages
    useEffect(() => {
        fetch(route('api.v1.rooms.messages.index', { room: room.id }))
            .then(res => res.json())
            .then(data => setMessages(data.data || []));
    }, [room.id]);

    // Scroll to bottom when messages change
    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    const handleSendMessage = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!newMessage.trim()) return;

        try {
            const response = await fetch(route('api.v1.rooms.messages.store', { room: room.id }), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'include',
                body: JSON.stringify({ content: newMessage }),
            });

            if (response.ok) {
                setNewMessage('');
            }
        } catch (error) {
            console.error('Failed to send message:', error);
        }
    };

    const handleLeaveRoom = async () => {
        try {
            const response = await fetch(route('api.v1.rooms.leave', { room: room.id }), {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'include',
            });

            if (response.ok) {
                router.visit('/rooms');
            }
        } catch (error) {
            console.error('Failed to leave room:', error);
        }
    };

    // Update members with online status
    const membersWithOnlineStatus = members.data.map(member => ({
        ...member,
        is_online: onlineMembers.has(member.user_id),
    }));

    return (
        <AppLayout>
            <Head title={`${room.name} - Room`} />
            
            <div className="flex h-[calc(100vh-4rem)]">
                <div className="flex-1 flex flex-col">
                    <div className="border-b px-6 py-4">
                        <h1 className="text-2xl font-bold">{room.name}</h1>
                        {room.description && (
                            <p className="text-muted-foreground mt-1">{room.description}</p>
                        )}
                    </div>

                    <ScrollArea className="flex-1 px-6">
                        <div className="py-4 space-y-4">
                            {messages.map((message) => (
                                <div
                                    key={message.id}
                                    className={`flex gap-3 ${
                                        message.sender.id === auth.user.id ? 'justify-end' : ''
                                    }`}
                                >
                                    {message.sender.id !== auth.user.id && (
                                        <Avatar className="h-8 w-8">
                                            <AvatarFallback className="text-xs">
                                                {message.sender.name.split(' ').map(n => n[0]).join('').toUpperCase()}
                                            </AvatarFallback>
                                        </Avatar>
                                    )}
                                    <div
                                        className={`max-w-[70%] ${
                                            message.sender.id === auth.user.id
                                                ? 'bg-primary text-primary-foreground'
                                                : 'bg-muted'
                                        } rounded-lg px-4 py-2`}
                                    >
                                        {message.sender.id !== auth.user.id && (
                                            <p className="text-xs font-semibold mb-1">
                                                {message.sender.name}
                                            </p>
                                        )}
                                        <p className="text-sm">{message.content}</p>
                                        <p className="text-xs opacity-70 mt-1">
                                            {format(new Date(message.created_at), 'HH:mm')}
                                        </p>
                                    </div>
                                    {message.sender.id === auth.user.id && (
                                        <Avatar className="h-8 w-8">
                                            <AvatarFallback className="text-xs">
                                                {auth.user.name.split(' ').map(n => n[0]).join('').toUpperCase()}
                                            </AvatarFallback>
                                        </Avatar>
                                    )}
                                </div>
                            ))}
                            <div ref={messagesEndRef} />
                        </div>
                    </ScrollArea>

                    <form onSubmit={handleSendMessage} className="border-t p-4">
                        <div className="flex gap-2">
                            <Input
                                value={newMessage}
                                onChange={(e) => setNewMessage(e.target.value)}
                                placeholder="Type a message..."
                                className="flex-1"
                            />
                            <Button type="submit" size="icon">
                                <Send className="h-4 w-4" />
                            </Button>
                        </div>
                    </form>
                </div>

                <RoomSidebar
                    room={room}
                    members={membersWithOnlineStatus}
                    currentUserId={auth.user.id}
                    onLeaveRoom={handleLeaveRoom}
                />
            </div>
        </AppLayout>
    );
}