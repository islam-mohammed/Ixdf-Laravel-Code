<?php
/**
 * @var \App\Models\CourseEnrollment $enrollment
 */
?>
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h2 class="card-header">Lessons</h2>
                <div class="card-body">
                    <ol>
                        @foreach($enrollment->course->lessons as $lesson)
                        <li>
                            <a
                                href="{{ route('lessons.show', ['slug' => $enrollment->course->slug, 'number' => $lesson->number]) }}">
                                {{ $lesson->title }}
                            </a>
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
            <course-statistics :score-statistics="{{ json_encode($scoreStatistics) }}"></course-statistics>
        </div>
    </div>
</div>
@endsection
