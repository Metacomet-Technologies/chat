export interface User {
    id: number;
    name: string;
    email: string;
}

export interface Message {
    id: number;
    roomId: number;
    userId: number;
    content: string;
    type: string;
    metadata?: Record<string, unknown>;
    user: User;
    createdAt: string;
    updatedAt: string;
}

export interface Room {
    id: number;
    name: string;
    type: 'public' | 'private' | 'direct';
    isPrivate: boolean;
    users?: User[];
    unreadCount?: number;
    lastMessageAt?: string;
    createdAt: string;
    updatedAt: string;
}

export interface MessageListResponse {
    messages: Message[];
    total: number;
    perPage: number;
    currentPage: number;
    lastPage: number;
    nextPageUrl?: string;
    previousPageUrl?: string;
}

export interface SendMessageRequest {
    content: string;
    type?: string;
    metadata?: Record<string, unknown>;
}

export interface CreateRoomRequest {
    name: string;
    type?: 'public' | 'private' | 'direct';
    isPrivate?: boolean;
    userIds?: number[];
}
