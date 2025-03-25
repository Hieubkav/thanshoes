<?php

namespace App\View\Components\Navbar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icons extends Component
{
    public $cartCount;

    /**
     * Create a new component instance.
     */
    public function __construct($cartCount = 0)
    {
        $this->cartCount = $cartCount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navbar.icons');
    }
}