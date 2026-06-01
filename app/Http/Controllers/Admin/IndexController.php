<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;

/**
 * Admin Index Controller
 *
 * View controller for managing the admin index page.
 */
class IndexController extends Controller
{
    /**
     * Show the index page.
     */
    public function index(AdminRequest $request): Response
    {
        $request->permission('overview:index');

        return inertia('admin/overview/index');
    }
}
