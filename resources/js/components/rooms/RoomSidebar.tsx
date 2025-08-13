import React from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Users, Settings, LogOut, Crown, Shield } from 'lucide-react';
import { cn } from '@/lib/utils';

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

interface RoomSidebarProps {
    room: Room;
    members: RoomMember[];
    currentUserId: number;
    onLeaveRoom?: () => void;
    onManageRoom?: () => void;
}

export function RoomSidebar({ 
    room, 
    members, 
    currentUserId, 
    onLeaveRoom,
    onManageRoom 
}: RoomSidebarProps) {
    
    const currentUserRole = room.user_role || 'member';
    const isAdmin = currentUserRole === 'admin';
    const isModerator = currentUserRole === 'moderator';
    
    const getRoleIcon = (role: string) => {
        switch (role) {
            case 'admin':
                return <Crown className="h-3 w-3" />;
            case 'moderator':
                return <Shield className="h-3 w-3" />;
            default:
                return null;
        }
    };
    
    const getRoleBadgeVariant = (role: string) => {
        switch (role) {
            case 'admin':
                return 'default';
            case 'moderator':
                return 'secondary';
            default:
                return 'outline';
        }
    };
    
    const sortedMembers = [...members].sort((a, b) => {
        // Sort by online status first
        if (a.is_online !== b.is_online) {
            return a.is_online ? -1 : 1;
        }
        // Then by role (admin > moderator > member)
        const roleOrder = { admin: 0, moderator: 1, member: 2 };
        if (roleOrder[a.role] !== roleOrder[b.role]) {
            return roleOrder[a.role] - roleOrder[b.role];
        }
        // Finally by name
        return a.name.localeCompare(b.name);
    });

    return (
        <div className="w-64 border-l bg-background flex flex-col h-full">
            <div className="p-4 border-b">
                <h3 className="font-semibold text-lg">{room.name}</h3>
                {room.description && (
                    <p className="text-sm text-muted-foreground mt-1">{room.description}</p>
                )}
                <div className="flex items-center gap-2 mt-2">
                    <Badge variant="outline" className="text-xs">
                        {room.is_public ? 'Public' : 'Private'}
                    </Badge>
                    <Badge variant="outline" className="text-xs flex items-center gap-1">
                        <Users className="h-3 w-3" />
                        {room.member_count}
                    </Badge>
                </div>
            </div>
            
            <ScrollArea className="flex-1">
                <div className="p-4">
                    <h4 className="text-sm font-medium mb-3 text-muted-foreground">
                        Members ({members.length})
                    </h4>
                    <div className="space-y-2">
                        {sortedMembers.map((member) => (
                            <div
                                key={member.user_id}
                                className={cn(
                                    "flex items-center gap-2 p-2 rounded-md",
                                    member.user_id === currentUserId && "bg-muted"
                                )}
                            >
                                <div className="relative">
                                    <Avatar className="h-8 w-8">
                                        <AvatarFallback className="text-xs">
                                            {member.name.split(' ').map(n => n[0]).join('').toUpperCase()}
                                        </AvatarFallback>
                                    </Avatar>
                                    {member.is_online && (
                                        <span className="absolute bottom-0 right-0 h-2.5 w-2.5 bg-green-500 rounded-full border-2 border-background" />
                                    )}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-medium truncate">
                                        {member.name}
                                        {member.user_id === currentUserId && (
                                            <span className="text-muted-foreground"> (You)</span>
                                        )}
                                    </p>
                                    {member.role !== 'member' && (
                                        <Badge 
                                            variant={getRoleBadgeVariant(member.role) as 'default' | 'secondary' | 'outline'}
                                            className="mt-0.5 text-xs py-0 h-4"
                                        >
                                            {getRoleIcon(member.role)}
                                            <span className="ml-1">{member.role}</span>
                                        </Badge>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </ScrollArea>
            
            <div className="p-4 border-t space-y-2">
                {(isAdmin || isModerator) && onManageRoom && (
                    <Button
                        variant="outline"
                        size="sm"
                        className="w-full justify-start"
                        onClick={onManageRoom}
                    >
                        <Settings className="mr-2 h-4 w-4" />
                        Manage Room
                    </Button>
                )}
                {onLeaveRoom && (
                    <Button
                        variant="outline"
                        size="sm"
                        className="w-full justify-start text-destructive hover:text-destructive"
                        onClick={onLeaveRoom}
                    >
                        <LogOut className="mr-2 h-4 w-4" />
                        Leave Room
                    </Button>
                )}
            </div>
        </div>
    );
}