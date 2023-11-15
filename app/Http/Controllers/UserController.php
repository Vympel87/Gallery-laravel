<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Please login to access the dashboard.',
                ])->onlyInput('email');
        }

        $users = User::get();

        return view('users')->with('userss', $users);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        return view('update', compact('user'));
    }

    public function updateData(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email',
            'photo' => 'image|nullable|max:1999'
        ]);
        // dd($request->all());
        $user = User::findOrFail($id);
        // check apakah image is uploaded
        if ($request->hasFile('photo')){
            // upload  new image
            $image = $request->file('photo');
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filenameSimpan = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('photo')->storeAs('photos', $filenameSimpan);

            //delete old image
            // dd(public_path().''.$user->photo);
            Storage::delete('photos/'.$user->photo);

            //update post with new image
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'photo'     => $filenameSimpan
            ]);
        } else {
            //update user without photo
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
            ]);
        }
        //redirect to dashboard
        return redirect()->route('user.index')->with(['message' => 'Data Berhasil Diubah!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){
        $user = User::findOrFail($id);

        Storage::delete($user->photo);

        $user->delete();

        return redirect()->route('user.index')->with(['message' => 'Data Berhasil Dihapus!']);
    }
}
