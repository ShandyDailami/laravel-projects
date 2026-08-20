<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NoteTagsController extends Controller
{
    public function getNotesByTag(Request $request, string $tagId)
    {
        // check tag if exists
        $tag = DB::selectOne("SELECT id, name FROM tags WHERE id = ?", [$tagId]);
        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found'
            ], 404);
        }

        $notes = DB::select("
            SELECT n.id, n.title, n.content, n.is_pinned, n.created_at, n.updated_at
            FROM notes n
            INNER JOIN note_tags nt ON n.id = nt.note_id
            WHERE nt.tag_id = ? AND n.user_id = ?
            ORDER BY n.is_pinned DESC, n.created_at DESC
        ", [$tagId, auth('api')->id()]);

        return response()->json([
            'success' => true,
            'data' => [
                'tag' => $tag,
                'notes' => $notes
            ]
        ], 200);
    }

    public function attachTag(Request $request, string $noteId)
    {
        // set validator
        $validator = Validator::make($request->all(), [
            'tag_id' => [
                'required',
                'uuid',
                Rule::exists('tags', 'id')
            ]
        ]);

        // if validator fails
        if ($validator->fails()) {
            return response()->json([
                $validator->errors()
            ], 422);
        }

        // check ownership note
        $note = DB::selectOne("SELECT id FROM notes WHERE id = ? AND user_id = ?", [$noteId, auth('api')->id()]);
        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found'
            ], 404);
        }

        // query insert to note_tags
        DB::insert("
            INSERT INTO note_tags (note_id, tag_id, created_at)
            VALUES (?, ?, NOW())
        ", [$noteId, $request->tag_id]);

        return response()->json([
            'success' => true,
            'message' => 'Data successfully created'
        ], 201);
    }

    public function dettachTag(Request $request, string $noteId, string $tagId)
    {
        // check data if exists
        $deleted = DB::delete("
            DELETE FROM note_tags nt
            USING notes n, tags t
            WHERE nt.note_id = n.id
                AND nt.tag_id = t.id
                AND nt.note_id = ?
                AND nt.tag_id = ?
                AND n.user_id = ?
        ", [$noteId, $tagId, auth('api')->id()]);

        if (!$deleted) {
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

    public function getNotesWithTags(string $noteId)
    {
        $tags = DB::select("
            SELECT t.id, t.name
            FROM tags t
            INNER JOIN note_tags nt on t.id = nt.tag_id
            WHERE nt.note_id = ?
        ", [$noteId]);

        if (!$tags) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found'
            ], 404);
        }

        $note = DB::select("
            SELECT n.id, n.title, n.content, n.is_pinned, n.created_at, n.updated_at
            FROM notes n
            INNER JOIN note_tags nt ON n.id = nt.note_id
            WHERE n.user_id = ? AND nt.note_id = ?
            ORDER BY n.is_pinned DESC, n.created_at DESC
            LIMIT 1
        ", [auth('api')->id(), $noteId]);

        return response()->json([
            'success' => true,
            'data' => [
                'tags' => $tags,
                'note' => $note
            ]
        ], 200);
    }
    public function syncTags(Request $request, string $noteId)
    {
        $validator = Validator::make($request->all(), [
            'tag_ids' => 'required|array',
            'tag_ids.*' => [
                'uuid',
                Rule::exists('tags', 'id')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $note = DB::selectOne("SELECT id FROM notes WHERE id = ? AND user_id = ? LIMIT 1", [$noteId, auth('api')->id()]);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found'
            ], 404);
        }

        DB::transaction(function () use ($noteId, $request) {
            DB::delete("DELETE FROM note_tags WHERE note_id = ?", [$noteId]);

            if (!empty($request->tag_ids)) {
                foreach (array_unique($request->tag_ids) as $tagId) {
                    DB::insert("
                        INSERT INTO note_tags (note_id, tag_id, created_at)
                        VALUES (?, ?, NOW())
                    ", [$noteId, $tagId]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Tags synced successfully'
        ], 200);
    }
}
