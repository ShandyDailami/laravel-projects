<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        // check if URL params if exists
        if ($search) {
            $searchTerm = "%{$search}%";
            $notes = DB::select("
                SELECT id, title, content, is_pinned, created_at, updated_at
                FROM notes
                WHERE user_id = ?
                    AND (title ILIKE ? OR content ILIKE ?)
                ORDER BY is_pinned DESC, created_at DESC
            ", [
                auth('api')->id(),
                $searchTerm,
                $searchTerm
            ]);
        } else {
            $notes = DB::select("
                SELECT id, title, content, is_pinned, created_at, updated_at
                FROM notes
                WHERE user_id = ?
                ORDER BY is_pinned DESC, created_at DESC
            ", [auth('api')->id()]);
        }
        return response()->json([
            'success' => true,
            'data' => $notes
        ], 200);
    }


    public function store(Request $request)
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'is_pinned' => 'nullable|boolean'
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create note
        $note = DB::selectOne('
            INSERT INTO notes(user_id, title, content, is_pinned, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
            RETURNING id, title, content, is_pinned, created_at, updated_at
        ', [
            auth('api')->id(),
            $request->title,
            $request->content,
            $request->boolean('is_pinned', false)
        ]);

        // return response JSON note is created
        if ($note) {
            return response()->json([
                'success' => true,
                'data' => $note
            ], 201);
        }

        // return JSON process insert failed
        return response()->json([
            'success' => false
        ], 500);
    }

    public function update(Request $request, string $id)
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'is_pinned' => 'nullable|boolean'
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // update data
        $updateNote = DB::selectOne("
            UPDATE notes
            SET
                title = ?,
                content = ?,
                is_pinned = ?,
                updated_at = NOW()
            WHERE id = ? AND user_id = ?
            RETURNING id, title, content, is_pinned, updated_at
        ", [
            $request->title,
            $request->content,
            $request->boolean('is_pinned', false),
            $id, // ID dari URL parameter
            auth('api')->id()
        ]);

        // check id
        if (!$updateNote) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully update data',
            'data' => $updateNote
        ]);
    }

    public function show(Request $request, string $id)
    {
        $note = DB::selectOne("
            SELECT id, user_id, title, content, is_pinned, created_at, updated_at
            FROM notes
            WHERE id = ? AND user_id = ?
            LIMIT 1
        ", [
            $id,
            auth('api')->id()
        ]);

        // if not exists
        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }

        // data exists
        return response()->json([
            'success' => true,
            'data' => $note
        ], 200);
    }

    public function destroy(Request $request, string $id)
    {
        $deleted = DB::delete("
            DELETE FROM notes
            WHERE id = ? AND user_id = ?
        ", [
            $id,
            auth('api')->id()
        ]);

        // check if not exists
        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }

        // delete data
        return response()->json([
            'success' => true,
            'message' => 'Data successfully deleted'
        ], 200);
    }
}
