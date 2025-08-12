import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Plus } from 'lucide-react';
import { useState } from 'react';

interface CreateRoomDialogProps {
    onCreate: (name: string, type: 'public' | 'private') => void;
    compact?: boolean;
}

export default function CreateRoomDialog({ onCreate, compact = false }: CreateRoomDialogProps) {
    const [open, setOpen] = useState(false);
    const [name, setName] = useState('');
    const [type, setType] = useState<'public' | 'private'>('public');
    const [isCreating, setIsCreating] = useState(false);

    const handleCreate = async () => {
        if (name.trim()) {
            setIsCreating(true);
            await onCreate(name.trim(), type);
            setName('');
            setType('public');
            setOpen(false);
            setIsCreating(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                {compact ? (
                    <Button variant="ghost" size="icon" title="Create new chat room">
                        <Plus className="h-4 w-4" />
                    </Button>
                ) : (
                    <Button variant="outline" className="w-full">
                        <Plus className="mr-2 h-4 w-4" />
                        New Chat Room
                    </Button>
                )}
            </DialogTrigger>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Create New Chat Room</DialogTitle>
                    <DialogDescription>Create a new chat room to start conversations with other users.</DialogDescription>
                </DialogHeader>
                <div className="grid gap-4 py-4">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Room Name</Label>
                        <Input
                            id="name"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            placeholder="e.g., General Discussion"
                            className="w-full"
                        />
                    </div>
                    <div className="grid gap-2">
                        <Label>Room Type</Label>
                        <RadioGroup value={type} onValueChange={(value: string) => setType(value as 'public' | 'private')}>
                            <div className="flex items-center space-x-2">
                                <RadioGroupItem value="public" id="public" />
                                <Label htmlFor="public" className="font-normal">
                                    Public - Anyone can join
                                </Label>
                            </div>
                            <div className="flex items-center space-x-2">
                                <RadioGroupItem value="private" id="private" />
                                <Label htmlFor="private" className="font-normal">
                                    Private - Invite only
                                </Label>
                            </div>
                        </RadioGroup>
                    </div>
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" onClick={() => setOpen(false)} disabled={isCreating}>
                        Cancel
                    </Button>
                    <Button type="submit" onClick={handleCreate} disabled={!name.trim() || isCreating}>
                        {isCreating ? 'Creating...' : 'Create Room'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
