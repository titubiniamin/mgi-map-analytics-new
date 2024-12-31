<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dealer;
use App\Models\Admin;
use App\Models\Billboard;
use App\Models\Highwall;
use App\Models\Retailer;
use App\Models\Shopsign;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['dashboard.view']);

        return view(
            'backend.pages.dashboard.index',
            [
                'total_admins' => Admin::count(),
                'total_roles' => Role::count(),
                'total_permissions' => Permission::count(),
                'total_dealers' => \App\Models\Dealer::count(),
                'total_retailers' => Retailer::count(),
                'total_billboards' => Billboard::count(),
                'total_shopsigns' => Shopsign::count(),
                'total_highwalls' => Highwall::count(),
            ]
        );
    }
}
