<?php

namespace App\Http\Livewire;

use App\Models\FAQ;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class Faqfront extends SearchableComponent
{
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $faqs = $this->searchFAQs();

        return view('livewire.faqfront', [
            'faqs' => $faqs,
        ]);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchFAQs()
    {
        return $this->paginate();
    }

    /**
     * @return string
     */
    public function model()
    {
        return FAQ::class;
    }

    /**
     * @return array
     */
    public function searchableFields()
    {
        return [
            'title',
        ];
    }
}
