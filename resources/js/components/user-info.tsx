import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import type { Group, User } from '@/types';

export function UserInfo({
    user,
    showEmail = false,
    isAdmin = false,
    group = null,
}: {
    user: User;
    showEmail?: boolean;
    isAdmin?: boolean;
    group?: Group | null;
}) {
    const getInitials = useInitials();
    const showAvatar = Boolean(user.avatar && user.avatar !== '');

    return (
        <>
            <Avatar className="h-8 w-8 overflow-hidden rounded-lg">
                {showAvatar ? (
                    <AvatarImage src={user.avatar} alt={user.name} />
                ) : null}
                <AvatarFallback className="rounded-lg text-black dark:text-white">
                    {getInitials(user.name)}
                </AvatarFallback>
            </Avatar>
            <div className="grid flex-1 text-left text-sm leading-tight">
                <span className="truncate font-medium">{user.name}</span>
                {group ? (
                    <span className="truncate text-xs text-muted-foreground">
                        {group.name}
                    </span>
                ) : null}
                {!group && showEmail ? (
                    <span className="truncate text-xs text-muted-foreground">
                        {user.email}
                    </span>
                ) : null}
                {isAdmin ? (
                    <span className="truncate text-xs font-semibold text-emerald-500">
                        Administrator
                    </span>
                ) : null}
            </div>
        </>
    );
}
