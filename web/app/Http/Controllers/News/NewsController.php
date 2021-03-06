<?php
/*
 * Copyright 2020 Diego Bravo, diego.bravo@alumnos.ucn.cl
 *                Daniel Suares, daniel.suares@alumnos.ucn.cl
 *                Raul Ramos, raul.ramos@alumnos.ucn.cl
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom
 * the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED INCLUDING
 * BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Http\Controllers\News;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\News;
use Illuminate\Validation\ValidationException;

/**
 * The Controller of the News model.
 * @package App\Http\Controllers\News
 * @author Diego Bravo Bascuñan, Daniel Suares
 */
class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // Get the News order by the published_At and then paginate
        $news = News::all();

        // Return the GET request with code 200 (By default is 200)
        return response()->json($news,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        // Return the view with the form.
        return view('news.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // The validation rules
        $rules = [
            'title' => 'string | required | max:255',
            'author' => 'string | required | max:255',
            'source' => 'string | required | max:255',
            'url' => 'string | required | min:3',
            'url_image' => 'string | required | min:3',
            'description' => 'string | required',
            'content' => 'string | required'
        ];

        // The error messages
        $messages = [
            'title.required' => 'The field title must be filled',
            'author.required' => 'The field author must be filled',
            'source.required' => 'The field source must be filled',
            'url.required' => 'The field url must be filled',
            'url_image.required' => 'The field url image must be filled',
            'description.required' => 'The field description must be filled',
            'content.required' => 'The field content must be filled',
        ];

        // Validate
        $this->validate($request, $rules, $messages);

        // After the validation, create a new instances of News
        $news = new News();

        // The date
        $published_at = Carbon::now();

        // Fill with the request attributes and then insert it into the database
        $news->fill([
            'title' => $request['title'],
            'author' => $request['author'],
            'source' => $request['source'],
            'url' => $request['url'],
            'url_image' => $request['url_image'],
            'description' => $request['description'],
            'content' => $request['content'],
            'published_at' => $published_at
        ]);

        $news->save();

        // Redirect to the form again
        return back()->with('info', 'Se registró correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $allNews = News::all()->where('id','=',$id);

        $allNews = News::all();

        foreach ($allNews as $news)
        {
            $new = $news;
        }
        return view('news.show', compact('new'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //Search the news
        $data = News::find($id);

        //Delete the news
        $data->delete();

        //Return page
        return back();
    }

    /**
     * Function that scrolls all the news
     *
     * @return Application|Factory|View
     */
    public function tableNews()
    {

        //Get all the news
        $news = News::all();

        //return view table
        return view('news.table')->with('news',$news);

    }

    /**
     * Function that returns all the news
     *
     * @return Application|Factory|View
     */
    public function watchNews()
    {
        //Get all the news
        $news = News::all();

        //return view
        return view('news.watch')->with('news',$news);
    }

    /**
     * Search title
     *
     * @param $content
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($content)
    {
        //title finder
        $news = News::where('title','like',"%{$content}%")->get();

        //return json
        return response()->json(['title' => $news]);

    }

    /**
     * Pagination and size
     *
     * @param Request $request
     * @return Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function newsResource(Request $request)
    {
        // Default value for pagSize
        $defaultPageSize = 20;

        // Min and max for pageSize
        $minPageSize = 1;
        $maxPageSize = 100;

        // By default the keyword is null
        $defaultQ = null;

        // The columns attributes for the keyword 'q'
        $columns = ['title', 'author', 'source', 'content', 'description'];

        // Get the url
        $url = url()->full();

        // The tags that can be added to the API request
        $pageSize = $request->input('pageSize', $defaultPageSize);
        $page = $request->input('page', 1);
        $q = $request->input('q', $defaultQ);

        /*
         * The pagSize cannot be minor than 20 or bigger than 100, so in that 2 cases
         * the response has to be an error along with is code. The other case is that
         * the pagSize pass by parameter is not a number, so in that case its the same.
         */
        if (is_numeric($pageSize))
        {
            // pagSize cant be a negative number
            // TODO: Fix this mess
            if($pageSize < $minPageSize)
            {
                return response([
                    'error' => 404,
                    'message' => 'data not found'
                ]);
            }
            elseif ($pageSize > $maxPageSize)
            {
                return response([
                    'error' => 404,
                    'message' => 'data not found'
                ]);
            }
        } else
        {
            return response([
                'error' => 404,
                'message' => 'data not found'
            ]);
        }

        // If the request doesnt contain a keyword..
        if ($q == $defaultQ)
        {
            return News::orderBy('published_at', 'DESC')->simplePaginate($pageSize);
        }

    }

    public function jsonLink()
    {
        return view('news.json');
    }

}
