<?php

namespace App\View\Components\Tables\BasicTables;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UsersTable extends Component
{
    public $users;
    
    /**
     * Create a new component instance.
     */
    public function __construct($users = [])
    {
        $this->users = $users;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.basic-tables.users-table');
    }
}