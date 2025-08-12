import ChatLayout from '@/components/chat/ChatLayout';
import AppLayout from '@/layouts/app-layout';
import { Room } from '@/types/messages';
import { Head } from '@inertiajs/react';

interface ChatPageProps {
    rooms?: Room[];
    selectedRoomId?: number | null;
}

export default function ChatPage({ rooms, selectedRoomId }: ChatPageProps) {
    return (
        <AppLayout>
            <Head title="Chat" />
            <div className="h-[calc(100vh-4rem)] lg:h-[calc(100vh-4rem)]">
                <ChatLayout initialRooms={rooms} selectedRoomId={selectedRoomId} />
            </div>
        </AppLayout>
    );
}
