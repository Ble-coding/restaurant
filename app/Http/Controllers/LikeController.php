<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Blog;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'blog_id' => 'required|exists:blogs,id',
        ]);

        $customer = auth('customer')->user();

        // Vérifiez si le customer a déjà liké ce post
        $likeExists = Like::where('customer_id', $customer->id)
            ->where('blog_id', $request->blog_id)
            ->exists();

        if ($likeExists) {
            return response()->json(['message' => 'Vous avez déjà liké ce blog'], 400);
        }

        // Ajoutez le like
        Like::create([
            'customer_id' => $customer->id,
            'blog_id' => $request->blog_id,
        ]);

        return response()->json(['message' => 'Like ajouté avec succès']);
    }

    public function toggleLike(Request $request)
    {
        try {
            $request->validate([
                'blog_id' => 'required|exists:blogs,id',
            ]);

            $blog = Blog::findOrFail($request->blog_id);
            $customer = auth('customer')->user();

            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
            }

            $liked = $blog->likes()->where('customer_id', $customer->id)->exists();

            if ($liked) {
                $blog->likes()->where('customer_id', $customer->id)->delete();
            } else {
                $blog->likes()->create(['customer_id' => $customer->id]);
            }

            return response()->json([
                'success' => true,
                'liked' => !$liked,
                'likes_count' => $blog->likes()->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du toggle de like : ', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
