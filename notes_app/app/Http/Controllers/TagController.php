<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = DB::select("SELECT id, name FROM tags");

        return response()->json([
            'success' => true,
            'data' => $tags
        ], 200);
    }

    public function store(Request $request)
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100'
        ]);

        // validation fail
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create data
        $tag = DB::selectOne("
            INSERT INTO tags(name)
            VALUES (?)
            RETURNING id, name
        ", [$request->name]);

        // return json
        if ($tag) {
            return response()->json([
                'success' => true,
                'message' => 'Data successfully created',
                'data' => $tag
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data not found'
        ], 404);
    }

    public function update(Request $request, string $id)
    {
        // set validation
        $validator = Validator::make(
            $request->all(),
            ['name' => 'required|string|max:100']
        );

        // if validation fails
        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }

        $updateTag = DB::selectOne("
            UPDATE tags 
            SET name = ? 
            WHERE id = ? 
            RETURNING id, name
        ", [
            $request->name,
            $id
        ]);

        // return JSON
        if ($updateTag) {
            return response()->json([
                'success' => true,
                'message' => 'Data successfully updated',
                'data' => $updateTag
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data not found'
        ], 404);
    }

    public function show(Request $request, string $id)
    {
        $tag = DB::selectOne(
            "SELECT id, name FROM tags WHERE id = ? LIMIT 1",
            [$id]
        );

        if ($tag) {
            return response()->json([
                'success' => true,
                'data' => $tag
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data not found'
        ], 404);
    }

    public function destroy(Request $request, string $id)
    {
        $data = DB::delete("DELETE FROM tags WHERE id = ?", [$id]);

        if ($data === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data successfully deleted'
        ], 200);
    }
}
