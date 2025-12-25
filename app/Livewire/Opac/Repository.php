<?php

namespace App\Livewire\Opac;

use Livewire\Component;

class Repository extends Component
{
    public function render()
    {
        return view('livewire.opac.repository')->layout('layouts.opac');
    }
}
