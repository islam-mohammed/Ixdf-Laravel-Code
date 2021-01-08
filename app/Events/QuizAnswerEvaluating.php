<?php declare(strict_types=1);

namespace App\Events;

use App\Models\GraderInterface;
use App\Models\QuizAnswer;
use Illuminate\Queue\SerializesModels;

final class QuizAnswerEvaluating
{
    use SerializesModels;

    public QuizAnswer $quizAnswer;

    public int $score;

    public GraderInterface $grader;

    public function __construct(QuizAnswer $quizAnswer, int $score, GraderInterface $grader)
    {
        $this->quizAnswer = $quizAnswer;
        $this->score = $score;
        $this->grader = $grader;
    }
}
