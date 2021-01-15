<?php

namespace App\Listeners;

use App\Events\QuizAnswerEvaluated;
use App\Http\Repositories\CourseRepository;
use App\Models\CourseScore;


class UpdateCourseScoreNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QuizAnswerEvaluated  $event
     * @return void
     */
    public function handle(QuizAnswerEvaluated $event)
    {
        // Get the course score object.
        $courseScore = CourseScore::query()->where('course_id', $event->courseId)->where('user_id',  $event->quizAnswer->user_id)->first();
        // if course score exists, then increase the scores and update
        // the last updated score otherwise we should create a new one.
        if ($courseScore) {
            $courseScore->updateCourseScore($event->score);
        } else {
            CourseScore::createCourseScore($event->score, $event->courseId, $event->quizAnswer->user_id);
        }
    }
}
