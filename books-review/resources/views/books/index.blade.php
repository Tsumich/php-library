@extends('layout.app')


@section('content')
    <h1></h1>
    <form method="GET" action='{{route('books.index')}}' class="mb-4 flex items-center space-x-2">
        <input type="text" name="title" placeholder="Поиск по названию книги"
            value='{{ request('title') }}' class="input h-10"/>
        <input type="hidden" name='filter' value="{{request('filter')}}"/>
        <button type="submit" class="btn h-10">Поиск</button>
        <a href="{{ route('books.index')}}" class="btn h-10">Очистить</a>
    </form>

    <div class="filter-container mb-4 flex">
        @php
            $filters = [
                '' => 'Последние',
                'popular_last_month' => 'Популярное за месяц',
                'popular_last_6_month' => 'Популярное за 6 месяцев',
                'popular_rated_last_month' => 'Лучшее за месяц',
                'popular_rated_last_6_month' => 'Лучшее за 6 месяцев'
            ]
        @endphp

        @foreach($filters as $key => $label)
            <a href='{{ route('books.index', [...request()->query(), 'filter' => $key]) }}'
                class="{{ request('filter') === $key || request('filter') === null && $key === '' ? 'filter-item-active' : 'filter-item'}}">
                {{$label}}
            </a>
        @endforeach
    </div>

    <ul>
        @forelse($books as $book)
        <li class="mb-4">
            <div class="book-item">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="w-full flex-grow sm:w-auto">
                        <a href='{{ route('books.show', $book) }}' class="book-title">{{ $book->title }}</a>
                        <span class="book-author">{{ $book->author }}</span>
                    </div>
                    <div>
                        <div class="book-rating flex gap-2">
                            {{number_format($book->reviews_avg_rating, 1) }}
                            <x-star-rating :rating='$book->reviews_avg_rating'/>
                        </div>
                        <div class="book-review-count">
                            out of {{ $book->reviews_count }} {{Str::plural('review',  $book->reviews_count)}}
                        </div>
                    </div>
                </div>
            </div>
        </li>
        @empty
            <li class="mb-4">
                <div class="empty-book-item">
                    <p class="empty-book-item">Книги не найдены</p>
                    <a href="{{ route('books.index')}}" class="reset-link">Попробовать снова</a>
                </div>
            </li>
        @endforelse
    </ul>
@endsection