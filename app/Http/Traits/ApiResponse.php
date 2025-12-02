<?php

namespace App\Http\Traits;

trait ApiResponse
{
    protected function paginate($data)
    {
        return response()->json([
            'success' => true,
            'page' => $data->currentPage(),
            'of' => $data->lastPage(),
            'data' => $data->items(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
            'next_page_url' => $data->nextPageUrl(),
            'prev_page_url' => $data->previousPageUrl(),
        ]);
    }
}

