<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class TestRouteAccessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:route-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test route access for notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing notification routes...');
        
        // Check if routes exist
        $routes = [
            'admin.notifications.index',
            'admin.notifications.recent', 
            'admin.notifications.unread-count',
            'admin.notifications.read',
            'admin.notifications.mark-all-read',
            'admin.notifications.destroy'
        ];
        
        foreach ($routes as $routeName) {
            try {
                $route = Route::getRoutes()->getByName($routeName);
                if ($route) {
                    $this->info("✓ Route '{$routeName}' exists: " . $route->uri());
                } else {
                    $this->error("✗ Route '{$routeName}' not found");
                }
            } catch (\Exception $e) {
                $this->error("✗ Error checking route '{$routeName}': " . $e->getMessage());
            }
        }
        
        $this->info('');
        $this->info('To access the notification page, make sure you are logged in as admin first.');
        $this->info('Then visit: http://127.0.0.1:8000/admin/notifications');
        
        return 0;
    }
}
