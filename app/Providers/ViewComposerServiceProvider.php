<?php

namespace App\Providers;
use App\Models\School;
use App\Models\Page;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        View::composer('*', function ($view) {
            // Get the school slug from the current route
            $schoolSlug = request()->route('schoolSlug');
            if ($schoolSlug) {
                $school = School::where('slug', $schoolSlug)->first();
                if ($school) {
                    $pages = Page::where('school_id', $school->id)->get();
                    $view->with('currentSchool', $school)
                         ->with('schoolPages', $pages);
                }
            }
        });
    
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
