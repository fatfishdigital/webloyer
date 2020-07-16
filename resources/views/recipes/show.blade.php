@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <table class="table">
                <tbody>
                    <tr>
                        <th>Name</th>
                        <td>{{ $recipe->name() }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $recipe->description() }}</td>
                    </tr>
                    <tr>
                        <th>Body</th>
                        <td><pre><code>{{ $recipe->body() }}</code></pre></td>
                    </tr>
                    @if ($recipe->afferentProjectsCount() === 0)
                        <tr>
                            <th>Used By</th>
                            <td></td>
                        </tr>
                    @else
                        @foreach ($afferentProjects as $i => $afferentProject)
                            <tr>
                                @if ($i === 0)
                                    <th rowspan="{{ count($afferentProjects) }}">Used By</th>
                                @endif
                                <td>{!! link_to_route('projects.show', $afferentProject->name(), $afferentProject->projectId()->id()) !!}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            {!! link_to_route('recipes.index', 'Back', [], ['class' => 'btn btn-danger']) !!}
            @if (Auth::user()->can('update.recipe'))
                {!! link_to_route('recipes.edit', 'Edit', [$recipe->recipeId()->id()], ['class' => 'btn btn-primary']) !!}
            @endif
        </div>
    </div>
</div>
@stop