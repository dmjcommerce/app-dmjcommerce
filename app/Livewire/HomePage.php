<?php

namespace App\Livewire;

use App\Models\brand;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Home Page - DMJ COMMERCE')]

//Traemos en la parte de inicio los brand que estan activos(Si su estado es 1)
class HomePage extends Component
{
    public function render()
    {
        $brands = brand::where('is_active', 1)->get();
        //Se usa para ver los atributos y lo que esta trayendo
        //dd($brands);
        return view('livewire.home-page', [
            'brands' => $brands
        ]);
    }
}
