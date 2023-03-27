<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class ArticleController extends Controller
{
    public function show(Request $request)
    {
        $source = $request->source;
        switch ($source) {
            case 'newsapi':
                $url = 'https://newsapi.org/v2/everything?q=bitcoin&apiKey=20b8c2f029f44cf0a86b81c944a62aa6';
                break;
            case 'nytimes':
                $url = 'https://api.nytimes.com/svc/mostpopular/v2/emailed/7.json?api-key=R9xkdNTfQtTP65ABd1qH49jGH643ATr1';
                break;
            case 'guardian':
                $url = 'https://content.guardianapis.com/search?api-key=add8186b-aa70-4379-9011-b7bb6d4a3db0';
                break;
            default:
                $url = 'https://newsapi.org/v2/everything?q=bitcoin&apiKey=20b8c2f029f44cf0a86b81c944a62aa6';
                break;
        }

        try {
            $response = Http::get($url);
            $data = $response->json();
            if (isset($data['articles'])) {
                return response()->json($data['articles'], 200);
            } else if (isset($data['results'])) {
                return response()->json($data['results'], 200);
            } else if (isset($data['response']['results'])) {
                return response()->json($data['response']['results'], 200);
            }
        } catch (\Exception $e) {
            // Handle the error gracefully and return an appropriate response to the user.
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
