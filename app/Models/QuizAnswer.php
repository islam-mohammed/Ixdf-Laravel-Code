<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\QuizAnswerEvaluated;
use App\Events\QuizAnswerEvaluating;
use ErrorException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $user_id
 * @property int $quiz_id
 * @property string $answer
 * @property string $score
 *
 * @property Quiz $quiz
 */
final class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'answer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grade(int $score, GraderInterface $gradedBy): void
    {
        if ($score > $this->quiz->max_score) {
            throw new \OutOfBoundsException("Score can not be higher that maximum for this quiz (max={$this->quiz->max_score})");
        }

        try {
            // start new transaction to maintain data integrity.
            DB::beginTransaction();
            event(new QuizAnswerEvaluating($this, $score, $gradedBy));

            // Get the course id
            $courseId = $this->quiz->lesson->course->id;
            // Get the course score object.
            $courseScore = CourseScore::query()->where('course_id', $courseId)->where('user_id', $this->user_id)->first();
            // if course score exists, then increase the scores and update
            // the last updated score otherwise we should create a new one.
            if ($courseScore) {
                $courseScore->score += $score;
                $courseScore->last_added_score = $score;
            } else {
                $courseScore = new CourseScore();
                $courseScore->course_id = $courseId;
                $courseScore->user_id = $this->user_id;
                $courseScore->last_added_score = $score;
                $courseScore->score = $score;
            }
            // save the course score.
            $courseScore->save();

            $this->score = $score;
            $this->save();

            event(new QuizAnswerEvaluated($this, $score, $gradedBy));
            // save the transaction
            DB::commit();
        } catch (\Throwable $th) {
            // rollback the transaction on error
            DB::rollBack();
            // throw the error
            throw new ErrorException($th);
        }
    }
}
