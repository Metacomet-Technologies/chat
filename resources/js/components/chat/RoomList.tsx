import { Room } from '@/types/messages';
import { Hash, Lock, Users } from 'lucide-react';
import CreateRoomDialog from './CreateRoomDialog';

interface RoomListProps {
    rooms: Room[];
    selectedRoom: Room | null;
    onRoomSelect: (room: Room) => void;
    onRoomCreate: (name: string, type: 'public' | 'private') => void;
}

export default function RoomList({ rooms, selectedRoom, onRoomSelect, onRoomCreate }: RoomListProps) {
    return (
        <div className="flex h-full flex-col">
            <div className="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Chat Rooms</h2>
                <CreateRoomDialog onCreate={onRoomCreate} compact />
            </div>

            <div className="flex-1 overflow-y-auto">
                {rooms.length === 0 ? (
                    <div className="p-4 text-center text-sm text-gray-500 dark:text-gray-400">No chat rooms yet. Create one to get started!</div>
                ) : (
                    rooms.map((room) => (
                        <button
                            key={room.id}
                            onClick={() => onRoomSelect(room)}
                            className={`w-full p-4 text-left transition-colors hover:bg-gray-100 active:bg-gray-200 dark:hover:bg-gray-800 dark:active:bg-gray-700 ${
                                selectedRoom?.id === room.id ? 'border-l-4 border-blue-500 bg-gray-100 dark:bg-gray-800' : ''
                            }`}
                        >
                            <div className="flex items-center gap-2">
                                {room.isPrivate ? (
                                    <Lock className="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                ) : (
                                    <Hash className="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                )}
                                <span className="font-medium text-gray-900 dark:text-white">{room.name}</span>
                            </div>
                            <div className="mt-1 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                {room.users && (
                                    <span className="flex items-center gap-1">
                                        <Users className="h-3 w-3" />
                                        {room.users.length}
                                    </span>
                                )}
                                {room.lastMessageAt && <span>{new Date(room.lastMessageAt).toLocaleTimeString()}</span>}
                            </div>
                        </button>
                    ))
                )}
            </div>
        </div>
    );
}
