import { Head, Link } from '@inertiajs/react';
import { Eye, Pencil, Plus } from 'lucide-react';
import CreateGroupModal from '@/components/create-group-modal';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { edit, index } from '@/routes/groups';
import type { Group } from '@/types';

type Props = {
    groups: Group[];
};

export default function GroupsIndex({ groups }: Props) {
    return (
        <>
            <Head title="Groups" />

            <h1 className="sr-only">Groups</h1>

            <div className="flex flex-col space-y-6">
                <div className="flex items-center justify-between">
                    <Heading
                        variant="small"
                        title="Groups"
                        description="Manage your groups and group memberships"
                    />

                    <CreateGroupModal>
                        <Button data-test="groups-new-group-button">
                            <Plus /> New group
                        </Button>
                    </CreateGroupModal>
                </div>

                <div className="space-y-3">
                    {groups.map((group) => (
                        <div
                            key={group.id}
                            data-test="group-row"
                            className="flex items-center justify-between rounded-lg border p-4"
                        >
                            <div className="flex items-center gap-4">
                                <div>
                                    <div className="flex items-center gap-2">
                                        <span className="font-medium">
                                            {group.name}
                                        </span>
                                        {group.isPersonal ? (
                                            <Badge variant="secondary">
                                                Personal
                                            </Badge>
                                        ) : null}
                                    </div>
                                    <span className="text-sm text-muted-foreground">
                                        {group.roleLabel}
                                    </span>
                                </div>
                            </div>

                            <TooltipProvider>
                                <div className="flex items-center gap-2">
                                    {group.role === 'member' ? (
                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    data-test="group-view-button"
                                                    asChild
                                                >
                                                    <Link
                                                        href={edit(group.slug)}
                                                    >
                                                        <Eye className="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>View group</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    ) : (
                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    data-test="group-edit-button"
                                                    asChild
                                                >
                                                    <Link
                                                        href={edit(group.slug)}
                                                    >
                                                        <Pencil className="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>Edit group</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    )}
                                </div>
                            </TooltipProvider>
                        </div>
                    ))}

                    {groups.length === 0 ? (
                        <p className="py-8 text-center text-muted-foreground">
                            You don't belong to any groups yet.
                        </p>
                    ) : null}
                </div>
            </div>
        </>
    );
}

GroupsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Groups',
            href: index(),
        },
    ],
};
