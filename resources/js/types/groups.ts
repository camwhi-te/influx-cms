export type GroupRole = 'owner' | 'admin' | 'member';

export type Group = {
    id: number;
    name: string;
    slug: string;
    isPersonal: boolean;
    role?: GroupRole;
    roleLabel?: string;
    isCurrent?: boolean;
};

export type GroupMember = {
    id: number;
    name: string;
    email: string;
    avatar?: string | null;
    role: GroupRole;
    role_label: string;
};

export type GroupInvitation = {
    code: string;
    email: string;
    role: GroupRole;
    role_label: string;
    created_at: string;
};

export type GroupPermissions = {
    canUpdateGroup: boolean;
    canDeleteGroup: boolean;
    canAddMember: boolean;
    canUpdateMember: boolean;
    canRemoveMember: boolean;
    canCreateInvitation: boolean;
    canCancelInvitation: boolean;
};

export type RoleOption = {
    value: GroupRole;
    label: string;
};
