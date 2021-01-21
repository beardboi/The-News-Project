@extends('layouts.app')
@section('content')
    <div class="container">
        <h3 align="center">Plataforma de noticias</h3>
        <br />
        <div class="row">
            <div class="col-md-12">
                @if(count($news) != 0)
                    <div class="card border-secondary">
                        <div class="card-header">
                            News
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tab_trabajo">
                                    <tr align="center">
                                        <th>
                                            title
                                        </th>
                                        <th>
                                            author
                                        </th>
                                        <th>
                                            source
                                        </th>
                                        <th>
                                            url
                                        </th>
                                        <th>
                                            url_image
                                        </th>
                                        <th>
                                            description
                                        </th>
                                        <th>
                                            content
                                        </th>
                                        <th >
                                            date
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                    <tbody>
                                    @foreach($news as $new)
                                        <tr id='atenciones'>
                                            <td align="center">
                                                {{$new->title}}
                                            </td>
                                            <td align="center">
                                                {{$new->author}}
                                            </td>
                                            <td align="center">
                                                {{$new->source}}
                                            </td>
                                            <td align="center">
                                                {{$new->url}}
                                            </td>
                                            <td align="center">
                                                {{$new->url_image}}
                                            </td>
                                            <td align="center">
                                                {{$new->description}}
                                            </td>
                                            <td align="center">
                                                {{$new->content}}
                                            </td>
                                            <td align="center">
                                                {{$new->published_at}}
                                            </td>
                                            <td align="center">
                                                <a href="{{route('news.destroy', $new->id)}}" onclick="return confirm('Estas seguro que desea eliminar')">
                                                    <input type="submit" name="upload" class="delete btn btn-danger btn-sm" value="Eliminar" >
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                @else
                    <div class="card border-secondary">
                        <div class="card-header">
                            News
                        </div>
                        <div class="card-body">
                            <h6 align="center"> There is no recorded news </h6>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
