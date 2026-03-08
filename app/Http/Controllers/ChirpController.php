<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chirps = Chirp::with('user')->latest()->take(50)->get(); //\App\Models\Chirp::All();

        return view('home', ['chirps' => $chirps]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //         $validated = $request->validate([
        //     'message' => [
        //         'required',
        //         'string',
        //         'max:255',
        //         Rule::unique('chirps')->where(function ($query) use ($user) {
        //             return $query->where('user_id', $user->id);
        //         })
        //     ],
        // ]);
        //
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ], [
            'message.required' => 'Please write something to chirp!',
            'message.max' => 'Chirps must be 255 characters or less.',
        ]);

        \App\Models\Chirp::create([
            'message' => $validated['message'],
            'user_id' => null,
        ]);

        return redirect('/')->with('success', 'Your chirp has been posted!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        return view('components/chirp', ['chirp' => $chirp]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        return view('chirps.edit', compact('chirp'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp)
    {
        if ($request->user()->cannot('update', $chirp)) {
            abort(403);
        }

        // $this->authorize('update', $chirp)

        $validated = $request->validate(['message' => 'required|string|max:255']);

        $chirp->update($validated);

        return redirect('/')->with('success', 'Your chirp has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp)
    {
        $chirp->delete();

        return redirect('/')->with('success', 'Your chirp has been deleted!');
    }
}
