<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'score',
        'last_added_score'
    ];
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateCourseScore(int $score): void
    {
        $this->score += $score;
        $this->last_added_score = $score;
        $this->save();
    }

    public static function createCourseScore(int $score, int $courseId, int $userId) {
        $courseScore = new CourseScore();
        $courseScore->course_id = $courseId;
        $courseScore->user_id = $userId;
        $courseScore->last_added_score = $score;
        $courseScore->score = $score;
        $courseScore->save();
    }


}
