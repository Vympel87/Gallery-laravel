<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
* @OA\Info(
* description="API doc gallery OpenAPI/Swagger",
* version="0.0.1",
* title="API documentation Gallery",
* termsOfService="http://swagger.io/terms/",
* @OA\Contact(
* email="yudhisitirarb727@gmail.com"
* ),
* @OA\License(
* name="Apache 2.0",
* url="http://www.apache.org/licenses/LICENSE-2.0.html"
* )
* )
*/


class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $data = array(
        // 'id' => "posts",
        // 'menu' => 'Gallery',
        // 'galleries' => Post::where('picture', '!=', '')->whereNotNull('picture')->orderBy('created_at', 'desc')->paginate(30)
        // );
        // return view('gallery.index')->with($data);

        $response = Http::get('http://127.0.0.1:8000/api/gallery');
        $objectResponse = $response->body();
        $data = json_decode($objectResponse, true);
        return view('gallery.index')->with([
            'galleries' => $data['galleries']['data'],
            'links' => $data['galleries']['links']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $this->validate($request, [
                'title' => 'required|max:255',
                'description' => 'required',
                'picture' => 'image|nullable|max:1999'
            ]);

            if ($request->hasFile('picture')) {
                $filenameWithExt = $request->file('picture')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('picture')->getClientOriginalExtension();
                $basename = uniqid() . time();
                $smallFilename = "small_{$basename}.{$extension}";
                $mediumFilename = "medium_{$basename}.{$extension}";
                $largeFilename = "large_{$basename}.{$extension}";
                $filenameSimpan = "{$basename}.{$extension}";
                $path = $request->file('picture')->storeAs('posts_image', $filenameSimpan);
            } else {
                $filenameSimpan = 'noimage.png';
            }

            $response = Http::attach(
                'picture', file_get_contents($request->picture), $filenameSimpan 
            )->post('http://127.0.0.1:8000/api/gallery-store', [
                'title' => $request->title,
                'description' => $request->description,
            ]);

            if ($response->successful()) {
                return redirect()->route('gallery.index')->with('Success', 'Berhasil menambahkan data baru');
            }
        
        } catch(Exception $e){
            return redirect('dashboard')->with('Fail', 'Gagal menambahkan data baru');
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(string $id)
    {
        $gallery = Post::findOrFail($id);
        return view('gallery.update', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateData(Request $request, string $id)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'picture' => 'image|nullable|max:1999'
        ]);
        $post = Post::findOrFail($id);
        
        if ($request->hasFile('picture')){
            
            $path = 'posts_image/'. $post->picture;
    
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $basename = uniqid() . time();
            $smallFilename = "small_{$basename}.{$extension}";
            $mediumFilename = "medium_{$basename}.{$extension}";
            $largeFilename = "large_{$basename}.{$extension}";
            $filenameSimpan = "{$basename}.{$extension}";

            
            $post->update([
                'title'         => $request->title,
                'description'   => $request->description,
                'picture'       => $filenameSimpan
            ]);
        } else {
            
            $post->update([
                'title'         => $request->title,
                'description'   => $request->description
            ]);
        }
        
        return redirect()->route('gallery.update', $post->id)->with(['message' => 'Data Berhasil Diubah!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gallery = Post::findOrFail($id);
        $path = 'posts_image/'. $gallery->picture;

        if (Storage::exists($path)) {
            Storage::delete($path);
        }
        $gallery->delete();

        return redirect()->route('gallery.index')->with(['message' => 'Data Berhasil Dihapus!']);
    }

    /**
     * @OA\Get(
     *      path="/api/gallery",
     *      tags={"Get Picture For Gallery"},
     *      summary="Menampilkan picture Gallery",
     *      description="Menampilkan picture Gallery",
     *      operationId="gallery",
     *      @OA\Response(
     *          response="default",
     *          description="Success Menampilkan picture"
     *      )
     * )
    */

    public function GetPic()
    { 
        try{
            $posts = Post::where('picture', '!=', '')->whereNotNull('picture')->orderBy('created_at', 'desc')->paginate(30);
            return response()->json([
                'galleries' => $posts,
                'success' => true
            ], 200);
        } catch(Exception $e){
            return response()->json([
                'message' => $e
            ],404);
        }
    }

     /**
     * @OA\Post(
     *     path="/api/gallery-store",
     *     summary="Add Picture In Gallery",
     *     tags={"Store Gallery"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Judul gallery",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Deskripsi Gallery",
     *                 ),
     *                 @OA\Property(
     *                     property="picture",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file Gallery",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success menambahkan data",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error",
     *     )
     * )
     */

    public function addPicture(Request $request)
    {
        try {
            $this->validate($request, [
                'title' => 'required|max:255',
                'description' => 'required',
                'picture' => 'image|nullable|max:1999'
            ]);

            if ($request->hasFile('picture')) {
                $extension = $request->file('picture')->getClientOriginalExtension();
                $basename = uniqid() . time();
                $filenameSimpan = "{$basename}.{$extension}";

                $path = public_path('storage/posts_image/'.$filenameSimpan);
                move_uploaded_file($request->file('picture'), $path);
            } else {
                $filenameSimpan = 'noimage.png';
            }

            $post = Post::create([
                'title' => $request->title,
                'description' => $request->description,
                'picture' => $filenameSimpan
            ]);

            return response()->json([
                'post' => $post,
                'message' => 'Data berhasil ditambahkan!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

}
