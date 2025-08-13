import api from '@/lib/api';
import { SharedData } from '@/types';
import { Message, Room } from '@/types/messages';
import { usePage } from '@inertiajs/react';
import { useEcho } from '@laravel/echo-react';
import { useEffect, useRef, useState } from 'react';
import MessageInput from './MessageInput';
import MessageList from './MessageList';

interface ChatWindowProps {
    room: Room;
    isMobile?: boolean;
}

export default function ChatWindow({ room, isMobile = false }: ChatWindowProps) {
    const [messages, setMessages] = useState<Message[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const messagesEndRef = useRef<HTMLDivElement>(null);
    const { auth } = usePage<SharedData>().props;

    // Use Laravel Echo React hook for real-time messages
    useEcho<{ message: Message }>(
        `room.${room.id}`,
        '.message.sent',
        (e) => {
            setMessages((prev) => {
                // Check if message already exists to avoid duplicates
                const exists = prev.some((msg) => msg.id === e.message.id);
                if (exists) return prev;
                return [...prev, e.message];
            });
        },
        [room.id], // Dependencies for the callback
        'private', // Channel visibility
    );

    useEffect(() => {
        // Clear messages when switching rooms
        setMessages([]);
        fetchMessages();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [room.id]);

    useEffect(() => {
        scrollToBottom();
    }, [messages]);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    const fetchMessages = async () => {
        setIsLoading(true);
        try {
            const response = await api.get(route('api.v1.rooms.messages.index', { room: room.id }));
            setMessages(response.data.messages.reverse());
        } catch (error) {
            console.error('Failed to fetch messages:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const sendMessage = async (content: string) => {
        try {
            await api.post(route('api.v1.rooms.messages.store', { room: room.id }), { content });
            // Message will be added via WebSocket broadcast, no need to add here
        } catch (error) {
            console.error('Failed to send message:', error);
        }
    };

    return (
        <div className="flex h-full flex-col">
            {/* Desktop header - hide on mobile since we have a custom header */}
            {!isMobile && (
                <div className="border-b border-zinc-200 p-4 dark:border-zinc-700">
                    <h3 className="text-lg font-semibold text-zinc-900 dark:text-white">{room.name}</h3>
                    {room.users && <p className="text-sm text-zinc-500 dark:text-zinc-400">{room.users.length} members</p>}
                </div>
            )}

            <div className={`flex-1 overflow-y-auto ${isMobile ? 'p-3' : 'p-4'}`}>
                {isLoading ? (
                    <div className="flex h-full items-center justify-center">
                        <div className="text-zinc-500 dark:text-zinc-400">Loading messages...</div>
                    </div>
                ) : (
                    <>
                        <MessageList messages={messages} currentUserId={auth.user.id} isMobile={isMobile} />
                        <div ref={messagesEndRef} />
                    </>
                )}
            </div>

            <MessageInput onSendMessage={sendMessage} isMobile={isMobile} />
        </div>
    );
}
