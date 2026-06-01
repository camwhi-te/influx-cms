<?php

namespace App\Support;

readonly class GroupPermissions
{
    public function __construct(
        public bool $canUpdateGroup,
        public bool $canDeleteGroup,
        public bool $canAddMember,
        public bool $canUpdateMember,
        public bool $canRemoveMember,
        public bool $canCreateInvitation,
        public bool $canCancelInvitation,
    ) {
        //
    }
}
