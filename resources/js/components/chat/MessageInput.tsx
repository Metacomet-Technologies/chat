import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Send } from 'lucide-react';
import React, { useState } from 'react';

interface MessageInputProps {
    onSendMessage: (content: string) => void;
    isMobile?: boolean;
}

export default function MessageInput({ onSendMessage, isMobile = false }: MessageInputProps) {
    const [message, setMessage] = useState('');
    const [isSending, setIsSending] = useState(false);

    const handleSend = async () => {
        if (message.trim() && !isSending) {
            setIsSending(true);
            await onSendMessage(message.trim());
            setMessage('');
            setIsSending(false);
        }
    };

    const handleKeyPress = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    return (
        <div className={`border-t border-zinc-200 dark:border-zinc-700 ${isMobile ? 'p-3' : 'p-4'}`}>
            <div className="flex gap-2">
                <Input
                    type="text"
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                    onKeyPress={handleKeyPress}
                    placeholder="Type a message..."
                    className={`flex-1 ${isMobile ? 'text-base' : ''}`}
                    disabled={isSending}
                    autoComplete="off"
                    autoCorrect="on"
                    autoCapitalize="sentences"
                />
                <Button
                    onClick={handleSend}
                    disabled={!message.trim() || isSending}
                    size={isMobile ? 'icon' : 'default'}
                    className={isMobile ? 'h-10 w-10' : ''}
                >
                    <Send className={isMobile ? 'h-5 w-5' : 'h-4 w-4'} />
                </Button>
            </div>
        </div>
    );
}
