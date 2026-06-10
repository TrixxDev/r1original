<?php

namespace App\Http\Controllers;

class BranchController extends Controller
{
    public function ulbroka()
    {
        return $this->show('ulbroka');
    }

    public function riga()
    {
        return $this->show('riga');
    }

    protected function show(string $key)
    {
        $branch = collect(config('seo.organization.locations', []))
            ->firstWhere('key', $key);

        if (!$branch) {
            abort(404);
        }

        return view('branches.show', compact('branch'));
    }
}

