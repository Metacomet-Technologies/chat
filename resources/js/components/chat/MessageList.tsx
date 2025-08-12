import { Message } from '@/types/messages';

interface MessageListProps {
    messages: Message[];
    currentUserId: number;
    isMobile?: boolean;
}

export default function MessageList({ messages, currentUserId, isMobile = false }: MessageListProps) {
    return (
        <div className="space-y-4">
            {messages.map((message) => {
                const isOwnMessage = message.userId === currentUserId;

                return (
                    <div key={message.id} className={`flex ${isOwnMessage ? 'justify-end' : 'justify-start'}`}>
                        <div
                            className={`${isMobile ? 'max-w-[85%]' : 'max-w-xs lg:max-w-md'} rounded-lg ${isMobile ? 'px-3 py-2' : 'px-4 py-2'} ${
                                isOwnMessage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-900 dark:bg-gray-700 dark:text-white'
                            }`}
                        >
                            {!isOwnMessage && (
                                <div className={`mb-1 ${isMobile ? 'text-[11px]' : 'text-xs'} font-semibold opacity-75`}>{message.user.name}</div>
                            )}
                            <div className={`break-words ${isMobile ? 'text-sm' : ''}`}>{message.content}</div>
                            <div
                                className={`mt-1 ${isMobile ? 'text-[10px]' : 'text-xs'} ${isOwnMessage ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400'}`}
                            >
                                {new Date(message.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                            </div>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
