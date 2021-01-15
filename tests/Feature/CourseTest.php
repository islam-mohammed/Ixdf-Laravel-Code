<?php

namespace Tests\Feature;

use App\Models\CourseScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CourseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShouldIncreaseTheUseScore()
    {
        // get the a user
        $user = User::first();

        $gradedBy = User::find(100);

        // Authenticate the user
        Auth::login($user);

        //get a user course
        $course = $user->courseEnrollments()->first()->course;

        $increaseBy = 5;

        $previousScore =  CourseScore::where('course_id', $course->id)->where('user_id',  $user->id)->first()->score;

        $expected = $increaseBy +  $previousScore;

        $quizAnswer = $course->quizzes()->first()->answers()->first();

        $quizAnswer->grade($increaseBy, $gradedBy);

        $newScore =  CourseScore::where('course_id', $course->id)->where('user_id',  $user->id)->first()->score;

        $this->assertSame($expected, $newScore);

    }
}
