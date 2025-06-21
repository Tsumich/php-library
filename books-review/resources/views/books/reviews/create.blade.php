@extends('layout.app')

@section('content')
    <h1 class="mb-10 text-2x1"> 
        Добавить рецензию на {{$book->title}}
    </h1>   
@php
        @endphp
    <form method="POST" action='{{route('books.reviews.store', $book)}}'>
        @csrf
        <label for='review'>Рецензия</label>
        <textarea name='review' id="review" required class="input mb-4"></textarea>

        <label for='review'>Оценка</label>
        <select name="rating" id="rating" class="input mb-4" required>
            <option value="">Выберите оценку</option>
            @for ($i = 1; $i <= 5; $i++)
             <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>

        <button type="submit" class="btn">Добавить</button>
    </form>
@endsection