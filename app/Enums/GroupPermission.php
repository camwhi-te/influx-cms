<?php

namespace App\Enums;

enum GroupPermission: string
{
    case UpdateGroup = 'group:update';
    case DeleteGroup = 'group:delete';

    case AddMember = 'member:add';
    case UpdateMember = 'member:update';
    case RemoveMember = 'member:remove';

    case CreateInvitation = 'invitation:create';
    case CancelInvitation = 'invitation:cancel';
}
